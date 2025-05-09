<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../Database/database.php';

$book_id = isset($_GET['book_id']) ? intval($_GET['book_id']) : 0;
$book = null;

if ($book_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM books WHERE book_id = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $book_id);
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $book = $result->fetch_assoc();
    }
    $stmt->close();
} else {
    echo "Debug: Invalid Book ID passed.";
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EduCycle</title>
    <link rel="stylesheet" href="../CSS/bookDetails.css" />
    <link rel="stylesheet" href="../CSS/styles.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
      rel="stylesheet"
    />
  </head>
  <body>
  <header>
    <nav class="navigation-bar">
    
    <div class="navdiv">
     <div class="logo">EduCycle</div>
 
      <div class="link">
       <a href="userAccount.php" class="nav-link">Account</a>
      </div>
     </div>
 </nav>
    </header>

    <main class="container">
      <div class="book-details-container">
        <div class="page-header">
          <a href="Mybook.php" class="back-link">← Back to My Books</a>
        </div>

        <?php if ($book): ?>
        <div class="book-details">
        
          <div class="book-cover">
            <img id="book-image" src="../uploads/<?= htmlspecialchars($book['image']) ?>" alt="" onerror="this.onerror=null; this.src='../uploads/default1.png';" />
            <span id="book-category" class="book-category"><?= htmlspecialchars($book['category']) ?></span>
          </div>

          <div class="book-info">
            <h1 id="book-title" class="book-title"><?= htmlspecialchars($book['title']) ?></h1>
            <p id="book-author" class="book-author"><?= htmlspecialchars($book['author']) ?></p>

            <div class="book-meta">
              <div class="meta-item">
                <span class="meta-label">Condition:</span>
                <span id="book-condition" class="meta-value"><?= htmlspecialchars($book['book_condition']) ?></span>
              </div>
              <div class="meta-item">
                <span class="meta-label">Category:</span>
                <span id="book-category-meta" class="meta-value"><?= htmlspecialchars($book['category']) ?></span>
              </div>
            </div>

            <div class="contact-info">
              <h3>Contact Information</h3>
              <div class="contact-method">
                <span class="contact-label">Contact:</span>
                <span class="contact-value"><?= htmlspecialchars($book['contact_info']) ?></span>
              </div>
              <div class="contact-method">
                <span class="contact-label">Description:</span>
                <span class="contact-value"><?= htmlspecialchars($book['description']) ?></span>
              </div>
            </div>

            <div class="action-buttons">
              
              <a href="users.php" class="btn btn-outline">Back to Listings</a>
            </div>
          </div>
        </div>
        <?php else: ?>
        <p>Book not found. Debug: Book ID passed was <strong><?= htmlspecialchars($book_id) ?></strong>.</p>
        <?php endif; ?>
      </div>
    </main>

    <footer>
      <div class="container">
        <p>&copy; 2025 BookSwap Educational Exchange</p>
        <div class="footer-links">
          <a href="#">Terms</a>
          <a href="#">Privacy</a>
          <a href="#">Help</a>
        </div>
      </div>
    </footer>
  </body>
</html>
