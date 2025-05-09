<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="CSS/styles.css" type="text/css">
</head>
<body>
<nav class="navigation-bar">
    
    <div class="navdiv">
     <div class="logo">EduCycle</div>
 
      <div class="link">
       <a href="desc/about.html" class="nav-link">About</a>
       <a href="desc/contact.html" class="nav-link">Contact</a>
       <a href="index.php" class="nav-link">Login</a>
      </div>
     </div>
 </nav>
    <?php
    if (isset($_GET['error'])) {
        if ($_GET['error'] == 'exists') {
            echo "<p id='user-error'>Username or Email already exists.</p>";
        } elseif ($_GET['error'] == 'empty') {
            echo "<p id='user-error'>Error: All fields are required.</p>";
        }
    }
    ?>
    <form id="registrationForm" action="Registration/newUsers.php" method="post" autocomplete="off" >
        

        <div class="register">
        <h2>Sign Up</h2>
    
        <div class="input-group">
            <label for="username">Username</label>
            <input type="text" name="username">
            
        </div>
        
        <div class="input-group">
            <br>
            <label>Password</label>
            <input type="password" id="password" name="password">
            <p id="password-error" class="error-message">*</p>
        </div>

        <div class="input-group">
            <br>
            <label>Repeat Password</label>
            <input type="password" id="confirm-password" name="repeat-password">
            <p id="confirm-password-error" class="error-message">not identitcal</p>
        </div>

        <div class="input-group">
            <br>
            <label>Email</label>
            <input type="email" id="email" name="email" required>
            <p id="email-error" class="error-message">Email wrong format</p>
            <br>
        </div>

        
        <button class="button1" type = "submit" name = "submit">Submit</button>
        
        <p>Already have an account? <a href="index.php">Login here</a></p>

        </div>
      
        
    </form>
    <script src="JS/regis.js"></script>
</body>
</html>

