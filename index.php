<?php
session_start();
include_once 'conn.php';

// Vulnerable login check
if(isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // SQL injection vulnerability (no prepared statements)
    $sql = "SELECT * FROM users WHERE email='$email' AND pass='$password'";
    $result = mysqli_query($conn, $sql);
    
    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['money'] = $row['money'];
        header("Location: dashboard.php");
    } else {
        $error = "Invalid email or password!";
    }
}

// Vulnerable registration implementation
if(isset($_POST['register'])) {
    $email = $_POST['reg_email'];
    $password = $_POST['reg_password']; 
    $message = $_POST['reg_message'];
    
    // No input validation or sanitization
    // No check for existing email
    // Plaintext password storage
    
    // SQL injection vulnerability
    $sql = "INSERT INTO users (email, pass, message, money) 
            VALUES ('$email', '$password', '$message', 100)";
    
    if(mysqli_query($conn, $sql)) {
        $reg_success = "Account created successfully! You can now login.";
    } else {
        $reg_error = "Registration failed: " . mysqli_error($conn);
    }
}

// Handle logout
if(isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Vulnerable App</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; display: flex; justify-content: space-between; }
        .login-form, .register-form { 
            width: 48%; 
            padding: 20px; 
            border: 1px solid #ccc; 
            box-sizing: border-box;
        }
        input[type=text], input[type=password], textarea { 
            width: 100%; 
            padding: 10px; 
            margin: 10px 0; 
            box-sizing: border-box; 
        }
        input[type=submit] { 
            background-color: #4CAF50; 
            color: white; 
            padding: 10px 15px; 
            border: none; 
            cursor: pointer; 
        }
        .error { color: red; }
        .success { color: green; }
        .footer { 
            margin-top: 20px; 
            text-align: center;
            clear: both;
        }
        .footer a {
            margin: 0 10px;
            text-decoration: none;
            color: #4CAF50;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-form">
            <h2>Login to Vulnerable App</h2>
            <?php if(isset($error)) { echo "<p class='error'>$error</p>"; } ?>
            <form method="POST" action="">
                <label for="email">Email:</label>
                <input type="text" name="email" required>
                
                <label for="password">Password:</label>
                <input type="password" name="password" required>
                
                <input type="submit" name="submit" value="Login">
            </form>
        </div>
        
        <div class="register-form">
            <h2>Create New Account</h2>
            <?php if(isset($reg_success)) { echo "<p class='success'>$reg_success</p>"; } ?>
            <?php if(isset($reg_error)) { echo "<p class='error'>$reg_error</p>"; } ?>
            <form method="POST" action="">
                <label for="reg_email">Email:</label>
                <input type="text" name="reg_email" required>
                
                <label for="reg_password">Password:</label>
                <input type="password" name="reg_password" required>
                
                <label for="reg_message">Profile Message:</label>
                <textarea name="reg_message" rows="4"></textarea>
                
                <input type="submit" name="register" value="Register">
            </form>
        </div>
    </div>
    
    <div class="footer">
        <a href="reset.php">Reset/Backup Database</a>
    </div>
</body>
</html>
