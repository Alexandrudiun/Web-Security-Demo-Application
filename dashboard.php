<?php
session_start();
include_once 'conn.php';

// No authentication check - missing session validation

// Process money transfer (CSRF vulnerability - no tokens)
if(isset($_POST['transfer'])) {
    $to_id = $_POST['to_id'];
    $amount = $_POST['amount'];
    $from_id = $_SESSION['user_id'];
    
    // No validation of funds
    $sql = "UPDATE users SET money = money - $amount WHERE id = $from_id";
    mysqli_query($conn, $sql);
    
    $sql = "UPDATE users SET money = money + $amount WHERE id = $to_id";
    mysqli_query($conn, $sql);
    
    // Update session money
    $_SESSION['money'] -= $amount;
    $success = "Transfer successful!";
}

// Process message update (XSS vulnerability)
if(isset($_POST['update_message'])) {
    $message = $_POST['message'];
    $user_id = $_SESSION['user_id'];
    
    // No sanitization of message
    $sql = "UPDATE users SET message = '$message' WHERE id = $user_id";
    mysqli_query($conn, $sql);
    $msg_success = "Message updated!";
}

// Get user data
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Search functionality (SQL injection)
$users = [];
if(isset($_GET['search'])) {
    $search = $_GET['search'];
    // SQL injection vulnerability
    $sql = "SELECT id, email, message FROM users WHERE email LIKE '%$search%'";
    $search_result = mysqli_query($conn, $sql);
    while($row = mysqli_fetch_assoc($search_result)) {
        $users[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Vulnerable App</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .card { border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; }
        input[type=text], input[type=number] { width: 100%; padding: 8px; margin: 5px 0; box-sizing: border-box; }
        input[type=submit] { background-color: #4CAF50; color: white; padding: 8px 12px; border: none; cursor: pointer; }
        .success { color: green; }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo $_SESSION['email']; ?></h1>
        
        <div class="card">
            <h3>Your Balance: $<?php echo $_SESSION['money']; ?></h3>
        </div>
        
        <div class="card">
            <h3>Your Message:</h3>
            <!-- XSS vulnerability - direct output of user data -->
            <p><?php echo $user['message']; ?></p>
            
            <h4>Update Message:</h4>
            <?php if(isset($msg_success)) { echo "<p class='success'>$msg_success</p>"; } ?>
            <form method="POST" action="">
                <textarea name="message" rows="4" style="width:100%"><?php echo $user['message']; ?></textarea>
                <input type="submit" name="update_message" value="Update Message">
            </form>
        </div>
        
        <div class="card">
            <h3>Transfer Money:</h3>
            <?php if(isset($success)) { echo "<p class='success'>$success</p>"; } ?>
            <form method="POST" action="">
                <label for="to_id">Recipient ID:</label>
                <input type="number" name="to_id" required>
                
                <label for="amount">Amount:</label>
                <input type="number" name="amount" required>
                
                <input type="submit" name="transfer" value="Transfer Money">
            </form>
        </div>
        
        <div class="card">
            <h3>Search Users:</h3>
            <form method="GET" action="">
                <input type="text" name="search" placeholder="Search by email">
                <input type="submit" value="Search">
            </form>
            
            <?php if(count($users) > 0): ?>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Message</th>
                    </tr>
                    <?php foreach($users as $u): ?>
                        <tr>
                            <td><?php echo $u['id']; ?></td>
                            <td><?php echo $u['email']; ?></td>
                            <!-- XSS vulnerability -->
                            <td><?php echo $u['message']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>
        
        <div class="card">
            <a href="index.php?logout=1">Logout</a>
        </div>
    </div>
</body>
</html>
