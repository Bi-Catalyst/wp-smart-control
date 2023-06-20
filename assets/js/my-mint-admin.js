async function setMintPrice(nftPrice) {
  try {
    // Signer
    const signer = await etherPovider.getSigner();

    // Instantiate contract
    const contractInstance = ContractInstance(signer);

    // Convert the price to wei (1 MATIC = 10^18 wei)
    const newPrice = ethers.parseUnits(nftPrice, "ether");

    // Call the setMintPrice function
    const tx = await contractInstance.setMintPrice(newPrice);

    // Wait for the transaction to be mined
    const receipt = await tx.wait();

    // Check the transaction status
    if (receipt.status === 1) {
      console.log("Mint price successfully updated");
    } else {
      console.log("Transaction failed");
    }
  } catch (error) {
    handleError(error);
  }
}

async function setDiscountPrice(discount) {
  try {
    // Signer
    const signer = await etherPovider.getSigner();
    // Instantiate contract
    const contractInstance = ContractInstance(signer);
    // Call the setMintPrice function

    const tx = await contractInstance.setDiscountPercentage(discount);
    // Wait for the transaction to be mined

    const receipt = await tx.wait();

    // Check the transaction status
    if (receipt.status === 1) {
      console.log("Discount successfully updated");
    } else {
      console.log("Transaction failed");
    }
  } catch (error) {
    handleError(error);
  }
}

async function setMaxQuantity(quantity) {
  try {
    // Instantiate contract
    const contractInstance = await Contract();
    // Call the setMintPrice function
    const tx = await contractInstance.setmaxQuantity(quantity);
    // Wait for the transaction to be mined
    const receipt = await tx.wait();

    // Check the transaction status
    if (receipt.status === 1) {
      console.log("Maximum quantity successfully updated");
    } else {
      console.log("Transaction failed");
    }
  } catch (error) {
    handleError(error);
  }
}

(function ($) {
  $(document).ready(async function () {
    // Check if wallet is connected already
    if ($("body").hasClass("toplevel_page_my-mint-plugin-settings")) {
      // Add listener to connect button
      document.querySelectorAll(".connect-wallet-button").forEach((link) => {
        link.addEventListener("click", async (e) => {
          e.preventDefault();
          displayPop("walletconnected-admin", ".connect-wallet-button");
        });
      });
      // Get public contract data
      getMintPrice("#nft_price_input");
      getDiscount("#mint_discount_input");
      getMaxQuantity("#max_quantity_input");
      // Check if the user has already connected wallet
      // if (window.localStorage.getItem("walletconnected-admin") === "true") {
      //   ConnectWallet("walletconnected-admin", ".connect-wallet-button")
      //     .then((v) => {
      //       console.log(v);
      //     })
      //     .catch((error) => {
      //       console.log(error);
      //     });
      // }
      if (web3Modal && web3Modal.cachedProvider) {
        await web3Modal.connect();
      }
      // Add listener to NFT price button
      $(document).on("click", "#nft_price_button", async function (e) {
        e.preventDefault();
        // Get the user's selected price
        let nftPrice = $("#nft_price_input").val();

        // validate price
        if (isNaN(nftPrice) || nftPrice < 0) {
          alert("NFT price can not be 0");
        }
        // set the new mint price
        setMintPrice(nftPrice);
      });

      // Add listener to discount button
      $(document).on("click", "#mint_discount_button", async function (e) {
        e.preventDefault();
        // Get the user's discount from input field
        let discount = parseInt($("#mint_discount_input").val());

        // validate discount
        if (isNaN(discount) || discount < 0 || discount >= 100) {
          alert("Discount price can not be 0% or 100%");
        }
        // set the new discount
        setDiscountPrice(discount);
      });

      // Add listener to discount button
      $(document).on("click", "#max_quantity_button", async function (e) {
        e.preventDefault();
        // Get the user's discount from input field
        let quantity = parseInt($("#max_quantity_input").val());

        // validate discount
        if (isNaN(quantity) || quantity < 1 || quantity >= 100) {
          alert("quantity can be 0, or greater thatn 100");
        }
        // set the new discount
        setMaxQuantity(quantity);
      });
    }
  });
})(jQuery);
