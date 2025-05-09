
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com"/>
    <title>Login</title>
    <link rel="stylesheet" href="CSS/styles.css" type="text/css">
    
</head>
<body>
<?php include_once('includedFiles/header.php');?>
<form action="Login/existingUsers.php" method="post" autocomplete="off">
    <div class="login">
        <h2>Login</h2>
        <div class="input-group">
            <label for="username">Username</label>
            <input type="text" name="username" placeholder="Type your username">
            <p class="error-message" id="username-error"></p>
        </div>
        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Type your password">
            <p class="error-message" id="password-error"></p>
        </div>
        <br>
        <button class="button1" type="submit" name="submit">Login</button>
        <p>Don't have an account? <a href="registration.php">Register here</a></p>
    </div>
</form>
<script>
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');

    if (error === 'wrong_password') {
        const passwordError = document.getElementById('password-error');
        passwordError.textContent = 'Incorrect password. Please try again.';
        passwordError.style.color = 'red';
        passwordError.style.fontWeight = 'bold';
    } else if (error === 'user_not_found') {
        const usernameError = document.getElementById('username-error');
        usernameError.textContent = 'User not found. Please check your username.';
        usernameError.style.color = 'red';
        usernameError.style.fontWeight = 'bold';
    }
</script>
</body>
</html>
