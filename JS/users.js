let currentCategory = ""; // Store the currently selected category

function loadCategory(category, element) {
  currentCategory = category; // Update the current category

  // Fetch books for the selected category
  fetch(`../Database/categories.php?category=${encodeURIComponent(category)}`)
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.text();
    })
    .then((data) => {
      document.getElementById("books-container").innerHTML = data;
    })
    .catch((error) => console.error("Error:", error));
}
