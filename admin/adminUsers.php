<?php
include '../Database/database.php';

session_start();

include '../Database/database.php';
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: ../index.php");
    exit();
}

// Handle delete requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'deleteUser') {
        $userId = intval($_POST['user_id']);
        $deleteQuery = "DELETE FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("i", $userId);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $conn->error]);
        }
        exit;
    } elseif ($_POST['action'] === 'deleteSelectedUsers') {
        $userIds = $_POST['user_ids'];
        $placeholders = implode(',', array_fill(0, count($userIds), '?'));
        $deleteQuery = "DELETE FROM users WHERE user_id IN ($placeholders)";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param(str_repeat('i', count($userIds)), ...$userIds);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $conn->error]);
        }
        exit;
    }
}

// Fetch all users
$query = "SELECT users.user_id, users.username, users.email, users.role, users.created_at, 
                 (SELECT COUNT(*) FROM books WHERE books.owner_id = users.user_id) AS books_listed 
          FROM users";
$result = $conn->query($query);

if (!$result) {
    die("Error executing query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookSwap - Admin User Management</title>
    <link rel="stylesheet" href="../CSS/adminUsers.css">
    <link rel="stylesheet" href="../CSS/admin.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <h1>EduCycle</h1>
               
            </div>
            <div class="sidebar-menu">
               
                <a href="adminBooks.php" class="sidebar-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                    <span>Books</span>
                </a>
                <a href="#" class="sidebar-item active">
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
                    <span>Users</span>
                </a>
                
            </div>
            <div class="admin-sidebar-footer">
                <a href="../logout.php" class="btn btn-outline btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1-2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    <span>Exit Admin</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="admin-header">
                <h1>Users Management</h1>
                <div class="admin-header-actions">
                    <div class="search-bar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        <input type="text" placeholder="Search users...">
                    </div>
                    <div class="admin-user">
                        <span>Admin User</span>
                        <div class="admin-avatar">
                            <span>A</span>
                        </div>
                    </div>
                </div>
            </header>

            <div class="content-container">
                <div class="content-header">
                    <div class="content-title">
                        <h2>All Users</h2>
                        <span class="count-badge"><?php echo $result->num_rows; ?> total</span>
                    </div>
                    <div class="content-filters">
                        <select class="filter-select">
                            <option>All Roles</option>
                            <option>Admin</option>
                            <option>User</option>

                        </select>
                        
                    </div>
                </div>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="checkbox-cell">
                                    <input type="checkbox" id="select-all">
                                </th>
                                <th>User</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Books Listed</th>
                                <th>Joined Date</th>
                    
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($user = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td class="checkbox-cell">
                                            <input type="checkbox" class="row-checkbox" data-user-id="<?php echo $user['user_id']; ?>">
                                        </td>
                                        <td class="user-cell">
                                            <div class="user-info">
                                                <div class="user-avatar">
                                                    <span><?php echo strtoupper(substr($user['username'], 0, 2)); ?></span>
                                                </div>
                                                <span class="user-name"><?php echo htmlspecialchars($user['username']); ?></span>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                                        <td><?php echo htmlspecialchars($user['books_listed']); ?></td>
                                        <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                                        
                                        <td class="actions-cell">
                                            
                                            <button class="action-btn delete-btn" title="Delete User" data-user-id="<?php echo $user['user_id']; ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7">No users found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="table-actions">
                    <button class="btn btn-danger delete-selected" id="delete-selected" disabled>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        Delete Selected
                    </button>
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
                <p>Are you sure you want to delete the selected user(s)? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" id="cancel-delete">Cancel</button>
                <button class="btn btn-danger" id="confirm-delete">Delete</button>
            </div>
        </div>
    </div>

    <script src="../JS/adminUser.js"></script>
    
</body>
</html>