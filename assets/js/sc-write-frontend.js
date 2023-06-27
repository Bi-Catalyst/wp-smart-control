
window.mint = async function mint(quantity) {
  const weiAmount = ethers.parseUnits(
    (quantity * Number.parseFloat(myMintPluginSettings.mintPrice)).toString(),
    "ether"
  );
  const { request } = await prepareWriteContract({
    address: myMintPluginSettings.contractAddress,
    abi: myMintPluginSettings.contractABI,
    functionName: "mint",
    args: [1],
    gas: 3000000n,
    value: weiAmount,
  });
  const { hash } = await writeContract(request);
};
