<?php
require_once 'config/database.php';
require_once 'models/User.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$error = '';
$success = '';

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $user->username = $_POST['username'];
    $user->email = $_POST['email'];
    $user->password_hash = $_POST['password'];
    $user->first_name = $_POST['first_name'];
    $user->last_name = $_POST['last_name'];
    $user->address = $_POST['address'];
    $user->phone = $_POST['phone'];
    
    // Server-side validation
    if(empty($user->username) || empty($user->email) || empty($user->password_hash)) {
        $error = "Please fill all required fields";
    } elseif(strlen($user->password_hash) < 6) {
        $error = "Password must be at least 6 characters";
    } else {
        if($user->register()) {
            $success = "Registration successful! You can now login.";
        } else {
            $error = "Registration failed. Username or email might already exist.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Order With Me</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 400px; margin: 50px auto; padding: 20px; background: white; border-radius: 5px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; }
        .btn { background: #3498db; color: white; border: none; padding: 10px; cursor: pointer; width: 100%; }
        .error { color: red; margin-bottom: 10px; }
        .success { color: green; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <?php if($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form id="register-form" method="POST" action="">
            <div class="form-group">
                <label>Username *</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Password * (min 6 characters)</label>
                <input type="password" name="password" id="password" required minlength="6">
            </div>
            <div class="form-group">
                <label>Confirm Password *</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
                <span id="password-error" style="color: red; display: none;">Passwords don't match</span>
            </div>
            <div class="form-group">
                <label>First Name *</label>
                <input type="text" name="first_name" required>
            </div>
            <div class="form-group">
                <label>Last Name *</label>
                <input type="text" name="last_name" required>
            </div>
            <div class="form-group">
                <label>Address *</label>
                <textarea name="address" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone">
            </div>
            <button type="submit" class="btn">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>

    <script>
        document.getElementById('register-form').onsubmit = function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const errorSpan = document.getElementById('password-error');
            
            if (password !== confirmPassword) {
                e.preventDefault();
                errorSpan.style.display = 'block';
                return false;
            }
            errorSpan.style.display = 'none';
            return true;
        };
    </script>
</body>
</html>