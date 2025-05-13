<?php
include_once 'conn.php';

// Vulnerable reset implementation
if(isset($_POST['confirm_reset'])) {
    // No CSRF protection
    
    // Drop table if exists (without checking permissions)
    $sql = "DROP TABLE IF EXISTS users";
    mysqli_query($conn, $sql);
    
    // Recreate table with InnoDB engine explicitly specified
    $sql = "CREATE TABLE users (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(256) NOT NULL,
        pass VARCHAR(256) NOT NULL,
        message TEXT NOT NULL,
        money INT(11) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    mysqli_query($conn, $sql);
    
    // Insert sample users with plaintext passwords
    $sql = "INSERT INTO users (email, pass, message, money) VALUES
        ('admin@example.com', 'admin123', 'I am the administrator', 10000),
        ('user@example.com', 'password123', 'Regular user here', 500),
        ('alice@example.com', 'alice123', 'Hello from Alice!', 1200),
        ('bob@example.com', 'bob456', 'Bob was here', 800)";
    
    if(mysqli_query($conn, $sql)) {
        $success = "Database has been reset successfully!";
    } else {
        $error = "Error resetting database: " . mysqli_error($conn);
    }
}

// Vulnerable backup implementation
if(isset($_GET['backup']) && $_GET['backup'] == 1) {
    // Direct file IO operations without proper permission checking
    $backup_file = "backup_" . date("Y-m-d") . ".sql";
    $sql = "SELECT * FROM users";
    $result = mysqli_query($conn, $sql);
    
    $backup_content = "-- Database Backup\n";
    $backup_content .= "-- Generated: " . date("Y-m-d H:i:s") . "\n\n";
    $backup_content .= "DROP TABLE IF EXISTS users;\n";
    $backup_content .= "CREATE TABLE users (\n";
    $backup_content .= "  id INT(11) AUTO_INCREMENT PRIMARY KEY,\n";
    $backup_content .= "  email VARCHAR(256) NOT NULL,\n";
    $backup_content .= "  pass VARCHAR(256) NOT NULL,\n";
    $backup_content .= "  message TEXT NOT NULL,\n";
    $backup_content .= "  money INT(11) NOT NULL\n";
    $backup_content .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;\n\n";
    
    while($row = mysqli_fetch_assoc($result)) {
        $backup_content .= "INSERT INTO users VALUES (";
        $backup_content .= $row['id'] . ", ";
        $backup_content .= "'" . $row['email'] . "', ";
        $backup_content .= "'" . $row['pass'] . "', ";
        $backup_content .= "'" . $row['message'] . "', ";
        $backup_content .= $row['money'] . ");\n";
    }
    
    // Directory traversal vulnerability
    $backup_path = $_GET['path'] ?? './';
    file_put_contents($backup_path . $backup_file, $backup_content);
    
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"$backup_file\"");
    echo $backup_content;
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Database - Vulnerable App</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; }
        .card { border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; }
        input[type=submit] { background-color: #f44336; color: white; padding: 8px 12px; border: none; cursor: pointer; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Database Management</h1>
        
        <div class="card">
            <h2>Reset Database</h2>
            <p>Warning: This will delete all existing data and create a fresh database with sample users.</p>
            
            <?php if(isset($success)): ?>
                <p class="success"><?php echo $success; ?></p>
            <?php endif; ?>
            
            <?php if(isset($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            
            <form method="POST" action="">
                <input type="submit" name="confirm_reset" value="Reset Database">
            </form>
        </div>
        
        <div class="card">
            <h2>Backup Database</h2>
            <p>Generate a SQL backup of the current database.</p>
            <a href="?backup=1">Download Backup</a>
        </div>
        
        <div class="card">
            <a href="index.php">Back to Login</a>
        </div>
    </div>
</body>
</html>
