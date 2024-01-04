import {
  EthereumClient,
  w3mConnectors,
  w3mProvider,
  WagmiCore,
  WagmiCoreChains,
  WagmiCoreProviders,
  // WagmiCoreConnectors
  // alchemyProvider,
  // infuraProvider
} from "https://unpkg.com/@web3modal/ethereum";

import { Web3Modal as WagmiWeb3Modal } from "https://unpkg.com/@web3modal/html";

try {
  window.localStorage.setItem("TRIGGER_MINT", false);
  window.process = {
    env: {
      NODE_ENV: "production",
    },
  };
  const projectId = "REDACTED_WALLETCONNECT_ID";

  const { polygon, polygonMumbai } = WagmiCoreChains;
  const { publicProvider, jsonRpcProvider, alchemyProvider, infuraProvider } =
    WagmiCoreProviders;

  const {
    configureChains,
    createConfig,
    prepareWriteContract: prepareWriteContract,
    writeContract,
    readContract,
    switchNetwork,
    getNetwork,
    getAccount,
    fetchBalance,
  } = WagmiCore;

  window.wagmiWeb3Modal = WagmiWeb3Modal;
  window.prepareWriteContract = prepareWriteContract;
  window.writeContract = writeContract;
  window.readContract = readContract;
  window.getNetwork = getNetwork;

  // 1. Define chains
  const chains = [polygonMumbai, polygon];

  const { publicClient } = configureChains(chains, [
    // publicProvider(),
    alchemyProvider({ apiKey: "REDACTED_ALCHEMY_KEY" }),
    // infuraProvider({ apiKey: "REDACTED_INFURA_KEY" }),
    w3mProvider({ projectId }),
  ]);

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

  // Set default chain
  web3modal.setDefaultChain(polygonMumbai);

  function triggerMint() {
    if (
      window.localStorage.getItem("TRIGGER_MINT") !== null ||
      window.localStorage.getItem("TRIGGER_MINT") === "true"
    ) {
      window.localStorage.getItem("TRIGGER_MINT") === "false";
      if ($(SCFlowPluginSettings.mintQuantityIdOrClass)) {
        let quantity = Number.parseInt(
          $(SCFlowPluginSettings.mintQuantityIdOrClass).val()
        );
        // Set quantity to 1 if it's not a valid number or less than or equal to zero
        if (isNaN(quantity) || quantity <= 0) {
          quantity = 1;
        }
        mint(quantity);
      }
    }
  }

  web3modal.subscribeModal((newState) => {
    const { open } = newState;
    if (
      open === false &&
      window.localStorage.getItem("wagmi.connected") === null
    ) {
      window.localStorage.setItem("TRIGGER_MINT", false);
    }
    // check if modal close and window.localStorage.getItem("wagmi.connected") === "false"
    // console.log(wagmiConfig.store.getStore());
  });

  web3modal.subscribeEvents((newState) => {
    const { name } = newState;
    console.log(SCFlowPluginSettings.activeChain);
    const { chain } = getNetwork();
    switch (name) {
      case "ACCOUNT_CONNECTED":
        {
          // Handle chain ID
          if (chain.id !== Number.parseInt(SCFlowPluginSettings.activeChain)) {
            switchNetwork({
              chainId: 80001,
            })
              .then((chain) => {
                triggerMint();
              })
              .catch((e) => {
                console.log(e);
              });
          }
          // Get Account
          const account = getAccount();
          // fetch balance
          fetchBalance({
            address: account.address,
          }).then((balance) => {
            console.log(balance);
          });
          // Check if mint event should be triggered
          if (window.localStorage.getItem("TRIGGER_MINT") === "true") {
            triggerMint();
          }
        }
        break;
      case "ACCOUNT_DISCONNECTED": {
        window.localStorage.setItem("WALLET_ADDRESS", "");
        window.localStorage.setItem("TRIGGER_MINT", false);
        // clean up
      }
      default:
        break;
    }

    console.log(newState);
  });

  // Automatically hide the popup after 3 seconds
  // jQuery(document).ready(function () {
  //   const desktopBtn = document.querySelector(
  //     "#header-btn-col > div > div > w3m-core-button"
  //   );
  //   if (!desktopBtn || typeof desktopBtn === "undefined") {
  //     return;
  //   }
  //   setTimeout(() => {
  //     try {
  //       const desktopBtn = document
  //         .querySelector("#header-btn-col > div > div > w3m-core-button")
  //         .shadowRoot.querySelector("w3m-connect-button")
  //         .shadowRoot.querySelector("w3m-button-big")
  //         .shadowRoot.querySelector("button");
  //       if (desktopBtn) {
  //         $(desktopBtn).addClass("w3m-custom-btn");
  //       }
  //       const mobileBtn = document
  //         .querySelector(
  //           "#mobile_menu1 > li.cstm-m-cnct-wlt > a > w3m-core-button"
  //         )
  //         .shadowRoot.querySelector("w3m-connect-button")
  //         .shadowRoot.querySelector("w3m-button-big")
  //         .shadowRoot.querySelector("button");
  //       if (mobileBtn) {
  //         $(mobileBtn).addClass("w3m-custom-btn-mobile");
  //         $(mobileBtn).css("height", "60px");
  //       }
  //     } catch (error) {
  //       console.log(error);
  //     }
  //   }, 2000);
  // });
} catch (error) {
  console.log(error);
}
