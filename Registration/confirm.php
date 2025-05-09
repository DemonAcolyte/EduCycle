<?php
include '../Database/database.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verify the token
    $sql = "SELECT * FROM pending_users WHERE token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $email = $row['email'];
        $password = $row['password'];

        // Move user to the main users table
        $sql = "INSERT INTO users (email, password) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $password);

        if ($stmt->execute()) {
            // Delete from pending_users
            $sql = "DELETE FROM pending_users WHERE token = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $token);
            $stmt->execute();

            echo "Registration confirmed. You can now log in.";
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Invalid or expired token.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "No token provided.";
}
?>
