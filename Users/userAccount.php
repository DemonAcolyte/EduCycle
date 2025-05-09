<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session and include database connection
session_start();
include '../Database/database.php';

// Handle logout request
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: ../index.php");
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Fetch user details from the database
$user_id = $_SESSION['user_id'];
$query = "SELECT username, email, created_at FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Fetch additional stats (e.g., books listed)
$query_stats = "SELECT COUNT(*) AS books_listed FROM books WHERE owner_id = ?";
$stmt_stats = $conn->prepare($query_stats);
if (!$stmt_stats) {
    die("Prepare failed: " . $conn->error);
}
$stmt_stats->bind_param("i", $user_id);
$stmt_stats->execute();
$result_stats = $stmt_stats->get_result();
$stats = $result_stats->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EduCycle - My Account</title>
    <link rel="stylesheet" href="../CSS/userAccount.css" />
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
      <div class="account-container">
        <div class="page-header">
          <h1>My Account</h1>
        </div>

        <div class="account-card">
          <div class="account-avatar">
            <div class="avatar-circle">
              <span class="avatar-initials">
                <?php echo strtoupper(substr($user['username'], 0, 2)); ?>
              </span>
            </div>
          </div>

          <div class="account-details">
            <div class="account-info">
              <div class="info-item">
                <span class="info-label">Username</span>
                <span class="info-value"><?php echo htmlspecialchars($user['username']); ?></span>
              </div>

              <div class="info-item">
                <span class="info-label">Account Created</span>
                <span class="info-value"><?php echo date("F j, Y", strtotime($user['created_at'])); ?></span>
              </div>

              <div class="info-item">
                <span class="info-label">Email</span>
                <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
              </div>
            </div>

            <div class="account-stats">
              <div class="stat-item">
                <span class="stat-value"><?php echo $stats['books_listed']; ?></span>
                <span class="stat-label">Books Listed</span>
              </div>
            </div>
          </div>
        </div>

        <div class="account-actions">
          <a href="Mybook.php" class="btn btn-outline">My Books</a>
          <form method="POST" style="display:inline;">
            <button type="submit" name="logout" class="btn btn-primary">Logout</button>
          </form>
        </div>
      </div>
    </main>

    <footer>
      <div class="container">
        <p>&copy; 2025 EduCycle </p>
        <div class="footer-links">
          
        </div>
      </div>
    </footer>
  </body>
</html>
