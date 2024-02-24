// Read operation
window.getMintPrice = async function getMintPrice(fn) {
  // Convert the price to wei (1 MATIC = 10^18 wei)
  try {
    const data = await readContract({
      address: SCFlowPluginSettings.contractAddress,
      abi: SCFlowPluginSettings.contractABI,
      functionName: "mintPrice",
    });
    const maticAmount = ethers.formatUnits(data, "ether");
    fn(maticAmount);
  } catch (error) {
    console.log(error);
  }
};

window.getMaxQuantity = async function getMaxQuantity(fn) {
  // Convert the price to wei (1 MATIC = 10^18 wei)
  try {
    const data = await readContract({
      address: SCFlowPluginSettings.contractAddress,
      abi: SCFlowPluginSettings.contractABI,
      functionName: "maxQuantity",
    });
    fn(data);
  } catch (error) {
    console.log(error);
  }
};

window.getTotalSupply = async function getTotalSupply(fn) {
  // Convert the price to wei (1 MATIC = 10^18 wei)
  try {
    const data = await readContract({
      address: SCFlowPluginSettings.contractAddress,
      abi: SCFlowPluginSettings.contractABI,
      functionName: "totalSupply",
    });
    fn(data);
  } catch (error) {
    console.log(error);
  }
};

window.getMaxSupply = async function getMaxSupply(fn) {
  // Convert the price to wei (1 MATIC = 10^18 wei)
  try {
    const data = await readContract({
      address: SCFlowPluginSettings.contractAddress,
      abi: SCFlowPluginSettings.contractABI,
      functionName: "maxSupply",
    });
    fn(data);
  } catch (error) {
    console.log(error);
  }
};

window.mint = async function mint(quantity, fn) {
  try {
    // Convert the price to wei (1 MATIC = 10^18 wei)
    const address = window.localStorage.getItem("WALLET_ADDRESS");
    // Call the mint function
    const value = ethers.parseUnits(
      (quantity * Number.parseFloat(SCFlowPluginSettings.mintPrice)).toString(),
      "ether"
    );
    const { request } = await prepareWriteContract({
      address: SCFlowPluginSettings.contractAddress,
      abi: SCFlowPluginSettings.contractABI,
      functionName: "mintTo",
      args: [quantity, address],
      // gas: estimatedGas,
      value: value,
    });
    const { hash } = await writeContract(request);
    fn(hash);
  } catch (error) {
    console.log(error);
  }
};
