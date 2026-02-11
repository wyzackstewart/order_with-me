<?php
require_once 'config/session.php';
require_once 'config/database.php';
require_once 'models/User.php';

if(isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$error = '';

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $user->username = $_POST['username'];
    $user->password_hash = $_POST['password'];
    
    if($user->login()) {
        $_SESSION['user_id'] = $user->user_id;
        $_SESSION['username'] = $user->username;
        $_SESSION['role'] = $user->role;
        $_SESSION['first_name'] = $user->first_name;
        
        if($user->role === 'admin') {
            header("Location: admin/index.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Order With Me</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 400px; margin: 100px auto; padding: 20px; background: white; border-radius: 5px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; }
        .btn { background: #3498db; color: white; border: none; padding: 10px; cursor: pointer; width: 100%; }
        .error { color: red; margin-bottom: 10px; }
        .admin-login { margin-top: 10px; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <?php if($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
        
        <div class="admin-login">
            <strong>Admin Test Account:</strong><br>
            Username: admin<br>
            Password: admin123
        </div>
    </div>
</body>
</html>