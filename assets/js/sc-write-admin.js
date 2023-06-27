// Write operations
window.setMintPrice = async function setMintPrice(nftPrice) {
    // Convert the price to wei (1 MATIC = 10^18 wei)
    const newNFTPrice = ethers.parseUnits(nftPrice, "ether");
    const { request } = await prepareWriteContract({
      address: myMintPluginSettings.contractAddress,
      abi: myMintPluginSettings.contractABI,
      functionName: "setMintPrice",
      args: [1],
      gas: 3000000n,
      value: newNFTPrice,
    });
    const { hash } = await writeContract(request);
    console.log(hash);
  };
  
  window.setDiscountPercentage = async function setDiscountPercentage(discount) {
    // Convert the price to wei (1 MATIC = 10^18 wei)
    const { request } = await prepareWriteContract({
      address: myMintPluginSettings.contractAddress,
      abi: myMintPluginSettings.contractABI,
      functionName: "setDiscountPercentage",
      args: [1],
      gas: 3000000n,
      value: discount,
    });
    const { hash } = await writeContract(request);
    console.log(hash);
  };
  
  window.setMaxQuantity = async function setMaxQuantity(discount) {
    // Convert the price to wei (1 MATIC = 10^18 wei)
    const { request } = await prepareWriteContract({
      address: myMintPluginSettings.contractAddress,
      abi: myMintPluginSettings.contractABI,
      functionName: "setmaxQuantity",
      args: [1],
      gas: 3000000n,
      value: quantity,
    });
    const { hash } = await writeContract(request);
    console.log(hash);
  };
  