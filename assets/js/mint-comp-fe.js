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

function crossMintConfig() {
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

// Function to handle the "decrease" button click
function decreaseQuantity() {
  var quantityInput = document.querySelector(
    myMintPluginSettings.mintQuantityIdOrClass
  );
  var currentValue = parseInt(quantityInput.value);

  if (currentValue > 1) {
    quantityInput.value = currentValue - 1;
  }
  crossMintConfig();
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
  crossMintConfig();
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
  jQuery(".final-nft-price-crypto").text(
    myMintPluginSettings.mintPrice + " MATIC"
  );

  try {
    await getTotalSupply(function (data) {
      jQuery(myMintPluginSettings.mintercounter).text(data);
    });
  } catch (error) {
    console.log(error);
    // jQuery(myMintPluginSettings.mintercounter).text(data);
  }

  try {
    await getMaxSupply(function (data) {
      jQuery("#total-nft-sup").text(data);
    });
  } catch (error) {
    jQuery("#total-nft-sup").text("3000");
  }

  // Get matic value on Fiat
  const currency = "chf"; // or 'chf'

  getMaticPrice(currency)
    .then((price) => {
      if (price !== null) {
        console.log(`Matic price in ${currency.toUpperCase()}: ${price}`);
        jQuery(".final-nft-price").text(
          "CHF " +
            (
              Number.parseFloat(myMintPluginSettings.mintPrice) *
              Number.parseFloat(price)
            ).toFixed(4) +
            ".-"
        );
      }
    })
    .catch((error) => {
      console.log("Error:", error);
    });

  $(document).on(
    "click",
    myMintPluginSettings.mintButtonIdOrClass,
    async function () {
      if (
        window.localStorage.getItem("wagmi.connected") === null ||
        window.localStorage.getItem("wagmi.connected") === "false"
      ) {
        window.localStorage.setItem("TIGGER_MINT", true);
        await web3modal.openModal();
      } else {
        // Get the user's selected quantity from the input field
        let quantity = parseInt(
          $(myMintPluginSettings.mintQuantityIdOrClass).val()
        );

        // Set quantity to 1 if it's not a valid number or less than or equal to zero
        if (isNaN(quantity) || quantity <= 0) {
          quantity = 1;
        }
        mint(quantity);
      }
    }
  );
});
