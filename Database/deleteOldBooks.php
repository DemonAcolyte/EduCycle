<?php
include 'database.php';

// SQL query to delete books older than one day
$sql = "DELETE FROM books WHERE created_at < NOW() - INTERVAL 1 DAY";

if ($conn->query($sql) === TRUE) {
    echo "Old books deleted successfully.";
} else {
    echo "Error deleting old books: " . $conn->error;
}

$conn->close();
?>
