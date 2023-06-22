import {
  EthereumClient,
  w3mConnectors,
  w3mProvider,
  WagmiCore,
  WagmiCoreChains,
  WagmiCoreConnectors,
} from "https://unpkg.com/@web3modal/ethereum";

// import UniversalProvider from "https://unpkg.com/@walletconnect/universal-provider";

import { Web3Modal } from "https://unpkg.com/@web3modal/html";
// import * as viem from "https://unpkg.com/viem"; // Import the entire 'viem' module

// import { prepareWriteContract, writeContract } from "https://unpkg.com/@wagmi/core";
// 0. Import wagmi dependencies
const { mainnet, polygon, avalanche, arbitrum } = WagmiCoreChains;
const { configureChains, createConfig, prepareWriteContract, writeContract } =
  WagmiCore;

// 1. Define chains
const chains = [mainnet, polygon, avalanche, arbitrum];

const projectId = "REDACTED_WALLETCONNECT_ID";

const { publicClient } = configureChains(chains, [w3mProvider({ projectId })]);
const wagmiConfig = createConfig({
  autoConnect: true,
  connectors: w3mConnectors({ projectId, version: 1, chains }),
  publicClient,
});
const ethereumClient = new EthereumClient(wagmiConfig, chains);
const web3modal = new Web3Modal({ projectId }, ethereumClient);
web3modal.subscribeModal((newState) => {
  const { data } = newState;
  console.log(newState);
});

window.mint = async function mint(quantity) {
  const weiAmount = ethers.parseUnits(
    (quantity * Number.parseFloat(myMintPluginSettings.mintPrice)).toString(),
    "gwei"
  );
  const { request } = await prepareWriteContract({
    address: myMintPluginSettings.contractAddress,
    abi: myMintPluginSettings.contractABI,
    functionName: "mint",
    args: [1],
    gas: 3000000n,
    value: 3000000n,
  });
  const { hash } = await writeContract(request);
};
// etherPovider = new ethers.BrowserProvider(ethereumClient.connectWalletConnectV2());

// const { request } = await prepareWriteContract({
//   address: myMintPluginSettings.contractAddress,
//   abi: myMintPluginSettings.contractABI,
//   functionName: "mint",
// });
//   const { hash } = await writeContract(request)

// const provider = await UniversalProvider.init({
//     projectId: DEFAULT_PROJECT_ID,
//     logger: DEFAULT_LOGGER,
//     relayUrl: DEFAULT_RELAY_URL,
//   });

// const chainId = caipChainId.split(":").pop();

// console.log("Enabling EthereumProvider for chainId: ", chainId);

// const session = await ethereumProvider.connect({
//   namespaces: {
//     eip155: {
//       methods: [
//         "eth_sendTransaction",
//         "eth_signTransaction",
//         "eth_sign",
//         "personal_sign",
//         "eth_signTypedData",
//       ],
//       chains: [`eip155:${chainId}`],
//       events: ["chainChanged", "accountsChanged"],
//       rpcMap: {chainId: `https://rpc.walletconnect.com?chainId=eip155:${chainId}&projectId=${DEFAULT_PROJECT_ID}`,},
//     },
//   },
//   pairingTopic: pairing?.topic,
// });
