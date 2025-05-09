document.addEventListener("DOMContentLoaded", function () {
  // Function to dynamically add a new book card
  function addBookCard(category, title, condition, author, image) {
    const booksContainer = document.getElementById("books-container");
    const bookCard = document.createElement("div");
    bookCard.className = "book-card";
    bookCard.innerHTML = `
      <a href="bookDetails.html?category=${encodeURIComponent(
        category
      )}&title=${encodeURIComponent(title)}&condition=${encodeURIComponent(
      condition
    )}&author=${encodeURIComponent(author)}&image=${encodeURIComponent(image)}">
        <img src="../uploads/${image}" alt="Book cover" />
        <div class="book-info">
          <span class="book-category">${category}</span>
          <h3>${title}</h3>
          <p class="book-author">${author}</p>
          <p class="book-condition">${condition}</p>
          <button class="btn btn-primary request-exchange-button">Request Exchange</button>
        </div>
      </a>
    `;
    booksContainer.appendChild(bookCard);

    // Add event listener for the "Request Exchange" button
    bookCard
      .querySelector(".request-exchange-button")
      .addEventListener("click", function (event) {
        event.preventDefault();
        window.location.href = `requestExchange.html?category=${encodeURIComponent(
          category
        )}&title=${encodeURIComponent(title)}&condition=${encodeURIComponent(
          condition
        )}&author=${encodeURIComponent(author)}&image=${encodeURIComponent(
          image
        )}`;
      });
  }

  function openAddBookPage() {
    window.location.href = "addBook.php";
  }

  function loadCategory(category, element, conditions) {
    console.log(conditions); // Check if conditions are being passed correctly
    fetch(
      `../Database/categories.php?category=${encodeURIComponent(
        category
      )}&conditions=${encodeURIComponent(JSON.stringify(conditions))}`
    )
      .then((response) => response.text())
      .then((data) => {
        document.getElementById("books-container").innerHTML = data;
      })
      .catch((error) => console.error("Error fetching books:", error));
  }

  // Expose the functions to the global scope
  window.openAddBookPage = openAddBookPage;
  window.loadCategory = loadCategory;
  window.addBookCard = addBookCard;
});

function filterBooks() {
  const likeNew = document.getElementById("like-new").checked;
  const veryGood = document.getElementById("very-good").checked;
  const good = document.getElementById("good").checked;
  const acceptable = document.getElementById("acceptable").checked;

  let conditions = [];
  if (likeNew) conditions.push("Like New");
  if (veryGood) conditions.push("Very Good");
  if (good) conditions.push("Good");
  if (acceptable) conditions.push("Acceptable");

  const xhr = new XMLHttpRequest();
  xhr.open("POST", "filter_books.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onload = function () {
    if (this.status === 200) {
      document.getElementById("books-container").innerHTML = this.responseText;
    }
  };
  xhr.send("conditions=" + JSON.stringify(conditions));
}
