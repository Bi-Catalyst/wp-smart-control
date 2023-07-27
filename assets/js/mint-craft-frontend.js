// Hide the popup
function hidePopup() {
  const popup = document.getElementById("popup");
  popup.style.display = "none";
}
// Show the popup with a specific message
function showPopup(type, content) {
  const popup = document.getElementById("popup");
  const popupText = document.getElementById("popup-text");
  const popupIcon = document.getElementById("popup-icon");
  const popupButton = document.getElementById("popup-button");

  popupText.innerHTML = content;
  popup.style.display = "block";

  let icon = "";

  switch (type) {
    case "success":
      icon =
        '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="#ffffff" d="M9.172 16.172l-4.172-4.172-1.414 1.414 5.586 5.586 12-12-1.414-1.414z" /></svg>'; // Replace with the SVG code for the success icon
      // popup.style.backgroundColor = "#12c99b";
      popup.style.backgroundColor = "rgba(231, 1, 126, 0.9)";
      break;
    case "error":
      icon =
        '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="#ffffff" d="M12 2c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zM12 22c-5.514 0-10-4.486-10-10s4.486-10 10-10 10 4.486 10 10-4.486 10-10 10z"/><path fill="#ffffff" d="M12 7c-0.552 0-1 0.448-1 1v6c0 0.552 0.448 1 1 1s1-0.448 1-1v-6c0-0.552-0.448-1-1-1zM12 16c-0.552 0-1-0.448-1-1s0.448-1 1-1 1 0.448 1 1-0.448 1-1 1z"/></svg>'; // Replace with the SVG code for the error icon
      // popup.style.backgroundColor = "#e41749"; // Alternative color: #e74c3c
      popup.style.backgroundColor = "rgba(1, 1, 0, 0.8)";

      break;
    case "warning":
      icon =
        '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="#ffffff" d="M12 2c-6.627 0-12 5.373-12 12 0 6.628 5.373 12 12 12s12-5.372 12-12c0-6.627-5.373-12-12-12zM12 22c-5.514 0-10-4.486-10-10s4.486-10 10-10 10 4.486 10 10c0 5.514-4.486 10-10 10zM11 15h2v2h-2zM11 7h2v6h-2z"/></svg>'; // Replace with the SVG code for the warning icon
      popup.style.backgroundColor = "#f2a600"; // Alternative color: #f1c40f
      break;
  }

  popupIcon.innerHTML = icon;

  // Add event listener to hide the popup when the button is clicked
  popupButton.addEventListener("click", () => {
    hidePopup();
  });

  // Automatically hide the popup after 3 seconds
  // setTimeout(() => {
  //   hidePopup();
  // }, 3000);
}
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
  const quantity = Number.parseInt(
    document.querySelector(myMintPluginSettings.mintQuantityIdOrClass).value
  );
  // if (quantity > Number.parseInt(myMintPluginSettings.maxQuantity)) {
  //   showPopup(
  //     "error",
  //     "Maximum quantity allowed is " + myMintPluginSettings.maxQuantity
  //   );
  //   return;
  // }
  const totalPrice = (
    quantity * Number.parseFloat(myMintPluginSettings.mintPrice)
  ).toFixed(3);
  const type = "erc-721";

  const mintConfig = {
    type: type,
    quantity: quantity,
    totalPrice: totalPrice.toString(),
  };
  // Update mintConfig attribute with the new JSON value
  document
    .querySelector("crossmint-pay-button")
    .setAttribute("mintConfig", JSON.stringify(mintConfig));
}

// Function to handle the "decrease" button click
function decreaseQuantity() {
  var quantityInput = document.querySelector(
    myMintPluginSettings.mintQuantityIdOrClass
  );
  var currentValue = Number.parseInt(quantityInput.value);

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
  var currentValue = Number.parseInt(quantityInput.value);
  if (currentValue + 1 > myMintPluginSettings.maxQuantity) {
    showPopup(
      "error",
      myMintPluginSettings.pupup.errorQuantity + ' ' + myMintPluginSettings.maxQuantity
    );
    return;
  } else {
    quantityInput.value = currentValue + 1;
    crossMintConfig();
  }
}

jQuery(document).ready(async function ($) {
  // Add popup
  document
    .querySelector(myMintPluginSettings.mintQuantityIdOrClass)
    .setAttribute("max", Number.parseInt(myMintPluginSettings.maxQuantity) + 1);
  if ($("#popup").length === 0) {
    // Create the popup element
    var popup = $(
      '<div id="popup" class="popup" style="display: none;"><div class="icon__wrapper"><div id="popup-icon"></div></div><span id="popup-text"></span><button id="popup-button">OK</button></div>'
    );
    // Append the popup to the body
    $("body").append(popup);
  }

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
    jQuery(myMintPluginSettings.minterCounterIdOrClass).text(
      myMintPluginSettings.totalSupply
    );
    await getTotalSupply(function (data) {
      jQuery(myMintPluginSettings.minterCounterIdOrClass).text(data);
    });
  } catch (error) {
    console.log(error);
    // jQuery(myMintPluginSettings.minterCounterIdOrClass).text(data);
  }

  try {
    jQuery("#total-nft-sup").text(myMintPluginSettings.maxSupply);
    await getMaxSupply(function (data) {
      jQuery("#total-nft-sup").text(data);
    });
  } catch (error) {
    console.log(error);
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
  crossMintConfig();
  $(document).on(
    "click",
    myMintPluginSettings.mintButtonIdOrClass,
    async function () {
      if (
        window.localStorage.getItem("wagmi.connected") === null ||
        window.localStorage.getItem("wagmi.connected") === "false"
      ) {
        window.localStorage.setItem("TRIGGER_MINT", true);
        await web3modal.openModal();
      } else {
        // Get the user's selected quantity from the input field
        const quantity = parseInt(
          $(myMintPluginSettings.mintQuantityIdOrClass).val()
        );

        // Set quantity to 1 if it's not a valid number or less than or equal to zero
        if (isNaN(quantity) || quantity <= 0) {
          quantity = 1;
        }
        if (quantity > Number.parseInt(myMintPluginSettings.maxQuantity)) {
          showPopup(
            "error",
            myMintPluginSettings.maxQuantity + myMintPluginSettings.maxQuantity
          );
          return;
        }
        // Check if the terms checkbox is checked
        if (!$(".terms-checkbox").is(":checked")) {
          // Display the popup with the appropriate message
          showPopup("error", myMintPluginSettings.popup.errorTermAndCondition);
        } else {
          // Call the mint function
          mint(quantity);
        }
      }
    }
  );
});
