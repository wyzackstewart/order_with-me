<?php
require_once 'config/session.php';
require_once 'config/database.php';
require_once 'models/Order.php';

requireLogin();

$database = new Database();
$db = $database->getConnection();
$order = new Order($db);

// Get user's orders
$orders = $order->readByUser($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Order With Me</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 1000px; margin: 0 auto; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #f4f4f4; }
        .status-pending { color: #f39c12; }
        .status-processing { color: #3498db; }
        .status-shipped { color: #9b59b6; }
        .status-delivered { color: #2ecc71; }
        .no-orders { text-align: center; padding: 50px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>My Orders</h2>
        
        <?php if($orders->rowCount() > 0): ?>
            <table>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Payment Method</th>
                    <th>Actions</th>
                </tr>
                <?php while($row = $orders->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td>#<?php echo $row['order_id']; ?></td>
                        <td><?php echo date('M d, Y', strtotime($row['order_date'])); ?></td>
                        <td>$<?php echo number_format($row['total_amount'], 2); ?></td>
                        <td class="status-<?php echo $row['status']; ?>">
                            <?php echo ucfirst($row['status']); ?>
                        </td>
                        <td><?php echo ucfirst(str_replace('_', ' ', $row['payment_method'])); ?></td>
                        <td>
                            <a href="order_details.php?id=<?php echo $row['order_id']; ?>">View Details</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <div class="no-orders">
                <p>You haven't placed any orders yet.</p>
                <a href="index.php" class="btn">Start Shopping</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>