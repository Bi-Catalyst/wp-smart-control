function crossMintConfig() {
  const quantity = Number.parseInt(
    document.querySelector(SCFlowPluginSettings.mintQuantityIdOrClass).value
  );
  // if (quantity > Number.parseInt(SCFlowPluginSettings.maxQuantity)) {
  //   showPopup(
  //     "error",
  //     "Maximum quantity allowed is " + SCFlowPluginSettings.maxQuantity
  //   );
  //   return;
  // }
  const totalPrice = (
    quantity * Number.parseFloat(SCFlowPluginSettings.mintPrice)
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
    SCFlowPluginSettings.mintQuantityIdOrClass
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
    SCFlowPluginSettings.mintQuantityIdOrClass
  );
  var currentValue = Number.parseInt(quantityInput.value);
  if (currentValue + 1 > SCFlowPluginSettings.maxQuantity) {
    showPopup(
      "error",
      SCFlowPluginSettings.pupup.errorQuantity +
        " " +
        SCFlowPluginSettings.maxQuantity
    );
    return;
  } else {
    quantityInput.value = currentValue + 1;
    crossMintConfig();
  }
}

jQuery(document).ready(async function ($) {
  if (
    !document.querySelector(SCFlowPluginSettings.mintQuantityIdOrClass) ||
    typeof document.querySelector(
      SCFlowPluginSettings.mintQuantityIdOrClass
    ) === "undefined"
  ) {
    console.log("Mint quantity css class not configured");
    return;
  }
  if (
    !document.querySelector(SCFlowPluginSettings.mintButtonIdOrClass) ||
    typeof document.querySelector(SCFlowPluginSettings.mintButtonIdOrClass) ===
      "undefined"
  ) {
    console.log("Mint button css class not configured");
    return;
  }
  // Add popup
  document
    .querySelector(SCFlowPluginSettings.mintQuantityIdOrClass)
    .setAttribute("max", Number.parseInt(SCFlowPluginSettings.maxQuantity) + 1);

  if ($("#popup").length === 0) {
    // Create the popup element
    var popup = $(
      '<div id="popup" class="popup" style="display: none;"><div class="icon__wrapper"><div id="popup-icon"></div></div><span id="popup-text"></span><button id="popup-button">OK</button></div>'
    );
    // Append the popup to the body
    $("body").append(popup);
  }

  // Attach event listener to the "decrease" button
  const decreaseBtn = document.querySelector(".decrease-btn");
  decreaseBtn.addEventListener("click", decreaseQuantity);

  // Attach event listener to the "increase" button
  const increaseBtn = document.querySelector(".increase-btn");
  increaseBtn.addEventListener("click", increaseQuantity);

  try {
    // Set mint price value
    jQuery(".final-nft-price-crypto").text(
      SCFlowPluginSettings.mintPrice + " MATIC"
    );

    // Total supply
    jQuery(SCFlowPluginSettings.minterCounterIdOrClass).text(
      SCFlowPluginSettings.totalSupply
    );
    await getTotalSupply(function (data) {
      jQuery(SCFlowPluginSettings.minterCounterIdOrClass).text(data);
    });

    // Max Supply
    jQuery("#total-nft-sup").text(SCFlowPluginSettings.maxSupply);
    await getMaxSupply(function (data) {
      jQuery("#total-nft-sup").text(data);
    });
  } catch (error) {
    console.log(error);
  }

  // Matic value on fiat
  const currency = SCFlowPluginSettings.fiatCurrency.toLowerCase(); // or 'chf'

  getMaticPrice(currency)
    .then((price) => {
      if (price !== null) {
        jQuery(".final-nft-price").text(
          SCFlowPluginSettings.fiatCurrency.toUpperCase() +
            " " +
            (
              Number.parseFloat(SCFlowPluginSettings.mintPrice) *
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
    SCFlowPluginSettings.mintButtonIdOrClass,
    async function () {
      try {
        // Check if the terms checkbox is checked
        if (!$(".terms-checkbox").is(":checked")) {
          // Display the popup with the appropriate message
          showPopup("error", SCFlowPluginSettings.popup.errorTermAndCondition);
          return;
        }
        if (
          localStorage.getItem("wagmi.connected") === null ||
          localStorage.getItem("wagmi.connected") === "false"
        ) {
          localStorage.setItem("TRIGGER_MINT", true);
          await web3modal.openModal();
        } else {
          // Get the user's selected quantity from the input field
          const quantity = parseInt(
            $(SCFlowPluginSettings.mintQuantityIdOrClass).val()
          );

          // Set quantity to 1 if it's not a valid number or less than or equal to zero
          if (isNaN(quantity) || quantity <= 0) {
            quantity = 1;
          }
          if (quantity > Number.parseInt(SCFlowPluginSettings.maxQuantity)) {
            showPopup(
              "error",
              SCFlowPluginSettings.maxQuantity +
                SCFlowPluginSettings.maxQuantity
            );
            return;
          }
          await mint(quantity, function (hash) {
            const { chain } = getNetwork();
            showPopup(
              "success",
              `
          <div class="popup-content">
            <p>${SCFlowPluginSettings.popup.sucessMint} <a href="${chain.blockExplorers.default.url}/tx/${hash}" target="_blank">here</a>.</p>
          </div>
          `
            );
          });
        }
      } catch (error) {
        localStorage.setItem("TRIGGER_MINT", false);
        showPopup(
          "error",
          `
          <div class="popup-content">
            <p>${error.shortMessage ? error.shortMessage : error.message}.</p>
          </div>
          `
        );
      }
    }
  );
});
