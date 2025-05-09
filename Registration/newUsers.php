<?php
include("../Database/database.php");

$username = $_POST["username"];
$password = $_POST["password"];
$repeatPass =  $_POST["repeat-password"];
$email = $_POST["email"];
$age = $_POST[""];
$created_at = date(format: "Y-m-d H:i:s");

// Check if username or email already exists
$sql_check = "SELECT * FROM users WHERE username = ? OR email = ?";
$stmt_check = $conn->prepare($sql_check);
if ($stmt_check) {
    $stmt_check->bind_param('ss', $username, $email);
    $stmt_check->execute();
    $result = $stmt_check->get_result();
    if ($result->num_rows > 0) {
        $stmt_check->close();
        $conn->close();
        header("Location: ../registration.php?error=exists");
        exit();
    }
    $stmt_check->close();
}

if(empty($username) || empty($password) || empty($repeatPass) || empty($email) || empty($created_at)) {
    header("Location: ../registration.php?error=empty");
    exit();
}

$password_hashed = password_hash($password, PASSWORD_DEFAULT);
$sql = "INSERT INTO users(username, email, password_hash, created_at) 
        VALUES (?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param('ssss', $username, $email, $password_hashed, $created_at);

    if ($stmt->execute()) {
        header("Location: ../index.php");
    } else {
        echo "Error Registering" . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Error preparing statement";
}

$conn->close();
?>