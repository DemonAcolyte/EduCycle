<?php
session_start();

include '../Database/database.php';
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: ../index.php");
    exit();
}



// Set session timeout duration (e.g., 15 minutes)
$timeout_duration = 900;

// Check if the last activity timestamp is set
if (isset($_SESSION['last_activity'])) {
    // Calculate the session's lifetime
    $elapsed_time = time() - $_SESSION['last_activity'];
    if ($elapsed_time > $timeout_duration) {
        // Destroy the session if the timeout duration is exceeded
        session_unset();
        session_destroy();
    }
}

// Update the last activity timestamp
$_SESSION['last_activity'] = time();


?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EduCycle</title>
    <link rel="stylesheet" href="../CSS/home.css" />
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
       <a href="../desc/about.html" class="nav-link">About</a>
       <a href="../desc/contact.html" class="nav-link">Contact</a>
       <a href="userAccount.php" class="nav-link">Account</a>
      </div>
     </div>
 </nav>
    </header>

    <div class="exchange-container">
      <!-- Sidebar -->
      <aside class="sidebar">
        <div class="sidebar-header">
          <h3>Categories</h3>
          <button class="sidebar-toggle" id="sidebar-toggle">
            <span></span><span></span><span></span>
          </button>
        </div>
        
        <div class="sidebar-content">
          <ul class="category-list">
            <li><a href="#" onclick="location.reload()">All</a></li>
            <li><a href="#" onclick="loadCategory('law', this)">Law & Governance</a></li>
            <li><a href="#" onclick="loadCategory('science', this)">Sciences</a></li>
            <li><a href="#" onclick="loadCategory('math', this)">Mathematics</a></li>
            <li><a href="#" onclick="loadCategory('engineering', this)">Engineering</a></li>
            <li><a href="#" onclick="loadCategory('medicine', this)">Medicine & Health</a></li>
            <li><a href="#" onclick="loadCategory('business', this)">Business & Economics</a></li>
            <li><a href="#" onclick="loadCategory('humanities', this)">Humanities</a></li>
            <li><a href="#" onclick="loadCategory('computer', this)">Computer Science</a></li>
            <li><a href="#" onclick="loadCategory('education', this)">Education</a></li>
            <li><a href="#" onclick="loadCategory('social', this)">Social Sciences</a></li>
          </ul>

          <div class="sidebar-divider"></div>

          <div class="filter-section">
            
            <div class="filter-options">
             
               
             
            </div>
          </div>
        </div>
      </aside>
      <script>
 
</script>
      <!-- Main Content -->
      <main class="main-content">
        <div class="page-header">
          <h1>Educational Book Exchange</h1>
          
        </div>

        <div class="search-bar">
          <input type="text" id="search-input" placeholder="Search for books..." oninput="searchBooks()" />
          
        </div>

        <div id="books-container" class="books-container">
          <!-- Existing books will be loaded here -->
          <?php
          include '../Database/database.php';
          $sql = "SELECT b.*, u.username AS owner_name FROM books b LEFT JOIN users u ON b.owner_id = u.user_id ORDER BY b.book_id DESC LIMIT 6";
          $result = $conn->query($sql);
          if ($result->num_rows > 0) {
              while($row = $result->fetch_assoc()) {
                  echo "<div class='book-card'>
                          <img src='../uploads/" . $row['image'] . "' alt='../uploads/default1.png'' onerror=\"this.onerror=null; this.src='../uploads/default1.png';\" />
                          <div class='book-info'>
                              <span class='book-category'>" . $row['category'] . "</span>
                              <p class='book-status'>Status: ".$row['status'] . "</p>
                              <h3>" . $row['title'] . "</h3>
                              <p class='book-author'>" . $row['author'] . "</p>
                              <p class='book-condition'>" . $row['book_condition'] . "</p>
                              <p class='book-author'>Submitted by: <strong>" . $row['owner_name'] . "</strong></p>
                              <button class='btn btn-primary' onclick=\"window.location.href='bookDetails.php?book_id=" . $row['book_id'] . "'\">Request Exchange</button>
                          </div>
                        </div>";
              }
          } else {
              echo "No books available.";
          }
          $conn->close();
          ?>
        </div>
      </main>
    </div>

    <footer>
      <div class="container">
        <p>&copy; 2025 EduCycle</p>
        <div class="footer-links">
          
        </div>
      </div>
    </footer>

    <script src="../JS/users.js"></script>
    <script src="../JS/script.js"></script>
    <script src="../JS/searchBar.js"></script>
  </body>
</html>