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
      
      getDiscountPercentage(function (data) {
        jQuery("#mint_discount_input").val(data);
      });

      getMaxQuantity(function (data) {
        jQuery("#max_quantity_input").val(data);
      });

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
        setDiscountPercentage(discount);
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
