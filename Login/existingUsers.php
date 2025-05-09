<?php
    session_start();
    include("../Database/database.php");
    

    if ($conn->connect_error){
        die("Connection Failed: ". $conn->connect_error);
    }

    if($_SERVER["REQUEST_METHOD"] === 'POST'){
        $username = $_POST['username'];
        $password = $_POST['password'];

        //Security Measure
        if (empty($username) || empty($password)){
            header("Location: ../index.html");
            die("Username and Password is required");
        }
       

        


    $sql ="SELECT user_id, username, password_hash FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
   
    if ($stmt->num_rows > 0){
        $stmt->bind_result($db_id, $db_username, $db_password);
        $stmt->fetch();
        
        if (password_verify($password, $db_password)){
            $_SESSION['user_id'] = $db_id;
            $_SESSION['username'] = $db_username;

            if (strtolower($db_username) === 'admin') {
                header("Location: ../admin/adminBooks.php");
            } else {
                header("Location: ../Users/users.php");
            }
            exit();
        } else {
            header("Location: ../index.php?error=wrong_password");
            exit();
        } 

    } else {
        header("Location: ../index.php?error=user_not_found");
        exit();
    }
    $stmt->close();
    $conn->close();
}
