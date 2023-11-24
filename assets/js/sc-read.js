// Read operation
window.getMintPrice = async function getMintPrice(fn) {
  // Convert the price to wei (1 MATIC = 10^18 wei)
  const data = await readContract({
    address: myMintPluginSettings.contractAddress,
    abi: myMintPluginSettings.contractABI,
    functionName: "mintPrice",
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


window.mint = async function mint(quantity,value,gas,fn) {
  // Convert the price to wei (1 MATIC = 10^18 wei)
  const { request } = await prepareWriteContract({
    address: myMintPluginSettings.contractAddress,
    abi: myMintPluginSettings.contractABI,
    functionName: "mint",
    args: [quantity],
    // gas: gas,
    value: value,
  });
  const { hash } = await writeContract(request);
  fn(hash)
};