// const GAS_STATION = 'https://api.blocknative.com/gasprices/blockprices'       //  ETHEREUM
// const GAS_STATION = 'https://gasstation-mainnet.matic.network/v2'             //  POLYGON
const GAS_STATION = "https://gasstation-mumbai.matic.today/v2"; //  MUMBAI TESTNET

//  Sign up for a free API key from https://www.alchemy.com
const ALCHEMY_API_KEY = "REDACTED_ALCHEMY_KEY";

//  Sign up for an API key from https://www.infura.io
const INFURA_API_KEY = "REDACTED_INFURA_KEY";

const EXTRA_TIP_FOR_MINER = 0; //  gwei

const providerRpc = new ethers.JsonRpcProvider(
  "https://rpc.ankr.com/polygon_mumbai"
);
// Contract address and ABI

// Contract instance
let _contractInstance = null;

// Wallet address
let _address = null;

const walletInstanceToConfig = new Map();
const supportedWallets = ["metamask", "wallet-connect"];
// Mimbai Mainnet network
const polygonMainnet = {
  chainId: "0x89",
  rpcUrls: ["https://polygon-rpc.com/"],
  chainName: "Matic Mainnet",
  nativeCurrency: {
    name: "MATIC",
    symbol: "MATIC",
    decimals: 18,
  },
  blockExplorerUrls: ["https://polygonscan.com"],
};

// Mumbai testnet network
const mumbaiTestnet = {
  chainId: "0x13881",
  chainName: "Matic Mumbai",
  nativeCurrency: {
    name: "MATIC",
    symbol: "MATIC",
    decimals: 18,
  },
  rpcUrls: ["https://matic-mumbai.chainstacklabs.com"],
  blockExplorerUrls: ["https://mumbai.polygonscan.com/"],
};

async function getMaticPrice(currency) {
  try {
    const response = await fetch(
      `https://api.coingecko.com/api/v3/simple/price?ids=matic-network&vs_currencies=${currency}`
    );
    const data = await response.json();
    const maticPrice = data["matic-network"][currency];

    return maticPrice;
  } catch (error) {
    console.log("Error fetching Matic price:", error);
    return null;
  }
}
// Unpkg imports
const Web3Modal = window.Web3Modal.default;
const WalletConnectProvider = window.WalletConnectProvider.default;
// const EvmChains = window.EvmChains;
// const Fortmatic = window.Fortmatic;

// Web3modal instance
let web3Modal;

let etherPovider;
// Chosen wallet provider given by the dialog window
let provider;

/**
 * Setup the orchestra
 */
function init() {
  if (web3Modal) return;
  // Tell Web3modal what providers we have available.
  // Built-in web browser provider (only one can exist as a time)
  // like MetaMask, Brave or Opera is added automatically by Web3modal
  const providerOptions = {
    walletconnect: {
      package: WalletConnectProvider,
      options: {
        // Moh's test key - don't copy as your mileage may vary
        infuraId: "REDACTED_INFURA_KEY",
      },
    },
    coinbasewallet: {
      package: CoinbaseWalletSDK,
      options: {
        // Moh's test key - don't copy as your mileage may vary
        infuraId: "REDACTED_INFURA_KEY",
      },
    },
    // fortmatic: {
    //   package: Fortmatic,
    //   options: {
    //     // Moh's TESTNET api key
    //     key: "pk_test_391E26A3B43A3350"
    //   }
    // }
  };

  web3Modal = new Web3Modal({
    cacheProvider: true, // very important
    providerOptions, // required
  });
}
// ≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖
//  Create contract instance
// ≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖

function ContractInstance(signer) {
  // Fetch the contract address and contract ABI from plugin settings
  const contractAddress = myMintPluginSettings.contractAddress;
  const contractABI = myMintPluginSettings.contractABI;
  _contractInstance = new ethers.Contract(contractAddress, contractABI, signer);
  return _contractInstance;
}

function handleError(error) {
  if (typeof error.data !== "undefined") {
    alert("Transaction failed: " + error.data.message);
  } else {
    alert("Transaction failed: " + error.message);
  }
}
// ≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖
//  Implementation methods
// ≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖≖
async function getTransactionPropertiesViaGasStation() {
  console.log("GETTING EIP-1559 GAS FROM GAS STATION");

  const gasStationResponse = await fetch(GAS_STATION);
  const gasStationObj = JSON.parse(await gasStationResponse.text());

  let block_number = gasStationObj.blockNumber;
  let base_fee = parseFloat(gasStationObj.estimatedBaseFee);
  let max_priority_fee =
    gasStationObj.standard.maxPriorityFee + EXTRA_TIP_FOR_MINER;
  let max_fee_per_gas = base_fee + max_priority_fee;

  //  In case the network gets (up to 25%) more congested
  max_fee_per_gas += base_fee * 0.25;

  console.log(`block_number: ${block_number}`);
  console.log(`base_fee: ${base_fee.toFixed(9)} gwei`);
  console.log(`max_priority_fee_per_gas: ${max_priority_fee} gwei`);
  console.log(`max_fee_per_gas: ${max_fee_per_gas} gwei`);

  //  cast gwei numbers to wei BigNumbers for ethers
  const maxFeePerGas = ethers.parseUnits(max_fee_per_gas.toFixed(9), "gwei");
  const maxPriorityFeePerGas = ethers.parseUnits(
    max_priority_fee.toFixed(9),
    "gwei"
  );

  //  Final object ready to feed into a transaction
  const transactionProperties = {
    maxFeePerGas,
    maxPriorityFeePerGas,
  };

  return transactionProperties;
}

async function getTransactionPropertiesViaAlchemyRPC() {
  console.log("GETTING EIP-1559 GAS FROM ALCHEMY API");

  const alchemyProvider = new ethers.AlchemyProvider(
    "maticmum",
    ALCHEMY_API_KEY
  );
  const block = await alchemyProvider.getBlock("latest");
  let block_number = block.number;
  let base_fee = parseFloat(ethers.formatUnits(block.baseFeePerGas, "gwei"));

  let max_priority_fee_hex = await alchemyProvider.send(
    "eth_maxPriorityFeePerGas",
    []
  );
  let max_priority_fee_wei =
    ethers.BigNumber.from(max_priority_fee_hex).toNumber();
  let max_priority_fee = parseFloat(
    ethers.formatUnits(max_priority_fee_wei, "gwei")
  );
  max_priority_fee += EXTRA_TIP_FOR_MINER;

  let max_fee_per_gas = base_fee + max_priority_fee;

  //  In case the network gets (up to 25%) more congested
  max_fee_per_gas += base_fee * 0.25;

  console.log(`block_number: ${block_number}`);
  console.log(`base_fee: ${base_fee.toFixed(9)} gwei`);
  console.log(`max_priority_fee_per_gas: ${max_priority_fee} gwei`);
  console.log(`max_fee_per_gas: ${max_fee_per_gas} gwei`);

  //  cast gwei numbers to wei BigNumbers for ethers
  const maxFeePerGas = ethers.parseUnits(max_fee_per_gas.toFixed(8), "gwei");
  const maxPriorityFeePerGas = ethers.parseUnits(
    max_priority_fee.toFixed(8),
    "gwei"
  );

  //  Final object ready to feed into a transaction
  const transactionProperties = {
    maxFeePerGas,
    maxPriorityFeePerGas,
  };

  return transactionProperties;
}

async function getTransactionPropertiesViaInfuraRPC() {
  console.log("GETTING EIP-1559 GAS FROM INFURA API");

  const infuraProvider = new ethers.providers.InfuraProvider(
    "maticmum",
    INFURA_API_KEY
  );
  const block = await infuraProvider.getBlock("latest");
  let block_number = block.number;
  let base_fee = parseFloat(ethers.formatUnits(block.baseFeePerGas, "gwei"));

  let max_priority_fee_hex = await infuraProvider.send(
    "eth_maxPriorityFeePerGas",
    []
  );
  let max_priority_fee_wei =
    ethers.BigNumber.from(max_priority_fee_hex).toNumber();
  let max_priority_fee = parseFloat(
    ethers.formatUnits(max_priority_fee_wei, "gwei")
  );
  max_priority_fee += EXTRA_TIP_FOR_MINER;

  let max_fee_per_gas = base_fee + max_priority_fee;

  //  In case the network gets (up to 25%) more congested
  max_fee_per_gas += base_fee * 0.25;

  console.log(`block_number: ${block_number}`);
  console.log(`base_fee: ${base_fee.toFixed(9)} gwei`);
  console.log(`max_priority_fee_per_gas: ${max_priority_fee} gwei`);
  console.log(`max_fee_per_gas: ${max_fee_per_gas} gwei`);

  //  cast gwei numbers to wei BigNumbers for ethers
  const maxFeePerGas = ethers.parseUnits(max_fee_per_gas.toFixed(8), "gwei");
  const maxPriorityFeePerGas = ethers.parseUnits(
    max_priority_fee.toFixed(8),
    "gwei"
  );

  //  Final object ready to feed into a transaction
  const transactionProperties = {
    maxFeePerGas,
    maxPriorityFeePerGas,
  };

  return transactionProperties;
}

function checkIfMetamask() {
  const { ethereum } = window;
  if (typeof ethereum !== "undefined" && ethereum.isMetaMask) {
    return true;
  }
  return false;
}

// https://docs.cloud.coinbase.com/wallet-sdk/docs/web3modal
async function disconnectWallet() {
  // Clear the wallet connection status in local storage
  window.localStorage.removeItem("walletconnected");
  if (provider.close) {
    await provider.close();
    // If the cached provider is not cleared,
    // WalletConnect will default to the existing session
    // and does not allow to re-scan the QR code with a new wallet.
    // Depending on your use case you may want or want not his behavir.
  }
  await web3Modal.clearCachedProvider();
  provider = null;
  // Remove the wallet dropdown from the document body
  const dropdown = document.querySelector(".wallet-dropdown");
  if (dropdown) {
    dropdown.remove();
  }

  // Update connect button text to "Connect Wallet"
  const connectButton = document.querySelector(
    myMintPluginSettings.connectButtonIdOrClass
  );
  if (connectButton) {
    connectButton.textContent = "Connect Wallet";
  }
}

async function Contract(_provider) {
  if (typeof _provider !== "undefined") {
    return ContractInstance(_provider);
  }
  const signer = await etherPovider.getSigner();
  return ContractInstance(signer);
}

async function getMintPrice(classInput) {
  const contractInstance = await Contract(providerRpc);
  const weiAmount = await contractInstance["MINT_PRICE()"]();
  const maticAmount = ethers.formatUnits(weiAmount, "ether");
  jQuery(classInput).val(maticAmount);
}

async function getMaxQuantity(classInput) {
  const contractInstance = await Contract(providerRpc);
  const maxQuantity = await contractInstance["maxQuantity()"]();
  jQuery(classInput).val(maxQuantity);
}

async function getTotalSupply() {
  try {
    const contractInstance = await Contract(providerRpc);
    return await contractInstance["totalSupply()"]();
  } catch (error) {
    handleError(error);
  }
}

async function getMaxSupply() {
  try {
    const contractInstance = await Contract(providerRpc);
    return await contractInstance["MAX_SUPPLY()"]();
  } catch (error) {
    handleError(error);
  }
}

async function getDiscount(classInput) {
  try {
    const contractInstance = await Contract(providerRpc);
    const discount = await contractInstance["discountPercentage()"]();
    jQuery(classInput).val(discount);
  } catch (error) {
    handleError(error);
  }
}

async function showWalletSelectionPopup() {
  return new Promise((resolve) => {
    const popupContainer = document.createElement("div");
    popupContainer.classList.add("wallet-popup-container");

    const popupContent = document.createElement("div");
    popupContent.classList.add("wallet-popup-content");

    const heading = document.createElement("h3");
    heading.textContent = "Connect Wallet";

    const metamaskButton = document.createElement("button");
    metamaskButton.textContent = "MetaMask";
    metamaskButton.classList.add("wallet-option");
    metamaskButton.addEventListener("click", () => resolve("MetaMask"));

    const coinbaseWalletButton = document.createElement("button");
    coinbaseWalletButton.textContent = "CoinbaseWallet";
    coinbaseWalletButton.classList.add("wallet-option");
    coinbaseWalletButton.addEventListener("click", () =>
      resolve("CoinbaseWallet")
    );

    const trustWalletButton = document.createElement("button");
    trustWalletButton.textContent = "TrustWallet";
    trustWalletButton.classList.add("wallet-option");
    trustWalletButton.addEventListener("click", () => resolve("TrustWallet"));

    popupContent.appendChild(heading);
    popupContent.appendChild(metamaskButton);
    popupContent.appendChild(coinbaseWalletButton);
    popupContent.appendChild(trustWalletButton);

    popupContainer.appendChild(popupContent);

    document.body.appendChild(popupContainer);
  });
}

async function displayPop(key, buttonClass) {
  const popupContainer = document.createElement("div");
  popupContainer.classList.add("dialog");
  popupContainer.dataset.state = "open";
  popupContainer.style.pointerEvents = "auto";

  const popupContent = document.createElement("div");
  popupContent.setAttribute("role", "dialog");
  popupContent.id = "radix-:R8h6plaqk:";
  popupContent.dataset.state = "open";
  popupContent.tabIndex = "-1";
  popupContent.classList.add("wallets-dialog");
  popupContent.style.maxWidth = "480px";
  popupContent.style.pointerEvents = "auto";

  const heading = document.createElement("h2");
  heading.id = "radix-:R8h6plaqkH1:";
  heading.classList.add("modal-title");
  heading.textContent = "Choose your wallet";

  const walletList = document.createElement("ul");
  walletList.classList.add("dialog-list");

  const createWalletButton = (id, logoSrc, name, installed) => {
    const button = document.createElement("button");
    button.id = id;
    button.type = "button";
    button.classList.add("wallet-button");

    const logoImg = document.createElement("img");
    logoImg.width = 32;
    logoImg.height = 32;
    logoImg.src = logoSrc;
    logoImg.alt = "";
    logoImg.loading = "eager";
    logoImg.decoding = "async";
    logoImg.style.height = "32px";
    logoImg.style.width = "32px";

    const walletName = document.createElement("span");
    walletName.classList.add("wallet-name");
    walletName.textContent = name;

    const isInstalled = document.createElement("span");
    isInstalled.classList.add("wallet-is-installed");
    isInstalled.textContent = installed ? "Installed" : "";

    button.appendChild(logoImg);
    button.appendChild(walletName);
    button.appendChild(isInstalled);

    return button;
  };

  const metamaskButton = createWalletButton(
    "metamask",
    "https://ipfs.thirdwebcdn.com/ipfs/QmZZHcw7zcXursywnLDAyY6Hfxzqop5GKgwoq8NB9jjrkN/metamask.svg",
    "MetaMask",
    true
  );

  const coinbaseButton = createWalletButton(
    "coinbase",
    "https://ipfs.thirdwebcdn.com/ipfs/QmcJBHopbwfJcLqJpX2xEufSS84aLbF7bHavYhaXUcrLaH/coinbase.svg",
    "Coinbase Wallet",
    true
  );

  const walletConnectButton = createWalletButton(
    "wallet-connect",
    "https://ipfs.thirdwebcdn.com/ipfs/QmX58KPRaTC9JYZ7KriuBzeoEaV2P9eZcA3qbFnTHZazKw/wallet-connect.svg",
    "WalletConnect",
    true
  );

  walletList.appendChild(metamaskButton);
  walletList.appendChild(coinbaseButton);
  walletList.appendChild(walletConnectButton);

  const helpButton = document.createElement("button");
  helpButton.type = "button";
  helpButton.classList.add("help-btn");
  helpButton.style.display = "block";
  helpButton.style.width = "100%";
  helpButton.style.textAlign = "center";
  helpButton.textContent = "Need help getting started?";

  const closeButtonContainer = document.createElement("div");
  closeButtonContainer.classList.add("close-btn");

  const closeButton = document.createElement("button");
  closeButton.type = "button";
  closeButton.setAttribute("aria-label", "Close");
  closeButton.classList.add("css-101rlmy");

  const closeIcon = document.createElement("svg");
  closeIcon.width = 15;
  closeIcon.height = 15;
  closeIcon.setAttribute("viewBox", "0 0 15 15");
  closeIcon.setAttribute("fill", "none");
  closeIcon.setAttribute("xmlns", "http://www.w3.org/2000/svg");
  closeIcon.style.width = "24px";
  closeIcon.style.height = "24px";
  closeIcon.style.color = "inherit";

  const closeIconPath = document.createElement("path");
  closeIconPath.setAttribute(
    "d",
    "M11.7816 4.03157C12.0062 3.80702 12.0062 3.44295 11.7816 3.2184C11.5571 2.99385 11.193 2.99385 10.9685 3.2184L7.50005 6.68682L4.03164 3.2184C3.80708 2.99385 3.44301 2.99385 3.21846 3.2184C2.99391 3.44295 2.99391 3.80702 3.21846 4.03157L6.68688 7.49999L3.21846 10.9684C2.99391 11.193 2.99391 11.557 3.21846 11.7816C3.44301 12.0061 3.80708 12.0061 4.03164 11.7816L7.50005 8.31316L10.9685 11.7816C11.193 12.0061 11.5571 12.0061 11.7816 11.7816C12.0062 11.557 12.0062 11.193 11.7816 10.9684L8.31322 7.49999L11.7816 4.03157Z"
  );
  closeIconPath.setAttribute("fill", "currentColor");
  closeIconPath.setAttribute("fill-rule", "evenodd");
  closeIconPath.setAttribute("clip-rule", "evenodd");

  closeIcon.appendChild(closeIconPath);
  closeButton.appendChild(closeIcon);
  closeButtonContainer.appendChild(closeButton);

  popupContent.appendChild(heading);
  popupContent.appendChild(walletList);
  popupContent.appendChild(helpButton);
  popupContent.appendChild(closeButtonContainer);

  // popupContainer.appendChild(popupContent);
  document.body.appendChild(popupContainer);
  document.body.appendChild(popupContent);

  // Event listeners for wallet selection
  metamaskButton.addEventListener("click", async () => {
    await ConnectWallet("metamask", key, buttonClass);
    popupContainer.remove();
    popupContent.remove();
  });

  coinbaseButton.addEventListener("click", async () => {
    await ConnectWallet("coinbase", key, buttonClass);
    popupContainer.remove();
    popupContent.remove();
  });

  walletConnectButton.addEventListener("click", async () => {
    await ConnectWallet("walletconnect", key, buttonClass);
    popupContainer.remove();
    popupContent.remove();
  });
}

async function ConnectWallet(wallet, key, buttonClass) {
  try {
    library = await getProvider(wallet);

    switchToNetwork(provider, mumbaiTestnet);

    const accounts = await provider.request({
      method: "eth_requestAccounts",
    });

    _address = accounts[0];

    const balance = await library.getBalance(_address);
    const maticBalance = ethers.formatEther(balance);
    const shortenedAddress = `${_address.substr(0, 3)}...${_address.substr(
      -4
    )}`;

    // Update connect button text with the wallet address
    const connectButton = document.querySelector(buttonClass);
    connectButton.textContent = shortenedAddress;

    const dropdown = document.querySelector(".wallet-dropdown");
    if (dropdown) {
      // Dropdown already exists, toggle its display
      dropdown.style.display =
        dropdown.style.display === "none" ? "block" : "none";
    } else {
      // Create the dropdown element
      const dropdown = document.createElement("div");
      dropdown.classList.add("wallet-dropdown");

      const maticBalanceElement = document.createElement("div");
      maticBalanceElement.textContent = `Matic: ${maticBalance}`;

      const disconnectButton = document.createElement("button");
      disconnectButton.textContent = "Disconnect";
      disconnectButton.addEventListener("click", disconnectWallet);
      dropdown.appendChild(maticBalanceElement);
      dropdown.appendChild(disconnectButton);

      // Append the dropdown to the connect button's parent element
      const connectButtonParent = connectButton.parentNode;
      connectButtonParent.appendChild(dropdown);
    }
    window.localStorage.setItem(key, true);
  } catch (error) {
    console.error(error);
  }
}

async function getProvider(wallet) {
  if (typeof etherPovider === "undefined" || typeof provider === "undefined") {
    init();
    provider = await web3Modal.connect(wallet);
    addListeners(provider);
    // v6:
    etherPovider = new ethers.BrowserProvider(provider);
  }
  return etherPovider;
}

async function addListeners(web3ModalProvider) {
  web3ModalProvider.on("accountsChanged", (accounts) => {
    // window.location.reload();
  });
  // Subscribe to chainId change
  web3ModalProvider.on("chainChanged", (chainId) => {
    // window.location.reload();
  });
}

async function switchToNetwork(wallet, network) {
  try {
    await wallet.request({
      method: "wallet_switchEthereumChain",
      params: [{ chainId: network.chainId }], // chainId must be in hexadecimal numbers
    });
  } catch (error) {
    if (error.code === 4902) {
      try {
        await wallet.request({
          method: "wallet_addEthereumChain",
          params: [network],
        });
      } catch (addError) {
        console.error(addError);
      }
    }
    console.error(error);
  }
}

function cronMintConfig() {
  let quantity = parseInt($(myMintPluginSettings.mintQuantityIdOrClass).val());
  let totalPrice = (
    quantity * parseFloat(myMintPluginSettings.mintPrice)
  ).toFixed(3);
  let type = "erc-721";

  let mintConfig = {
    type: type,
    quantity: quantity,
    totalPrice: totalPrice.toString(),
  };

  // Update mintConfig attribute with the new JSON value
  $("crossmint-pay-button").attr("mintConfig", JSON.stringify(mintConfig));
}

// const dic_net = {
//   name: "Matic Mumbai",
//   chainId: 80001,
//   _defaultProvider: (providers) =>
//     new providers.JsonRpcProvider("https://matic-mumbai.chainstacklabs.com"),
// };
// const dic_net = {
//   name: "Matic Mumbai",
//   chainId: 80001,
//   _defaultProvider: (providers) =>
//     new providers.JsonRpcProvider("https://polygon-mumbai.g.alchemy.com/v2/REDACTED_ALCHEMY_KEY"),
// };
// const provider = ethers.getDefaultProvider(dic_net);
// mainet https://rpc.ankr.com/polygon
// testnet https://rpc.ankr.com/polygon_mumbai
// const provider = new ethers.providers.JsonRpcProvider('https://rpc.ankr.com/polygon_mumbai');
