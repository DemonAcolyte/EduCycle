<?php
include '../Database/database.php';

session_start();
if (!isset($_SESSION['user_id'])) {
  // Redirect to login page if not logged in
  header("Location: ../index.php");
  exit();
}
// Fetch all books or filter by category, status, or search query
$categoryFilter = isset($_GET['category']) && $_GET['category'] !== 'all' ? $_GET['category'] : null;
$statusFilter = isset($_GET['status']) && $_GET['status'] !== 'all' ? $_GET['status'] : null;
$searchQuery = isset($_GET['search']) ? $_GET['search'] : null;

$query = "SELECT books.book_id, books.title, books.image, books.category, books.status, books.created_at, users.username 
          FROM books 
          LEFT JOIN users ON books.owner_id = users.user_id";

$conditions = [];
$params = [];
$types = '';

if ($categoryFilter) {
    $conditions[] = "books.category = ?";
    $params[] = $categoryFilter;
    $types .= 's';
}

if ($statusFilter) {
    $conditions[] = "books.status = ?";
    $params[] = $statusFilter;
    $types .= 's';
}

if ($searchQuery) {
    $conditions[] = "books.title LIKE ?";
    $params[] = '%' . $searchQuery . '%';
    $types .= 's';
}

if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($query);
}

if (!$result) {
    die("Error executing query: " . $conn->error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'deleteBook') {
        $bookId = intval($_POST['book_id']);
        $deleteQuery = "DELETE FROM books WHERE book_id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("i", $bookId);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $conn->error]);
        }
        exit;
    } elseif ($_POST['action'] === 'deleteSelectedBooks') {
        $bookIds = $_POST['book_ids'];
        $placeholders = implode(',', array_fill(0, count($bookIds), '?'));
        $deleteQuery = "DELETE FROM books WHERE book_id IN ($placeholders)";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param(str_repeat('i', count($bookIds)), ...$bookIds);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $conn->error]);
        }
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EduCycle Admin - Books Management</title>
    <link rel="stylesheet" href="../CSS/admin.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
      rel="stylesheet"
    />
  </head>
  <body>
    <div class="admin-layout">
      <!-- Admin Sidebar -->
      <aside class="admin-sidebar">
        <div class="admin-logo">
          <h1>EduCycle</h1>
          
        </div>

        <nav class="admin-nav">
          <ul>
            
            <li>
              <a href="#" class="active">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                  <path
                    d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"
                  ></path>
                </svg>
                Books
              </a>
            </li>
            <li>
              <a href="adminUsers.php">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                  <circle cx="9" cy="7" r="4"></circle>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                Users
              </a>
            </li>
            
            
          </ul>
        </nav>

        <div class="admin-sidebar-footer">
          <a href="../logout.php" class="btn btn-outline btn-sm">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="20"
              height="20"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1-2-2h4"></path>
              <polyline points="16 17 21 12 16 7"></polyline>
              <line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
            Exit Admin
          </a>
        </div>
      </aside>

      <!-- Admin Content -->
      <main class="admin-content">
        <header class="admin-header">
          <div class="admin-header-title">
            <h1>Books Management</h1>
          </div>

          <div class="admin-header-actions">
            <div class="admin-search">
              <input type="text" placeholder="Search books..." />
              <button class="search-btn">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="18"
                  height="18"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <circle cx="11" cy="11" r="8"></circle>
                  <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
              </button>
            </div>

            <div class="admin-user-menu">
              <span>Admin User</span>
              <div class="admin-avatar"><span>A</span></div>
            </div>
          </div>
        </header>

        <div class="admin-container">
          <div class="admin-card">
            <div class="admin-card-header">
              <div class="admin-card-title">
                <h2>All Books</h2>
                <span class="badge"><?php echo $result->num_rows; ?> total</span>
              </div>

              <div class="admin-card-actions">
                <div class="admin-filter">
                  <select>
                    <option value="all">All Categories</option>
                    <option value="law">Law & Governance</option>
                            <option value="science">Sciences</option>
                            <option value="math">Mathematics</option>
                            <option value="engineering">Engineering</option>
                            <option value="medicine">Medicine & Health</option>
                            <option value="business">Business & Economics</option>
                            <option value="humanities">Humanities</option>
                            <option value="computer">Computer Science</option>
                            <option value="education">Education</option>
                            <option value="social">Social Sciences</option>
                  </select>
                </div>

                <div class="admin-filter" id="status-filter">
                  <select>
                    <option value="all">All Status</option>
                    <option value="Active">Active</option>
                    <option value="Not Available">Not Available</option>
                    
                  </select>
                </div>
              </div>
            </div>

            <div class="admin-table-container">
              <table class="admin-table">
                <thead>
                  <tr>
                    <th>
                      <input type="checkbox" id="select-all" />
                    </th>
                    <th>Book</th>
                    <th>Category</th>
                    <th>Owner</th>
                    <th>Listed Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($result->num_rows > 0): ?>
                    <?php while ($book = $result->fetch_assoc()): ?>
                      
                      <tr>
                        <td>
                          <input
                            type="checkbox"
                            class="book-select"
                            data-book-id="<?php echo $book['book_id']; ?>"
                          />
                        </td>
                        <td>
                          <div class="book-info">
                            <img
                              src="../uploads/<?php echo htmlspecialchars($book['image']); ?>"
                              
                              class="book-thumbnail"
                              
                            />
                            <div>
                              <div class="book-title">
                                <?php echo htmlspecialchars($book['title']); ?>
                              </div>
                              <div class="book-author">
                                <!-- Author information can be added here if available -->
                              </div>
                            </div>
                          </div>
                        </td>
                        <td><?php echo htmlspecialchars($book['category']); ?></td>
                        <td><?php echo htmlspecialchars($book['username'] ?? 'Unknown'); ?></td>
                        <td><?php echo htmlspecialchars($book['created_at']); ?></td>
                        <td>
                          <span class="status-badge status-<?php echo strtolower($book['status']); ?>">
                            <?php echo htmlspecialchars($book['status']); ?>
                          </span>
                        </td>
                        <td>
                          <div class="table-actions">
                            <button class="btn-icon" title="View Details" onclick="window.location.href='bookDetails_admin.php?book_id=<?php echo $book['book_id']; ?>'">
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
                                <path
                                  d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"
                                ></path>
                                <circle cx="12" cy="12" r="3"></circle>
                              </svg>
                            </button>
                            <button
                              class="btn-icon delete-book"
                              title="Delete Book"
                              data-book-id="<?php echo $book['book_id']; ?>"
                            >
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
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path
                                  d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"
                                ></path>
                              </svg>
                            </button>
                          </div>
                        </td>
                      </tr>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="7">No books found.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>

            <div class="admin-card-footer">
              <div class="bulk-actions">
                <button
                  class="btn btn-danger btn-sm"
                  id="delete-selected"
                  disabled
                >
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
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path
                      d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"
                    ></path>
                  </svg>
                  Delete Selected
                </button>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal" id="delete-modal">
      <div class="modal-content">
        <div class="modal-header">
          <h2>Confirm Deletion</h2>
          <button class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="warning-icon">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="24"
              height="24"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path
                d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"
              ></path>
              <line x1="12" y1="9" x2="12" y2="13"></line>
              <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
          </div>
          <p id="delete-message">
            Are you sure you want to delete this book? This action cannot be
            undone.
          </p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-outline" id="cancel-delete">Cancel</button>
          <button class="btn btn-danger" id="confirm-delete">Delete</button>
        </div>
      </div>
    </div>

    <script src="../JS/adminBooks.js"></script>
  </body>
</html>
