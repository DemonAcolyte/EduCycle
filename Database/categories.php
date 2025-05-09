<?php
include 'database.php';

if (isset($_GET['search']) || isset($_GET['category'])) {
    $search = isset($_GET['search']) ? "%" . $_GET['search'] . "%" : "%";
    $category = isset($_GET['category']) ? $_GET['category'] : "%";

    $sql = "SELECT b.*, u.username AS owner_name FROM books b LEFT JOIN users u ON b.owner_id = u.user_id WHERE title LIKE ? AND category LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $search, $category);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='book-card'>
                    <img src='../uploads/" . $row['image'] . "' alt='../uploads/default1.png' onerror=\"this.onerror=null; this.src='../uploads/default1.png';\" />
                    <div class='book-info'>
                        <span class='book-category'>" . $row['category'] . "</span>
                        <p class='book-status'>Status: ".$row['status'] . "</p>
                        <h3>" . $row['title'] . "</h3>
                        <p class='book-author'>" . $row['author'] . "</p>
                        <p class='book-condition'>" . $row['book_condition'] . "</p>
                        <p class='book-author'>Submitted by: " . $row['owner_name'] . "</p>
                        <button class='btn btn-primary' onclick=\"window.location.href='bookDetails.php?book_id=" . $row['book_id'] . "'\">Request Exchange</button>
                    </div>
                  </div>";
        }
    } else {
        echo "No books found.";
    }

    $stmt->close();
} else {
    echo "No search query or category provided.";
}

$conn->close();
?>