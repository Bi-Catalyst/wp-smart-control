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
