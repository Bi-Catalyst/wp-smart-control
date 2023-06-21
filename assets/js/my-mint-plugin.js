// Function to handle the "decrease" button click
function decreaseQuantity() {
  var quantityInput = document.querySelector(
    myMintPluginSettings.mintQuantityIdOrClass
  );
  var currentValue = parseInt(quantityInput.value);

  if (currentValue > 1) {
    quantityInput.value = currentValue - 1;
  }
  cronMintConfig();
}

// Function to handle the "increase" button click
function increaseQuantity() {
  var quantityInput = document.querySelector(
    myMintPluginSettings.mintQuantityIdOrClass
  );
  var currentValue = parseInt(quantityInput.value);

  if (currentValue < 5) {
    quantityInput.value = currentValue + 1;
  }
  cronMintConfig();
}

async function mintWithWallet(quantity) {
  try {
    // Instantiate contract
    const contractInstance = await Contract();
    // Generate Nonce
    const nonce = await getNonce(providerRpc, _address);
    // Get transaction properties
    let transactionProperties = await getTransactionPropertiesViaGasStation();
    // Create row tx
    let rawTxn = await contractInstance.populateTransaction.mint(
      quantity,
      transactionProperties
    );

    console.log(
      "...Submitting transaction with gas price of:",
      ethers.formatUnits(gasFee, "gwei"),
      " - & nonce:",
      nonce
    );
    // Create Wallet
    const wallet = getWallet(etherPovider);
    // Send row transaction
    let signedTxn = (await wallet).broadcastTransaction(rawTxn);
    // wait for confirmation
    let reciept = (await signedTxn).wait();

    if (reciept) {
      console.log(
        "Transaction is successful!!!" + "\n" + "Transaction Hash:",
        (await signedTxn).hash +
        "\n" +
        "Block Number: " +
        (await reciept).blockNumber +
        "\n" +
        "Navigate to https://polygonscan.com/tx/" +
        (await signedTxn).hash,
        "to see your transaction"
      );
    } else {
      console.log("Error submitting transaction");
    }
  } catch (e) {
    handleError(error);
  }
}

async function mint(quantity) {
  // Create a new ethers provider

  // Instantiate contract
  // Mint the NFTs
  try {
    // Instantiate contract
    const signer = await etherPovider.getSigner();
    const contractInstance = ContractInstance(signer);
    const totalPrice = await contractInstance["MINT_PRICE()"]();
    // Mint
    const weiAmount = ethers.parseUnits(
      (quantity * Number.parseFloat(myMintPluginSettings.mintPrice)).toString(),
      "ether"
    );

    console.log("NFT price from smart contract", totalPrice);
    console.log("NFT price from WP", weiAmount);

    // Gete nonce
    const nonce = await getNonce(providerRpc, _address);
    const tx = await _contractInstance["mint(uint256)"](quantity, {
      value: weiAmount.toString(),
      // value: totalPrice,
      gasLimit: 3000000,
      nonce: nonce || undefined,
    });

    await tx.wait();

    alert("Transaction confirmed");
    // Additional logic after successful minting
  } catch (error) {
    handleError(error);
  }
}

function getWallet(library, privateKey) {
  const wallet = new ethers.Wallet(privateKey, library);
  return wallet;
}

async function getNonce(library, address) {
  let nonce = await library.getTransactionCount(address);
  return nonce;
}

async function getBalance(library, account) {
  const balance = await library.getBalance(account);
  return balance;
}

jQuery(document).ready(async function ($) {
  // Connect wallet button click event
  // Attach event listener to the "decrease" button
  var decreaseBtn = document.querySelector(".decrease-btn");
  decreaseBtn.addEventListener("click", decreaseQuantity);

  // Attach event listener to the "increase" button
  var increaseBtn = document.querySelector(".increase-btn");
  increaseBtn.addEventListener("click", increaseQuantity);

  // Set mint price value
  jQuery(".eth-price").text(myMintPluginSettings.mintPrice + "MATIC");

  const totalSupply = await getTotalSupply();
  const maxSupply = await getMaxSupply();

  jQuery(myMintPluginSettings.mintercounter).text(
    totalSupply + "/" + maxSupply
  );

  // Get matic value on Fiat
  const currency = "chf"; // or 'chf'
  getMaticPrice(currency)
    .then((price) => {
      if (price !== null) {
        console.log(`Matic price in ${currency.toUpperCase()}: ${price}`);
        jQuery(".fiat-price").text("CHF " +
          (
            Number.parseFloat(myMintPluginSettings.mintPrice) *
            Number.parseFloat(price)
          ).toFixed(4) + ".-"
        );
      }
    })
    .catch((error) => {
      console.log("Error:", error);
    });
  // const connectButton = document.querySelector(
  //   myMintPluginSettings.connectButtonIdOrClass
  // );
  // Add listener for mint button
  $(document).on(
    "click",
    myMintPluginSettings.mintButtonIdOrClass,
    async function () {
      // Get the user's selected quantity from the input field
      let quantity = parseInt(
        $(myMintPluginSettings.mintQuantityIdOrClass).val()
      );

      // Set quantity to 1 if it's not a valid number or less than or equal to zero
      if (isNaN(quantity) || quantity <= 0) {
        quantity = 1;
      }

      // Confirm the minting action with the user
      if (!confirm("Are you sure you want to mint " + quantity + " NFT(s)?")) {
        return;
      }
      // setMintPrice();
      mint(quantity);
    }
  );
});
