(function ($) {
  $(document).ready(async function () {
    // Check if wallet is connected already
    try {
      const pluginTopLevel =
        ".toplevel_page_" + SCFlowPluginSettings.pluginName + "-settings";
      if ($(pluginTopLevel) && $(pluginTopLevel).length > 0) {
        if ($("#popup").length === 0) {
          // Create the popup element
          var popup = $(
            '<div id="popup" class="popup" style="display: none;"><div class="icon__wrapper"><div id="popup-icon"></div></div><span id="popup-text"></span><button id="popup-button">OK</button></div>'
          );
          // Append the popup to the body
          $("body").append(popup);
        }
        // Add listener to connect button
        document.querySelectorAll(".connect-wallet-button").forEach((link) => {
          link.addEventListener("click", async (e) => {
            e.preventDefault();
            displayPop("walletconnected-admin", ".connect-wallet-button");
          });
        });

        // Get public contract data
        getMintPrice(function (data) {
          jQuery("#nft_price_input").text(data);
        });

        getMaxQuantity(function (data) {
          jQuery("#max_quantity_input").val(data);
        });
      }
    } catch (error) {
      console.log(error);
    }
  });
})(jQuery);
