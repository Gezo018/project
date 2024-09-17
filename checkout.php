<?php
session_start();
include 'db_connection.php'; // Ensure $pdo is defined in this file
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    echo "<div class='container'><h2>กรุณาเข้าสู่ระบบเพื่อทำการชำระเงิน</h2></div>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Get active cart for user
$stmt = $pdo->prepare("SELECT id FROM cart WHERE customer_id = ? AND status = 'active'");
$stmt->execute([$user_id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$result) {
    echo "<div class='container'><h2>ตะกร้าของคุณว่างเปล่า</h2></div>";
    exit();
}

$cart_id = $result['id'];

// Fetch cart items
$stmt = $pdo->prepare("
    SELECT ci.*, p.name, p.price, p.image 
    FROM cart_items ci
    JOIN products p ON ci.product_id = p.id
    WHERE ci.cart_id = ?
");
$stmt->execute([$cart_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_price = 0;

function getPoundCakePrice($size) {
    switch ($size) {
        case '1':
            return 250;
        case '1.5':
            return 350;
        case '2':
            return 500;
        default:
            return 250; // Default to 1 pound price
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ชำระเงิน - บ้านแฟรงค์เบเกอร์</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Prompt', sans-serif;
            background-color: #F9DBBA;
            color: #333;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 40px auto;
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
        }

        .checkout-title {
            width: 100%;
            font-size: 32px;
            font-weight: 600;
            color: #4CAF50;
            text-align: center;
            margin-bottom: 20px;
        }

        .order-summary, .checkout-form {
            background-color: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            flex: 1 1 400px;
        }

        .order-summary h3, .checkout-form h3 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }

        .order-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
        }

        .order-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .order-item img {
            width: 100px;
            height: 100px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 20px;
            border: 1px solid #ddd;
        }

        .item-details {
            flex: 1;
        }

        .item-details p {
            margin-bottom: 8px;
            font-size: 16px;
            color: #555;
        }

        .item-details p strong {
            color: #333;
        }

        .order-total {
            text-align: right;
            margin-top: 20px;
            font-size: 20px;
            font-weight: 600;
            color: #333;
        }

        .checkout-form .form-group {
            margin-bottom: 20px;
        }

        .checkout-form label {
            display: block;
            margin-bottom: 8px;
            font-size: 16px;
            color: #333;
        }

        .checkout-form input,
        .checkout-form textarea,
        .checkout-form select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            color: #333;
            background-color: #fafafa;
            transition: border-color 0.3s;
        }

        .checkout-form input:focus,
        .checkout-form textarea:focus,
        .checkout-form select:focus {
            border-color: #4CAF50;
            outline: none;
        }

        .checkout-form button {
            width: 100%;
            padding: 15px;
            background-color: #4CAF50;
            color: #fff;
            font-size: 18px;
            font-weight: 500;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 10px;
        }

        .checkout-form button:hover {
            background-color: #45a049;
        }

        .upload-group {
            display: none;
            flex-direction: column;
            gap: 10px;
        }

        .upload-group input {
            border: 1px solid #ddd;
            background-color: #fafafa;
        }

        .upload-group label {
            font-size: 16px;
            color: #333;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                gap: 20px;
            }

            .order-item img {
                width: 80px;
                height: 80px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="checkout-title">
            <i class="fas fa-shopping-cart"></i> กรอกข้อมูลการชำระเงิน
        </h2>
        
        <div class="order-summary">
    <h3>สรุปรายการสั่งซื้อ</h3>
    <?php if ($cart_items): ?>
        <?php foreach ($cart_items as $item): 
            if ($item['size'] == '1.5' || $item['size'] == '2') {
                $item_price = getPoundCakePrice($item['size']);
            } else {
                $item_price = $item['price'];
            }
            $item_total = $item_price * $item['quantity'];
            $total_price += $item_total;
        ?>
                
                    <div class="order-item">
                        <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <div class="item-details">
                            <p><strong><?php echo htmlspecialchars($item['name']); ?></strong></p>
                            <p>ขนาด: <?php echo htmlspecialchars($item['size']); ?> <?php echo ($item['name'] == 'เค้กปอนด์') ? 'ปอนด์' : ''; ?></p>
                            <p>รสชาติ: <?php echo htmlspecialchars($item['flavor']); ?></p>
                            <p>จำนวน: <?php echo htmlspecialchars($item['quantity']); ?></p>
                            <p class="item-price">ราคา: ฿<?php echo number_format($item_total, 2); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="order-total">
                    ยอดรวมทั้งหมด: ฿<?php echo number_format($total_price, 2); ?>
                </div>
            <?php else: ?>
                <p>ไม่พบสินค้าในตะกร้าของคุณ.</p>
            <?php endif; ?>
        </div>

        <form action="process_order.php" method="POST" enctype="multipart/form-data" class="checkout-form">
            <h3>ข้อมูลการจัดส่ง</h3>
            <div class="form-group">
                <label for="name">ชื่อ-นามสกุล</label>
                <input type="text" id="name" name="name" placeholder="กรอกชื่อและนามสกุลของคุณ" required>
            </div>
            <div class="form-group">
                <label for="address">ที่อยู่</label>
                <textarea id="address" name="address" rows="3" placeholder="กรอกที่อยู่สำหรับการจัดส่ง" required></textarea>
            </div>
            <div class="form-group">
                <label for="phone">เบอร์โทรศัพท์</label>
                <input type="tel" id="phone" name="phone" placeholder="กรอกเบอร์โทรศัพท์ของคุณ" required>
            </div>
            <div class="form-group">
                <label for="email">อีเมล</label>
                <input type="email" id="email" name="email" placeholder="กรอกอีเมลของคุณ" required>
            </div>
            <div class="form-group">
                <label for="cake_details">รายละเอียดเพิ่มเติม(ถ้ามี)</label>
                <input type="text" id="cake_details" name="cake_details" placeholder="กรอกรายละเอียดเค้กที่ต้องการ">
            </div>

            <div class="form-group">
                <label for="delivery_date">เลือกวันที่จัดส่ง</label>
                <input type="date" id="delivery_date" name="delivery_date" required>
            </div>

            <div class="form-group">
                <label for="payment_method">วิธีการชำระเงิน</label>
                <select id="payment_method" name="payment_method" required onchange="toggleUploadFields()">
                    <option value="bank_transfer">โอนเงินผ่านธนาคาร</option>
                    <option value="cash_on_delivery">ชำระเงินเมื่อได้รับสินค้า</option>
                </select>
            </div>

            <div class="upload-group" id="upload_fields">
                <div class="form-group">
                    <label for="payment_slip">แนบสลิปการโอนเงิน (หากชำระเงินผ่านธนาคาร)</label>
                    <input type="file" id="payment_slip" name="payment_slip" accept="image/*">
                </div>

                <div class="form-group" style="text-align: center;">
                    <label><h2>QR CODE</h2></label>
                    <img src="uploads/qrcode.jpg" alt="QR Code" class="qr-code" style="max-width: 400px; height: auto;">
                </div>
            </div>

            <input type="hidden" name="total_price" value="<?php echo htmlspecialchars($total_price); ?>">
            <button type="submit"><i class="fas fa-check"></i> ยืนยันการสั่งซื้อ</button>
        </form>

        <script>
            function toggleUploadFields() {
                const paymentMethod = document.getElementById('payment_method').value;
                const uploadFields = document.getElementById('upload_fields');
                
                if (paymentMethod === 'bank_transfer') {
                    uploadFields.style.display = 'flex';
                } else {
                    uploadFields.style.display = 'none';
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                toggleUploadFields();

                const today = new Date().toISOString().split('T')[0];
                document.getElementById('delivery_date').setAttribute('min', today);
            });
        </script>
    </div>
</body>
</html>