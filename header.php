<?php
// This file is included in all frontend pages
?>
<div class="header">
    <div class="nav">
        <h1><a href="index.php" style="color: white; text-decoration: none;">Order With Me</a></h1>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="cart.php">Cart (<span id="cart-count">0</span>)</a>
            <?php if(isLoggedIn()): ?>
                <a href="orders.php">My Orders</a>
                <span style="color: white; margin-left: 1rem;">
                    Welcome, <?php echo $_SESSION['first_name']; ?>!
                </span>
                <?php if(isAdmin()): ?>
                    <a href="admin/">Admin Panel</a>
                <?php endif; ?>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.header { background: #333; color: white; padding: 1rem; }
.nav { display: flex; justify-content: space-between; align-items: center; }
.nav-links a { color: white; text-decoration: none; margin-left: 1rem; }
.nav-links a:hover { text-decoration: underline; }
</style>

<script>
// Initialize cart count
document.addEventListener('DOMContentLoaded', function() {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const total = cart.reduce((sum, item) => sum + item.quantity, 0);
    if (document.getElementById('cart-count')) {
        document.getElementById('cart-count').textContent = total;
    }
});
</script>