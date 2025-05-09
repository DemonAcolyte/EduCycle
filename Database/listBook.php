<?php
session_start();
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'database.php';


$title = $_POST['book-title'];
$author = $_POST['book-author'];
$year = $_POST['year'];
$category = $_POST['category'];
$condition = $_POST['book-condition'];
$edition = $_POST['book-edition'];
$description = $_POST['book-description'];
$contact_method = $_POST['contact-method'];
$contact_info = $_POST['contact-info'];
$owner_id = $_SESSION['user_id'];

$upload_dir = '../uploads/'; // Root directory for uploaded images
$cover_image = '';
if (isset($_FILES['book-cover']) && $_FILES['book-cover']['error'] == 0) {
    $cover_image = basename($_FILES['book-cover']['name']);
    $upload_path = $upload_dir . $cover_image;

    // Check if the directory is writable
    if (!is_writable($upload_dir)) {
        die("Error: Upload directory is not writable.");
    }

    if (!move_uploaded_file($_FILES['book-cover']['tmp_name'], $upload_path)) {
        die("Error uploading file.");
    }

}

$sql = "INSERT INTO books (title, author, category, book_condition, book_year, edition, description, contact_info, contact_method, image, owner_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("ssssssssssi", $title, $author, $category, $condition, $year, $edition, $description, $contact_info, $contact_method, $cover_image, $owner_id);

if ($stmt->execute()) {
    // Redirect to users.php after successful submission
    header("Location: ../Users/users.php");
    
    exit();
} else {
    $stmt->close();
    $conn->close();
    die("Error executing statement: " . $stmt->error);
}



?>
