function searchBooks() {
  const searchInput = document.getElementById("search-input").value;

  // Include the current category in the search request
  const categoryParam = currentCategory
    ? `&category=${encodeURIComponent(currentCategory)}`
    : "";

  fetch(
    `../Database/categories.php?search=${encodeURIComponent(
      searchInput
    )}${categoryParam}`,
    {
      method: "GET",
    }
  )
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
