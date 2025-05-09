<?php
session_start();
require_once '../Database/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$owner_id = $_SESSION['user_id'];

// Fetch books listed by the user
$query = "SELECT * FROM books WHERE owner_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_book_id'])) {
    $delete_book_id = $_POST['delete_book_id'];
    $delete_query = "DELETE FROM books WHERE book_id = ? AND owner_id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("ii", $delete_book_id, $owner_id);
    $delete_stmt->execute();
    header("Location: Mybook.php"); // Refresh the page to reflect changes
    exit();
}

// Handle status update request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_id'], $_POST['status'])) {
    $book_id = $_POST['book_id'];
    $status = $_POST['status'];
    $update_query = "UPDATE books SET status = ? WHERE book_id = ? AND owner_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("sii", $status, $book_id, $owner_id);
    $update_stmt->execute();
    header("Location: Mybook.php"); // Refresh the page to reflect changes
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EduCycle - My Books</title>
    <link rel="stylesheet" href="../CSS/Mybook.css" />
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
       
       <a href="users.php" class="nav-link">Home</a>
      </div>
     </div>
 </nav>
    </header>

    <main class="container">
      <div class="my-books-container">
        <div class="page-header">
          <h1>My Books</h1>
          <a href="userAccount.php" class="back-link">← Back to Account</a>
        </div>

        <div class="books-actions">
          <div class="books-count">
            <span><?php echo $result->num_rows; ?> books listed</span>
          </div>
          <a href="addBook_Account.php" class="btn btn-primary">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="16"
              height="16"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <line x1="12" y1="5" x2="12" y2="19"></line>
              <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Add New Book
          </a>
        </div>

        <div class="books-list">
          <?php if ($result->num_rows > 0): ?>
            <?php while ($book = $result->fetch_assoc()): ?>
              <div class="book-item">
                <div class="book-thumbnail">
                  <img src="../uploads/<?php echo htmlspecialchars($book['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="Book cover" />
                  <span class="book-category"><?php echo htmlspecialchars($book['category']); ?></span>
                </div>

                <div class="book-details">
                  <h3 class="book-title"><?php echo htmlspecialchars($book['title']); ?></h3> 
                  <p class="book-author">By <?php echo htmlspecialchars($book['author']); ?></p>
                  <div class="book-meta">
                    <span class="meta-item"><?php echo htmlspecialchars($book['edition']); ?></span>
                    <span class="meta-item"><?php echo htmlspecialchars($book['book_condition']); ?></span>
                    <span class="meta-item status-<?php echo strtolower($book['status']); ?>">
                      <?php echo htmlspecialchars($book['status']); ?>
                    </span>
                  </div>
                </div>

                <div class="book-actions">
                  <a href="bookDetails_Account.php?book_id=<?php echo $book['book_id']; ?>" class="btn btn-outline btn-sm">View</a>
                  
                  <form method="POST" class="status-form">
                    <input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
                    <select name="status" class="status-select" onchange="this.form.submit()">
                      <option value="Active"<?php echo ($book['status'] == 'Active') ? 'selected' : ''; ?>>Active</option>
                      <option value="Not Available" <?php echo ($book['status'] == 'Not Available') ? 'selected' : ''; ?>>Not Available</option>
                    </select>
                  </form>
                  
                  <form method="POST" style="display:inline;">
                    <input type="hidden" name="delete_book_id" value="<?php echo $book['book_id']; ?>">
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                  </form>
                </div>
              </div>
            <?php endwhile; ?>
          <?php else: ?>
            <p>No books found. <a href="addBook_Account.php">Add a new book</a> to get started.</p>
          <?php endif; ?>
        </div>
      </div>
    </main>

    <footer>
      <div class="container">
        <p>&copy; 2025 EduCycle</p>
        <div class="footer-links">
          
        </div>
      </div>
    </footer>
  </body>
</html>