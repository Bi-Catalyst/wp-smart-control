import {
  EthereumClient,
  w3mConnectors,
  w3mProvider,
  WagmiCore,
  WagmiCoreChains,
  // WagmiCoreConnectors
  // alchemyProvider,
  // infuraProvider
} from "https://unpkg.com/@web3modal/ethereum";

import { Web3Modal } from "https://unpkg.com/@web3modal/html";

const projectId = "REDACTED_WALLETCONNECT_ID";

const { mainnet, polygon, polygonMumbai, avalanche, arbitrum } =
  WagmiCoreChains;

const {
  configureChains,
  createConfig,
  prepareWriteContract,
  writeContract,
  readContract,
  switchNetwork,
  getNetwork,
} = WagmiCore;

// 1. Define chains
const chains = [mainnet, polygon, polygonMumbai, avalanche, arbitrum];

const { publicClient } = configureChains(chains, [
  // alchemyProvider({ apiKey: "REDACTED_ALCHEMY_KEY" }),
  // infuraProvider({ apiKey: "REDACTED_INFURA_KEY" }),
  w3mProvider({ projectId }),
]);

const wagmiConfig = createConfig({
  autoConnect: true,
  connectors: w3mConnectors({ projectId, chains }),
  publicClient,
});

const ethereumClient = new EthereumClient(wagmiConfig, chains);

const web3modal = new Web3Modal(
  { projectId, walletConnectVersion: 2 },
  ethereumClient
);

window.mint = async function mint(quantity) {
  const weiAmount = ethers.parseUnits(
    (quantity * Number.parseFloat(myMintPluginSettings.mintPrice)).toString(),
    "ether"
  );
  const { request } = await prepareWriteContract({
    address: myMintPluginSettings.contractAddress,
    abi: myMintPluginSettings.contractABI,
    functionName: "mint",
    args: [quantity],
    gas: 3000000n,
    value: weiAmount,
  });
  const { hash } = await writeContract(request);
};

function checkIsMint() {
  if (
    window.localStorage.getItem("TIGGER_MINT") !== null ||
    window.localStorage.getItem("TIGGER_MINT") === "true"
  ) {
    window.localStorage.getItem("TIGGER_MINT") === "false";
    let quantity = parseInt(
      $(myMintPluginSettings.mintQuantityIdOrClass).val()
    );

    // Set quantity to 1 if it's not a valid number or less than or equal to zero
    if (isNaN(quantity) || quantity <= 0) {
      quantity = 1;
    }
    mint(quantity);
  }
}

web3modal.subscribeModal((newState) => {
  const { open } = newState;
  if (
    open === false &&
    window.localStorage.getItem("wagmi.connected") === null
  ) {
    window.localStorage.setItem("TIGGER_MINT", false);
  }
  // check if modal close and window.localStorage.getItem("wagmi.connected") === "false"
  // console.log(wagmiConfig.store.getStore());
});

web3modal.subscribeEvents((newState) => {
  const { name } = newState;
  console.log(myMintPluginSettings.activeChain);
  const { chain } = getNetwork();
  if (
    name === "ACCOUNT_CONNECTED" &&
    chain.id !== Number.parseInt(myMintPluginSettings.activeChain)
  ) {
    switchNetwork({
      chainId: 80001,
    })
      .then((chain) => {
        console.log(chain);
        checkIsMint();
      })
      .catch((e) => {
        console.log(e);
        // try {
        //   publicClient
        //     .request({
        //       method: "wallet_addEthereumChain",
        //       params: [mumbai],
        //     })
        //     .catch((e) => console.log(e))
        //     .then((v) => console.log(v));
        // } catch (addError) {
        //   console.error(addError);
        // }
      });
  } else if (name === "ACCOUNT_CONNECTED") {
    checkIsMint();
  }
  console.log(newState);
});

window.web3modal = web3modal;
window.prepareWriteContract = prepareWriteContract;
window.writeContract = writeContract;
window.readContract = readContract;
