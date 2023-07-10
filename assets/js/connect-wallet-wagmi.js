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

const { polygon, polygonMumbai } = WagmiCoreChains;

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
const chains = [polygon, polygonMumbai];

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
  try {
    const { chain } = getNetwork();
    if (chain.id !== Number.parseInt(myMintPluginSettings.activeChain)) {
      for (const _chain of chains) {
        if (_chain.id === Number.parseInt(myMintPluginSettings.activeChain)) {
          showPopup(
            "error",
            "Please switch to active chain ".concat(" ", _chain.name)
          );
          break;
        }
      }
      return;
    }
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

    var explorerURL = "";
    if (myMintPluginSettings.activeChain === "80001") {
      explorerURL = "https://mumbai.polygonscan.com/tx/" + hash;
    } else if (myMintPluginSettings.activeChain === "137") {
      explorerURL = "https://polygonscan.com/tx/" + hash;
    }

    if (explorerURL) {
      var popupHTML = `
      <div class="popup-content">
        <p>Transaction submitted successfully. Check it <a href="${explorerURL}" target="_blank">here</a>.</p>
      </div>
      `;
      showPopup("success", popupHTML);
    }
  } catch (error) {
    showPopup("error", error.shortMessage ? error.shortMessage : error.message);
  }
};

function triggerMint() {
  if (
    window.localStorage.getItem("TRIGGER_MINT") !== null ||
    window.localStorage.getItem("TRIGGER_MINT") === "true"
  ) {
    window.localStorage.getItem("TRIGGER_MINT") === "false";
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
    window.localStorage.setItem("TRIGGER_MINT", false);
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
        triggerMint();
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
    triggerMint();
  }
  console.log(newState);
});

window.web3modal = web3modal;
window.prepareWriteContract = prepareWriteContract;
window.writeContract = writeContract;
window.readContract = readContract;

// Automatically hide the popup after 3 seconds
$(document).ready(function () {
  setTimeout(() => {
    const desktopBtn = document
      .querySelector("#header-btn-col > div > div > w3m-core-button")
      .shadowRoot.querySelector("w3m-connect-button")
      .shadowRoot.querySelector("w3m-button-big")
      .shadowRoot.querySelector("button");
    if (desktopBtn) {
      $(desktopBtn).addClass("w3m-custom-btn");
    }
    const mobileBtn = document
      .querySelector("#mobile_menu1 > li.cstm-m-cnct-wlt > a > w3m-core-button")
      .shadowRoot.querySelector("w3m-connect-button")
      .shadowRoot.querySelector("w3m-button-big")
      .shadowRoot.querySelector("button");
    if (mobileBtn) {
      $(mobileBtn).addClass("w3m-custom-btn-mobile");
      $(mobileBtn).css("height", "60px");
    }
  }, 2000);
});
