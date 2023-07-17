// Read operation
window.getMintPrice = async function getMintPrice(fn) {
  // Convert the price to wei (1 MATIC = 10^18 wei)
  const data = await readContract({
    address: myMintPluginSettings.contractAddress,
    abi: myMintPluginSettings.contractABI,
    functionName: "MINT_PRICE",
  });
  console.log(data);
  const maticAmount = ethers.formatUnits(data, "ether");
  fn(maticAmount)
};

window.getMaxQuantity = async function getMaxQuantity(fn) {
  // Convert the price to wei (1 MATIC = 10^18 wei)
  const data = await readContract({
    address: myMintPluginSettings.contractAddress,
    abi: myMintPluginSettings.contractABI,
    functionName: "maxQuantity",
  });
  console.log(data);
  fn(data)
};

window.getTotalSupply = async function getTotalSupply(fn) {
  // Convert the price to wei (1 MATIC = 10^18 wei)
  const data = await readContract({
    address: myMintPluginSettings.contractAddress,
    abi: myMintPluginSettings.contractABI,
    functionName: "totalSupply",
  });
  console.log(data);
  fn(data)
};

window.getMaxSupply = async function getMaxSupply(fn) {
  // Convert the price to wei (1 MATIC = 10^18 wei)
  const data = await readContract({
    address: myMintPluginSettings.contractAddress,
    abi: myMintPluginSettings.contractABI,
    functionName: "MAX_SUPPLY",
  });
  console.log(data);
  fn(data)
};