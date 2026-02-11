<?php
require_once 'config/session.php';
require_once 'config/database.php';
require_once 'models/Order.php';
require_once 'models/OrderItem.php';

requireLogin();

$database = new Database();
$db = $database->getConnection();
$order = new Order($db);
$orderItem = new OrderItem($db);

if(isset($_GET['id'])) {
    $order->order_id = $_GET['id'];
    $order->readOne();
    
    // Check if user owns this order or is admin
    if(!isAdmin() && $order->user_id != $_SESSION['user_id']) {
        header("Location: orders.php");
        exit();
    }
    
    $items = $orderItem->getByOrder($order->order_id);
} else {
    header("Location: orders.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details #<?php echo $order->order_id; ?> - Order With Me</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .order-header { background: #f4f4f4; padding: 1rem; border-radius: 5px; margin-bottom: 2rem; }
        .order-info { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; }
        .order-items table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
        .order-items th, .order-items td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .order-items th { background: #f4f4f4; }
        .status { padding: 0.25rem 0.5rem; border-radius: 3px; font-weight: bold; }
        .status-pending { background: #f39c12; color: white; }
        .status-processing { background: #3498db; color: white; }
        .status-shipped { background: #9b59b6; color: white; }
        .status-delivered { background: #2ecc71; color: white; }
        .total { text-align: right; font-size: 1.2rem; font-weight: bold; margin-top: 1rem; }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container">
        <h2>Order Details #<?php echo $order->order_id; ?></h2>
        
        <div class="order-header">
            <div class="order-info">
                <div>
                    <strong>Order Date:</strong><br>
                    <?php echo date('F j, Y, g:i a', strtotime($order->order_date)); ?>
                </div>
                <div>
                    <strong>Status:</strong><br>
                    <span class="status status-<?php echo $order->status; ?>">
                        <?php echo ucfirst($order->status); ?>
                    </span>
                </div>
                <div>
                    <strong>Payment Method:</strong><br>
                    <?php echo ucfirst(str_replace('_', ' ', $order->payment_method)); ?>
                </div>
                <div>
                    <strong>Total Amount:</strong><br>
                    $<?php echo number_format($order->total_amount, 2); ?>
                </div>
            </div>
            
            <div style="margin-top: 1rem;">
                <strong>Shipping Address:</strong><br>
                <?php echo nl2br(htmlspecialchars($order->shipping_address)); ?>
            </div>
        </div>
        
        <div class="order-items">
            <h3>Order Items</h3>
            <table>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
                <?php 
                $total = 0;
                while($item = $items->fetch(PDO::FETCH_ASSOC)): 
                    $subtotal = $item['unit_price'] * $item['quantity'];
                    $total += $subtotal;
                ?>
                    <tr>
                        <td>
                            <?php echo htmlspecialchars($item['name']); ?>
                            <?php if($item['image_url']): ?>
                                <br><img src="<?php echo $item['image_url']; ?>" style="max-width: 50px; margin-top: 5px;">
                            <?php endif; ?>
                        </td>
                        <td>$<?php echo number_format($item['unit_price'], 2); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>$<?php echo number_format($subtotal, 2); ?></td>
                    </tr>
                <?php endwhile; ?>
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                    <td><strong>$<?php echo number_format($total, 2); ?></strong></td>
                </tr>
            </table>
        </div>
        
        <div style="margin-top: 2rem;">
            <a href="orders.php" class="btn">Back to Orders</a>
            <?php if(isAdmin()): ?>
                <a href="admin/orders.php" class="btn">Back to Admin Orders</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>