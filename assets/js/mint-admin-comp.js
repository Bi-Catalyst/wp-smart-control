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
      getMintPrice(function (data) {
        jQuery("#nft_price_input").text(data);
      });
      
      getMaxQuantity(function (data) {
        jQuery("#max_quantity_input").val(data);
      });
    }
  });
})(jQuery);
