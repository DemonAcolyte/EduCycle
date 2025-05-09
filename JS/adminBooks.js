document.addEventListener("DOMContentLoaded", function () {
  const selectAll = document.getElementById("select-all");
  const bookSelects = document.querySelectorAll(".book-select");
  const deleteSelected = document.getElementById("delete-selected");
  const deleteButtons = document.querySelectorAll(".delete-book");
  const deleteModal = document.getElementById("delete-modal");
  const cancelDelete = document.getElementById("cancel-delete");
  const confirmDelete = document.getElementById("confirm-delete");
  const closeModal = document.querySelector(".close-modal");
  const deleteMessage = document.getElementById("delete-message");

  let selectedBooks = [];
  let currentBookId = null;
  let isMultiDelete = false;

  // Select all checkbox
  selectAll.addEventListener("change", function () {
    bookSelects.forEach((checkbox) => {
      checkbox.checked = this.checked;
      const bookId = checkbox.getAttribute("data-book-id");

      if (this.checked) {
        if (!selectedBooks.includes(bookId)) {
          selectedBooks.push(bookId);
        }
      } else {
        selectedBooks = selectedBooks.filter((id) => id !== bookId);
      }
    });

    updateDeleteButton();
  });

  // Individual book checkboxes
  bookSelects.forEach((checkbox) => {
    checkbox.addEventListener("change", function () {
      const bookId = this.getAttribute("data-book-id");

      if (this.checked) {
        if (!selectedBooks.includes(bookId)) {
          selectedBooks.push(bookId);
        }
      } else {
        selectedBooks = selectedBooks.filter((id) => id !== bookId);
        selectAll.checked = false;
      }

      updateDeleteButton();
    });
  });

  // Update delete selected button state
  function updateDeleteButton() {
    deleteSelected.disabled = selectedBooks.length === 0;
  }

  // Delete selected books button
  deleteSelected.addEventListener("click", function () {
    if (selectedBooks.length > 0) {
      isMultiDelete = true;
      deleteMessage.textContent = `Are you sure you want to delete ${selectedBooks.length} selected books? This action cannot be undone.`;
      deleteModal.classList.add("show");
    }
  });

  // Individual delete buttons
  deleteButtons.forEach((button) => {
    button.addEventListener("click", function () {
      currentBookId = this.getAttribute("data-book-id");
      isMultiDelete = false;
      deleteMessage.textContent =
        "Are you sure you want to delete this book? This action cannot be undone.";
      deleteModal.classList.add("show");
    });
  });

  // Hide modal when cancel is clicked
  cancelDelete.addEventListener("click", function () {
    deleteModal.classList.remove("show");
    if (isMultiDelete) {
      isMultiDelete = false;
    } else {
      currentBookId = null;
    }
  });

  // Hide modal when X is clicked
  closeModal.addEventListener("click", function () {
    deleteModal.classList.remove("show");
    if (isMultiDelete) {
      isMultiDelete = false;
    } else {
      currentBookId = null;
    }
  });

  // Delete books when confirmed
  confirmDelete.addEventListener("click", function () {
    if (isMultiDelete && selectedBooks.length > 0) {
      // AJAX request to delete multiple books from the database
      fetch("adminBooks.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: `action=deleteSelectedBooks&book_ids[]=${selectedBooks.join(
          "&book_ids[]="
        )}`,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            selectedBooks.forEach((bookId) => {
              const bookRow = document
                .querySelector(`.book-select[data-book-id="${bookId}"]`)
                .closest("tr");
              bookRow.style.opacity = "0";
              setTimeout(() => {
                bookRow.remove();
              }, 300);
            });

            // Update book count
            const badge = document.querySelector(".badge");
            const currentCount = parseInt(badge.textContent);
            badge.textContent = `${currentCount - selectedBooks.length} total`;

            // Reset selected books
            selectedBooks = [];
            selectAll.checked = false;
            updateDeleteButton();
          } else {
            alert("Error deleting books: " + (data.error || "Unknown error"));
          }
        })
        .catch((error) => {
          alert("Error deleting books: " + error.message);
        })
        .finally(() => {
          deleteModal.classList.remove("show");
          isMultiDelete = false;
        });
    } else if (!isMultiDelete && currentBookId) {
      // AJAX request to delete the book from the database
      fetch("adminBooks.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: `action=deleteBook&book_id=${currentBookId}`,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            const bookRow = document
              .querySelector(`.delete-book[data-book-id="${currentBookId}"]`)
              .closest("tr");
            bookRow.style.opacity = "0";
            setTimeout(() => {
              bookRow.remove();

              // Update book count
              const badge = document.querySelector(".badge");
              const currentCount = parseInt(badge.textContent);
              badge.textContent = `${currentCount - 1} total`;

              currentBookId = null;
            }, 300);
          } else {
            alert("Error deleting book: " + (data.error || "Unknown error"));
          }
        })
        .catch((error) => {
          alert("Error deleting book: " + error.message);
        })
        .finally(() => {
          deleteModal.classList.remove("show");
        });
    }
  });

  // Close modal when clicking outside
  window.addEventListener("click", function (event) {
    if (event.target === deleteModal) {
      deleteModal.classList.remove("show");
      if (isMultiDelete) {
        isMultiDelete = false;
      } else {
        currentBookId = null;
      }
    }
  });

  const categoryDropdown = document.querySelector(
    ".admin-filter select:nth-of-type(1)"
  );
  const statusDropdown = document.querySelector("#status-filter select");
  const searchInput = document.querySelector(".admin-search input");

  if (!categoryDropdown || !statusDropdown || !searchInput) {
    console.error(
      "Dropdowns or search input not found. Check your HTML structure or selectors."
    );
    return;
  }

  // Handle category and status filter changes
  function applyFilters() {
    const selectedCategory = categoryDropdown.value;
    const selectedStatus = statusDropdown.value;
    const searchQuery = searchInput.value.trim();

    // Fetch filtered books via AJAX
    fetch(
      `adminBooks.php?category=${selectedCategory}&status=${selectedStatus}&search=${encodeURIComponent(
        searchQuery
      )}`
    )
      .then((response) => response.text())
      .then((html) => {
        updateTable(html);
      })
      .catch((error) => {
        console.error("Error fetching filtered books:", error);
      });
  }

  categoryDropdown.addEventListener("change", applyFilters);
  statusDropdown.addEventListener("change", applyFilters);
  searchInput.addEventListener("input", applyFilters);

  function updateTable(html) {
    // Replace the table body with the filtered results
    const parser = new DOMParser();
    const doc = parser.parseFromString(html, "text/html");
    const newTableBody = doc.querySelector(".admin-table tbody");
    const currentTableBody = document.querySelector(".admin-table tbody");

    if (newTableBody) {
      currentTableBody.innerHTML = newTableBody.innerHTML;

      // Update the total count badge
      const newBadge = doc.querySelector(".badge").textContent;
      document.querySelector(".badge").textContent = newBadge;

      // Reinitialize event listeners for new rows
      initializeEventListeners();
    } else {
      console.error("Failed to update table: No table body found in response.");
    }
  }

  function initializeEventListeners() {
    const bookSelects = document.querySelectorAll(".book-select");
    const deleteButtons = document.querySelectorAll(".delete-book");
    const selectAll = document.getElementById("select-all");
    const deleteSelected = document.getElementById("delete-selected");

    // Reinitialize select all checkbox
    selectAll.addEventListener("change", function () {
      bookSelects.forEach((checkbox) => {
        checkbox.checked = this.checked;
        const bookId = checkbox.getAttribute("data-book-id");

        if (this.checked) {
          if (!selectedBooks.includes(bookId)) {
            selectedBooks.push(bookId);
          }
        } else {
          selectedBooks = selectedBooks.filter((id) => id !== bookId);
        }
      });

      updateDeleteButton();
    });

    // Reinitialize individual book checkboxes
    bookSelects.forEach((checkbox) => {
      checkbox.addEventListener("change", function () {
        const bookId = this.getAttribute("data-book-id");

        if (this.checked) {
          if (!selectedBooks.includes(bookId)) {
            selectedBooks.push(bookId);
          }
        } else {
          selectedBooks = selectedBooks.filter((id) => id !== bookId);
          selectAll.checked = false;
        }

        updateDeleteButton();
      });
    });

    // Reinitialize delete buttons
    deleteButtons.forEach((button) => {
      button.addEventListener("click", function () {
        currentBookId = this.getAttribute("data-book-id");
        isMultiDelete = false;
        deleteMessage.textContent =
          "Are you sure you want to delete this book? This action cannot be undone.";
        deleteModal.classList.add("show");
      });
    });

    // Reinitialize delete selected button
    deleteSelected.addEventListener("click", function () {
      if (selectedBooks.length > 0) {
        isMultiDelete = true;
        deleteMessage.textContent = `Are you sure you want to delete ${selectedBooks.length} selected books? This action cannot be undone.`;
        deleteModal.classList.add("show");
      }
    });
  }

  function updateDeleteButton() {
    deleteSelected.disabled = selectedBooks.length === 0;
  }

  initializeEventListeners();
});
