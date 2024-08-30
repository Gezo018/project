<?php
session_start();
include 'db_connection.php';
include 'navbar.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<div class='container'><h2>Please log in to view your cart.</h2></div>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if the user has an active cart
$stmt = $pdo->prepare("SELECT id FROM cart WHERE customer_id = ? AND status = 'active'");
$stmt->execute([$user_id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$result) {
    // If no active cart, create one
    $stmt = $pdo->prepare("INSERT INTO cart (customer_id, status) VALUES (?, 'active')");
    $stmt->execute([$user_id]);
    $cart_id = $pdo->lastInsertId();
} else {
    $cart_id = $result['id'];
}

// Fetch cart items
$stmt = $pdo->prepare("
    SELECT ci.*, p.name, p.image 
    FROM cart_items ci
    JOIN products p ON ci.product_id = p.id
    WHERE ci.cart_id = ?
");
$stmt->execute([$cart_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตะกร้าสินค้า - บ้านแฟรงค์เบเกอร์</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Itim&display=swap">
    <style>
        body {
            font-family: 'Itim', sans-serif;
            color: #333;
            background-color: #fff8f0;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .cart-title {
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
            color: #4CAF50;
        }
        .cart-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #f9f9f9;
        }
        .cart-item img {
            max-width: 100px;
            margin-right: 20px;
            border-radius: 4px;
        }
        .cart-item-info {
            flex: 1;
        }
        .item-price {
            color: #555;
            font-weight: bold;
        }
        .cart-total {
            text-align: right;
            font-size: 18px;
            margin-top: 20px;
            font-weight: bold;
        }
        .button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .button:hover {
            background-color: #45a049;
        }
        .remove-button {
            background-color: #f44336;
        }
        .remove-button:hover {
            background-color: #e31b0c;
        }
        .checkout-button {
            margin-top: 20px;
            width: 100%;
            background-color: #ff6f61;
        }
        .checkout-button:hover {
            background-color: #ff564f;
        }
        .empty-cart {
            text-align: center;
            font-size: 18px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="cart-title">
            <i class="fas fa-shopping-cart"></i> ตะกร้าสินค้าของคุณ
        </h2>

        <?php 
        $total_price = 0;

        if (empty($cart_items)): ?>
            <div class="empty-cart">ตะกร้าสินค้าของคุณว่างเปล่า</div>
        <?php else: 
            foreach ($cart_items as $item): 
                $item_total = $item['price'] * $item['quantity'];
                $total_price += $item_total;
        ?>
            <div class="cart-item">
                <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                <div class="cart-item-info">
                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                    <p><strong>ขนาด:</strong> <?php echo htmlspecialchars($item['size']); ?></p>
                    <p><strong>รสชาติ:</strong> <?php echo htmlspecialchars($item['flavor']); ?></p>
                    <p><strong>ข้อความ:</strong> <?php echo htmlspecialchars($item['custom_message']); ?></p>
                    <p><strong>จำนวน:</strong> <?php echo htmlspecialchars($item['quantity']); ?></p>
                    <p class="item-price"><strong>ราคา:</strong> ฿<?php echo number_format($item_total, 2); ?></p>
                </div>
                <form action="remove_from_cart.php" method="POST">
                    <input type="hidden" name="cart_item_id" value="<?php echo htmlspecialchars($item['id']); ?>">
                    <button type="submit" class="button remove-button">
                        <i class="fas fa-trash"></i> ลบ
                    </button>
                </form>
            </div>
            <hr>
        <?php 
            endforeach;
        endif; ?>

        <div class="cart-total">
            <p><strong>ยอดรวม:</strong> ฿<?php echo number_format($total_price, 2); ?></p>
        </div>

        <?php if ($total_price > 0): ?>
        <form action="checkout.php" method="POST">
            <button type="submit" class="button checkout-button">
                <i class="fas fa-lock"></i> สั่งซื้อและชำระเงิน
            </button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
