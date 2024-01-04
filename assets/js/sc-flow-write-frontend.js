window.mint = async function mint(quantity) {
  const weiAmount = ethers.parseUnits(
    (quantity * Number.parseFloat(SCFlowPluginSettings.mintPrice)).toString(),
    "ether"
  );
  const { request } = await prepareWriteContract({
    address: SCFlowPluginSettings.contractAddress,
    abi: SCFlowPluginSettings.contractABI,
    functionName: "mint",
    args: [1],
    gas: 3000000n,
    value: weiAmount,
  });
  const { hash } = await writeContract(request);
};
