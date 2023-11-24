(function ($) {
  $(document).ready(async function () {
    // Check if wallet is connected already
    try {
      if (
        $(".toplevel_page_mint-craft-settings") &&
        $(".toplevel_page_mint-craft-settings").length > 0
      ) {
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
