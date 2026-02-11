<?php
require_once 'config/session.php';
require_once 'config/database.php';
require_once 'models/Order.php';
require_once 'models/OrderItem.php';
require_once 'models/Product.php';

requireLogin();

$database = new Database();
$db = $database->getConnection();
$order = new Order($db);
$orderItem = new OrderItem($db);
$product = new Product($db);

$error = '';
$success = '';

if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get cart data from session or POST (in real app, use AJAX)
    $cart = json_decode($_POST['cart_data'], true);
    
    if(empty($cart)) {
        $error = "Your cart is empty";
    } else {
        // Calculate total
        $total = 0;
        foreach($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        // Create order
        $order->user_id = $_SESSION['user_id'];
        $order->total_amount = $total;
        $order->shipping_address = $_POST['shipping_address'];
        $order->payment_method = $_POST['payment_method'];
        
        if($order->create()) {
            // Add order items
            foreach($cart as $item) {
                $orderItem->order_id = $order->order_id;
                $orderItem->product_id = $item['id'];
                $orderItem->quantity = $item['quantity'];
                $orderItem->unit_price = $item['price'];
                $orderItem->create();
                
                // Update product stock
                $product->product_id = $item['id'];
                if($product->readOne()) {
                    $product->stock_quantity -= $item['quantity'];
                    $product->update();
                }
            }
            
            $success = "Order placed successfully! Order ID: #" . $order->order_id;
            
            // Clear cart
            echo '<script>localStorage.removeItem("cart");</script>';
        } else {
            $error = "Failed to create order";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Order With Me</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input, select, textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; }
        .btn { background: #3498db; color: white; border: none; padding: 10px 20px; cursor: pointer; }
        .btn-submit { background: #2ecc71; }
        .error { color: red; }
        .success { color: green; }
        .order-summary { background: #f9f9f9; padding: 15px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Checkout</h2>
        
        <?php if($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="success"><?php echo $success; ?></div>
            <a href="orders.php" class="btn">View My Orders</a>
        <?php else: ?>
        
        <div class="order-summary" id="order-summary">
            <!-- Order summary will be loaded by JavaScript -->
        </div>
        
        <form method="POST" action="" id="checkout-form">
            <input type="hidden" name="cart_data" id="cart-data">
            
            <div class="form-group">
                <label>Shipping Address *</label>
                <textarea name="shipping_address" rows="3" required></textarea>
            </div>
            
            <div class="form-group">
                <label>Payment Method *</label>
                <select name="payment_method" required>
                    <option value="">Select payment method</option>
                    <option value="credit_card">Credit Card</option>
                    <option value="paypal">PayPal</option>
                    <option value="cod">Cash on Delivery</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-submit">Place Order</button>
        </form>
        
        <?php endif; ?>
    </div>

    <script>
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        
        function loadOrderSummary() {
            const container = document.getElementById('order-summary');
            const cartDataInput = document.getElementById('cart-data');
            
            if (cart.length === 0) {
                container.innerHTML = '<p>Your cart is empty</p>';
                return;
            }
            
            let html = '<h3>Order Summary</h3>';
            let total = 0;
            
            cart.forEach(item => {
                const subtotal = item.price * item.quantity;
                total += subtotal;
                html += `<p>${item.name} x ${item.quantity}: $${subtotal.toFixed(2)}</p>`;
            });
            
            html += `<p><strong>Total: $${total.toFixed(2)}</strong></p>`;
            container.innerHTML = html;
            
            // Set cart data in hidden input
            cartDataInput.value = JSON.stringify(cart);
        }
        
        // Load summary on page load
        loadOrderSummary();
        
        // Update cart count
        function updateCartCount() {
            const total = cart.reduce((sum, item) => sum + item.quantity, 0);
            if (document.getElementById('cart-count')) {
                document.getElementById('cart-count').textContent = total;
            }
        }
        
        updateCartCount();
    </script>
</body>
</html>