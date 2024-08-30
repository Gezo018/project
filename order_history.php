<?php
session_start();
include 'db_connection.php';
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    echo "<div class='container'><h2>Please log in to view your order history.</h2></div>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch order history
$stmt = $pdo->prepare("
    SELECT o.id, o.total_price, o.created_at, oi.product_id, p.name, oi.quantity, oi.price, oi.size, oi.flavor, oi.custom_message
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    WHERE o.customer_id = ? AND o.status = 'pending'
    ORDER BY o.created_at DESC
");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการสั่งซื้อ - บ้านแฟรงค์เบเกอร์</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Itim&display=swap">
    <style>
        body {
            font-family: 'Itim', cursive;
            background-color: #fff8f0;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #d2691e;
            text-align: center;
            margin-bottom: 30px;
        }
        .order {
            border: 1px solid #e0e0e0;
            padding: 15px;
            border-radius: 8px;
            background-color: #fff4e6;
            margin-bottom: 20px;
        }
        .order h3 {
            color: #ff6347;
            margin-bottom: 10px;
        }
        .order p {
            margin: 5px 0;
            color: #555;
        }
        .order-items {
            margin-top: 15px;
            padding-left: 20px;
        }
        .order-item {
            padding: 10px;
            background-color: #fdf2e9;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        hr {
            border: none;
            border-top: 1px solid #e0e0e0;
            margin: 20px 0;
        }
        p {
            font-size: 16px;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>ประวัติการสั่งซื้อ</h2>

        <?php if (empty($orders)): ?>
            <p style="text-align:center;">คุณยังไม่มีประวัติการสั่งซื้อ</p>
        <?php else: ?>
            <?php 
            $last_order_id = null;
            foreach ($orders as $order): 
                if ($order['id'] !== $last_order_id): 
                    if ($last_order_id !== null) echo '<hr>'; 
                    $last_order_id = $order['id']; 
            ?>
                <div class="order">
                    <h3>คำสั่งซื้อที่ #<?php echo htmlspecialchars($order['id']); ?></h3>
                    <p><strong>วันที่:</strong> <?php echo htmlspecialchars($order['created_at']); ?></p>
                    <p><strong>ยอดรวม:</strong> ฿<?php echo number_format($order['total_price'], 2); ?></p>
                    <div class="order-items">
                        <?php foreach ($orders as $item): ?>
                            <?php if ($item['id'] == $order['id']): ?>
                                <div class="order-item">
                                    <p><strong>ชื่อสินค้า:</strong> <?php echo htmlspecialchars($item['name']); ?></p>
                                    <p><strong>ขนาด:</strong> <?php echo htmlspecialchars($item['size']); ?></p>
                                    <p><strong>รสชาติ:</strong> <?php echo htmlspecialchars($item['flavor']); ?></p>
                                    <p><strong>ข้อความ:</strong> <?php echo htmlspecialchars($item['custom_message']); ?></p>
                                    <p><strong>จำนวน:</strong> <?php echo htmlspecialchars($item['quantity']); ?></p>
                                    <p><strong>ราคา:</strong> ฿<?php echo number_format($item['price'], 2); ?></p>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
