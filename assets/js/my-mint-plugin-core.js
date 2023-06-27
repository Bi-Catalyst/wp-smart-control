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

let etherPovider;
// Chosen wallet provider given by the dialog window
let provider;


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
