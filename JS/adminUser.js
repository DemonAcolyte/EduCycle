document.addEventListener("DOMContentLoaded", function () {
  const selectAll = document.getElementById("select-all");
  const rowCheckboxes = document.querySelectorAll(".row-checkbox");
  const deleteSelectedBtn = document.getElementById("delete-selected");
  const deleteButtons = document.querySelectorAll(".delete-btn");
  const deleteModal = document.getElementById("delete-modal");
  const cancelDelete = document.getElementById("cancel-delete");
  const confirmDelete = document.getElementById("confirm-delete");
  const closeModal = document.querySelector(".close-modal");
  const searchInput = document.querySelector(".search-bar input");
  const userRows = document.querySelectorAll(".data-table tbody tr");
  let selectedUserIds = [];

  // Select all checkbox
  selectAll.addEventListener("change", function () {
    rowCheckboxes.forEach((checkbox) => {
      checkbox.checked = this.checked;
      const userId = checkbox.getAttribute("data-user-id");

      if (this.checked) {
        if (!selectedUserIds.includes(userId)) {
          selectedUserIds.push(userId);
        }
      } else {
        selectedUserIds = selectedUserIds.filter((id) => id !== userId);
      }
    });

    updateDeleteButtonState();
  });

  // Individual row checkboxes
  rowCheckboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", function () {
      const userId = this.getAttribute("data-user-id");

      if (this.checked) {
        if (!selectedUserIds.includes(userId)) {
          selectedUserIds.push(userId);
        }
      } else {
        selectedUserIds = selectedUserIds.filter((id) => id !== userId);
      }

      // Update "select all" checkbox
      selectAll.checked =
        rowCheckboxes.length ===
        document.querySelectorAll(".row-checkbox:checked").length;

      updateDeleteButtonState();
    });
  });

  // Update delete button state
  function updateDeleteButtonState() {
    deleteSelectedBtn.disabled = selectedUserIds.length === 0;
  }

  // Delete selected button
  deleteSelectedBtn.addEventListener("click", function () {
    if (selectedUserIds.length > 0) {
      openDeleteModal("selected");
    }
  });

  // Individual delete buttons
  deleteButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const userId = this.getAttribute("data-user-id");
      selectedUserIds = [userId];
      openDeleteModal("single");
    });
  });

  // Open delete modal
  function openDeleteModal(type) {
    const modalBody = document.querySelector(".modal-body p");

    if (type === "selected") {
      modalBody.textContent = `Are you sure you want to delete ${selectedUserIds.length} selected user(s)? This action cannot be undone.`;
    } else {
      modalBody.textContent =
        "Are you sure you want to delete this user? This action cannot be undone.";
    }

    deleteModal.classList.add("show");
  }

  // Hide modal when cancel is clicked
  cancelDelete.addEventListener("click", function () {
    deleteModal.classList.remove("show");
  });

  // Hide modal when X is clicked
  closeModal.addEventListener("click", function () {
    deleteModal.classList.remove("show");
  });

  // Delete users when confirmed
  confirmDelete.addEventListener("click", function () {
    if (selectedUserIds.length > 0) {
      const action =
        selectedUserIds.length === 1 ? "deleteUser" : "deleteSelectedUsers";
      const formData = new URLSearchParams();
      formData.append("action", action);

      if (selectedUserIds.length === 1) {
        formData.append("user_id", selectedUserIds[0]);
      } else {
        selectedUserIds.forEach((userId) =>
          formData.append("user_ids[]", userId)
        );
      }

      fetch("adminUsers.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: formData.toString(),
      })
        .then((response) => response.json())
        .then((result) => {
          if (result.success) {
            selectedUserIds.forEach((userId) => {
              const userRow = document
                .querySelector(`.row-checkbox[data-user-id="${userId}"]`)
                .closest("tr");
              userRow.style.opacity = "0";
              setTimeout(() => {
                userRow.remove();
              }, 300);
            });

            // Update counts
            const totalCount = document.querySelector(".count-badge");
            const currentCount = parseInt(totalCount.textContent);
            totalCount.textContent = `${
              currentCount - selectedUserIds.length
            } total`;

            // Reset selected user IDs
            selectedUserIds = [];
            selectAll.checked = false;
            updateDeleteButtonState();

            deleteModal.classList.remove("show");
          } else {
            alert("Error deleting users: " + result.error);
          }
        })
        .catch((error) => {
          console.error("Error:", error);
        });
    }
  });

  // Close modal when clicking outside
  window.addEventListener("click", function (event) {
    if (event.target === deleteModal) {
      deleteModal.classList.remove("show");
    }
  });

  // Search functionality
  searchInput.addEventListener("input", function () {
    const searchTerm = this.value.toLowerCase();

    userRows.forEach((row) => {
      const username = row
        .querySelector(".user-name")
        .textContent.toLowerCase();
      const email = row
        .querySelector("td:nth-child(3)")
        .textContent.toLowerCase();

      if (username.includes(searchTerm) || email.includes(searchTerm)) {
        row.style.display = "";
      } else {
        row.style.display = "none";
      }
    });
  });
});
document.addEventListener("DOMContentLoaded", function () {
  const roleFilter = document.querySelector(".filter-select");
  const userRows = document.querySelectorAll(".data-table tbody tr");

  // Role filter functionality
  roleFilter.addEventListener("change", function () {
    const selectedRole = this.value.toLowerCase();

    userRows.forEach((row) => {
      const userRole = row
        .querySelector("td:nth-child(4)")
        .textContent.toLowerCase();

      if (selectedRole === "all roles" || userRole === selectedRole) {
        row.style.display = "";
      } else {
        row.style.display = "none";
      }
    });
  });
});
