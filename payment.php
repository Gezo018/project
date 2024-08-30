<?php
session_start();
include 'db_connection.php'; // ตรวจสอบว่ามีการกำหนดตัวแปร $pdo หรือ $conn ในไฟล์นี้
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    echo "<div class='container'><h2>Please log in to proceed.</h2></div>";
    exit();
}

$user_id = $_SESSION['user_id'];
$payment_method = $_POST['payment_method'];
$total_price = floatval($_POST['total_price']);

// Process payment details
if ($payment_method === 'cash_on_delivery') {
    $total_price /= 2; // ลดราคา 50% สำหรับการชำระเงินปลายทาง
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>การชำระเงิน - บ้านแฟรงค์เบเกอร์</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;600&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Prompt', sans-serif;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .payment-title {
            font-size: 24px;
            color: #4CAF50;
            margin-bottom: 20px;
        }
        .payment-section {
            margin-bottom: 20px;
        }
        .payment-section img {
            max-width: 200px;
            height: auto;
            display: block;
            margin: 10px 0;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input[type="file"] {
            padding: 5px;
        }
        .button {
            display: inline-block;
            background-color: #4CAF50;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            text-align: center;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
        }
        .button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="payment-title">
            <i class="fas fa-credit-card"></i> การชำระเงิน
        </h2>

        <div class="payment-section">
            <h3>ข้อมูลการชำระเงิน</h3>
            <?php if ($payment_method === 'bank_transfer'): ?>
                <p>กรุณาโอนเงินจำนวน: ฿<?php echo number_format($total_price, 2); ?></p>
                <img src="path/to/your/qrcode.png" alt="QR Code for Bank Transfer">
                <p>กรุณาแนบรูปสลีฟการโอนเงินด้านล่าง:</p>
                <form action="process_payment.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="receipt">อัปโหลดสลิปการโอนเงิน:</label>
                        <input type="file" id="receipt" name="receipt" required>
                    </div>
                    <input type="hidden" name="payment_method" value="<?php echo htmlspecialchars($payment_method); ?>">
                    <input type="hidden" name="total_price" value="<?php echo number_format($total_price, 2); ?>">
                    <button type="submit" class="button">ยืนยันการชำระเงิน</button>
                </form>
            <?php elseif ($payment_method === 'cash_on_delivery'): ?>
                <p>คุณจะต้องชำระเงินจำนวนครึ่งหนึ่งของยอดรวมทั้งหมดเมื่อได้รับสินค้า: ฿<?php echo number_format($total_price, 2); ?></p>
                <p>กรุณาตรวจสอบรายละเอียดการสั่งซื้อของคุณและเตรียมเงินสดให้พร้อมเมื่อได้รับสินค้า.</p>
                <form action="process_payment.php" method="POST">
                    <input type="hidden" name="payment_method" value="<?php echo htmlspecialchars($payment_method); ?>">
                    <input type="hidden" name="total_price" value="<?php echo number_format($total_price, 2); ?>">
                    <button type="submit" class="button">ยืนยันการสั่งซื้อ</button>
                </form>
            <?php else: ?>
                <p>คุณเลือกชำระด้วยบัตรเครดิต. ระบบจะดำเนินการชำระเงินผ่านบัตรเครดิตของคุณโดยอัตโนมัติ.</p>
                <form action="process_payment.php" method="POST">
                    <input type="hidden" name="payment_method" value="<?php echo htmlspecialchars($payment_method); ?>">
                    <input type="hidden" name="total_price" value="<?php echo number_format($total_price, 2); ?>">
                    <button type="submit" class="button">ยืนยันการสั่งซื้อ</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
