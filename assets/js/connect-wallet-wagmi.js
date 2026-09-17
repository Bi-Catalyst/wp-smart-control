import {
  EthereumClient,
  w3mConnectors,
  w3mProvider,
  WagmiCore,
  WagmiCoreChains,
  WagmiCoreProviders,
} from "https://unpkg.com/@web3modal/ethereum@2.7.1/dist/cdn/bundle.js";

import { Web3Modal as WagmiWeb3Modal } from "https://unpkg.com/@web3modal/html@2.7.1/dist/cdn/bundle.js";

const settings = window.SCFlowPluginSettings;

try {
  window.localStorage.setItem("TRIGGER_MINT", "false");
  window.process = { env: { NODE_ENV: "production" } };

  const projectId = settings.wcProjectId;
  const { polygon, polygonMumbai } = WagmiCoreChains;
  const { alchemyProvider } = WagmiCoreProviders;
  const {
    configureChains,
    createConfig,
    prepareWriteContract,
    writeContract,
    readContract,
    switchNetwork,
    getNetwork,
    getAccount,
  } = WagmiCore;

  window.prepareWriteContract = prepareWriteContract;
  window.writeContract = writeContract;
  window.readContract = readContract;
  window.getNetwork = getNetwork;

  const chains = [polygonMumbai, polygon];
  const providers = [w3mProvider({ projectId })];
  if (settings.alchemyProvider) {
    providers.unshift(alchemyProvider({ apiKey: settings.alchemyProvider }));
  }
  const { publicClient } = configureChains(chains, providers);

  const wagmiConfig = createConfig({
    autoConnect: true,
    connectors: w3mConnectors({ projectId, chains }),
    publicClient,
  });

  const ethereumClient = new EthereumClient(wagmiConfig, chains);
  const web3modal = new WagmiWeb3Modal(
    { projectId, walletConnectVersion: 2 },
    ethereumClient
  );
  window.web3modal = web3modal;

  function readQuantity() {
    const input = document.querySelector(settings.mintQuantityIdOrClass);
    const quantity = input ? Number.parseInt(input.value) : NaN;
    return isNaN(quantity) || quantity <= 0 ? 1 : quantity;
  }

  function showMintSuccess(hash) {
    const { chain } = getNetwork();
    showPopup(
      "success",
      `<div class="popup-content"><p>${settings.popup.sucessMint} <a href="${chain.blockExplorers.default.url}/tx/${hash}" target="_blank">here</a>.</p></div>`
    );
  }

  function triggerPendingMint() {
    if (window.localStorage.getItem("TRIGGER_MINT") !== "true") {
      return;
    }
    window.localStorage.setItem("TRIGGER_MINT", "false");
    if (!document.querySelector(settings.mintQuantityIdOrClass)) {
      return;
    }
    mint(readQuantity(), showMintSuccess);
  }

  function ensureActiveChain() {
    const { chain } = getNetwork();
    const activeChainId = Number.parseInt(settings.activeChain);
    if (chain && chain.id !== activeChainId) {
      switchNetwork({ chainId: activeChainId }).catch((error) =>
        console.error(error)
      );
    }
  }

  web3modal.subscribeModal(({ open }) => {
    if (!open && window.localStorage.getItem("wagmi.connected") === null) {
      window.localStorage.setItem("TRIGGER_MINT", "false");
    }
  });

  web3modal.subscribeEvents(({ name }) => {
    if (name === "ACCOUNT_CONNECTED") {
      ensureActiveChain();
      window.localStorage.setItem("WALLET_ADDRESS", getAccount().address);
      triggerPendingMint();
    }
    if (name === "ACCOUNT_DISCONNECTED") {
      window.localStorage.setItem("WALLET_ADDRESS", "");
      window.localStorage.setItem("TRIGGER_MINT", "false");
    }
  });

  if (wagmiConfig.storage["wagmi.connected"]) {
    window.localStorage.setItem("WALLET_ADDRESS", getAccount().address);
  }
} catch (error) {
  console.error(error);
}
