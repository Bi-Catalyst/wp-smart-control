jQuery(document).ready(function ($) {
  // Trigger function button click event
  $(".trigger-function").on("click", function (e) {
    e.preventDefault();
    const settingKey = $(this).data("setting-key");
    var functionName = settingKey.replace("mint-craft-function_", "");

    // Get the contract address and ABI from the myMintPluginSettings object
    var contractAddress = myMintPluginSettings.contractAddress;
    var contractABI = myMintPluginSettings.contractABI;

    // Get the state mutability from the data attribute
    var stateMutability = $(this).data("state-mutability");
    // TODO show error pop up
    // Prepare the function call based on the state mutability
    var args = [];
    var $functionContainer = $(this).closest(".function-field");
    $functionContainer
      .find('.function-inputs input[id^="' + settingKey + '_"]')
      .each(function () {
        let inputValue = $(this).val();
        const inputType = $(this).data("type");

        // Convert the input value based on the input type
        if (inputType === "uint256") {
          inputValue = Number(inputValue);
        }

        args.push(inputValue);
      });
    switch (stateMutability) {
      case "view":
        // Read contract function call
        var request = {
          address: contractAddress,
          abi: contractABI,
          functionName: functionName,
        };
        if (args.length > 0) {
          request.args = args;
        }
        // Call readContract and handle the response
        readContract(request)
          .then(function (response) {
            // Example: Update the input field value with the data
            // $("#" + settingKey).val(response);

            // Example: Display the result in a separate element
            $("#" + settingKey + "_result").val(response);
          })
          .catch(function (error) {
            console.log(error);
          });
        break;

      case "payable":
      case "nonpayable":
        // Prepare the writeContract request
        var value = ""; // Initialize the value as an empty string

        if (stateMutability === "payable") {
          value = ethers.parseUnits(
            (
              args[0] * Number.parseFloat(myMintPluginSettings.mintPrice)
            ).toString(),
            "ether"
          );
        }

        var request = {
          address: contractAddress,
          abi: contractABI,
          functionName: functionName,
          args: args,
        };
        // Add the value parameter if it is not empty
        if (value !== "") {
          request.value = value;
        }

        // Call prepareWriteContract and handle the response
        prepareWriteContract(request)
          .then(function (preparedConfig) {
            var writeRequest = preparedConfig.request;

            // Call writeContract and handle the response
            writeContract(writeRequest)
              .then(function (response) {
                var hash = response.hash; // Get the transaction hash

                // Do something with the transaction hash if needed

                // Example: Update the input field value with the hash
                $("#" + settingKey).val(hash);

                // Example: Display the transaction hash in a popup
                var explorerURL = "";
                if (myMintPluginSettings.activeChain === "80001") {
                  explorerURL = "https://mumbai.polygonscan.com/tx/" + hash;
                } else if (myMintPluginSettings.activeChain === "137") {
                  explorerURL = "https://polygonscan.com/tx/" + hash;
                }

                if (explorerURL) {
                  var popupHTML = `
                    <div class="transaction-popup">
                      <div class="transaction-popup-content">
                        <p>Transaction submitted successfully.</p>
                        <p>Check the status <a href="${explorerURL}" target="_blank">here</a>.</p>
                      </div>
                    </div>
                  `;

                  $("body").append(popupHTML);
                  setTimeout(function () {
                    $(".transaction-popup").fadeOut(500, function () {
                      $(this).remove();
                    });
                  }, 3000);
                }
              })
              .catch(function (error) {
                console.log(error);
              });
          })
          .catch(function (error) {
            console.log(error);
          });
        break;

      default:
        console.log("Invalid state mutability");
        break;
    }
  });
});
