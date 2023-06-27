import {
  EthereumClient,
  w3mConnectors,
  w3mProvider,
  WagmiCore,
  WagmiCoreChains,
  WagmiCoreConnectors,
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

web3modal.subscribeModal((newState) => {
  console.log(newState);
  //   console.log(wagmiConfig.store.getStore());
});

web3modal.subscribeEvents((newState) => {
  const { name } = newState;
  if (name === "ACCOUNT_CONNECTED") {
    switchNetwork({
      chainId: 80001,
    })
      .then((chain) => {
        console.log(chain);
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
  }
  console.log(newState);
});

window.prepareWriteContract = prepareWriteContract;
window.writeContract = writeContract;
window.readContract = readContract;
