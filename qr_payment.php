<?php
session_start();
include 'db_connection.php'; // Include the database connection file

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Load PHPMailer autoload

if (!isset($_GET['order_id']) || !isset($_GET['total_price'])) {
    echo "<div class='container'><h2 class='error'>ไม่พบข้อมูลการชำระเงิน</h2></div>";
    exit();
}

$order_id = $_GET['order_id'];
$total_price = $_GET['total_price'];

// Fetch order details
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "<div class='container'><h2 class='error'>ไม่พบคำสั่งซื้อ</h2></div>";
    exit();
}

// Handle receipt upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['receipt']) && $_FILES['receipt']['error'] === UPLOAD_ERR_OK) {
    $receipt = $_FILES['receipt'];
    $upload_dir = 'uploads/';
    $upload_file = $upload_dir . basename($receipt['name']);
    
    // Allowed file types
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($receipt['type'], $allowed_types)) {
        echo "<div class='container'><h2 class='error'>ประเภทไฟล์ไม่ถูกต้อง. โปรดอัปโหลดไฟล์รูปภาพ.</h2></div>";
        exit();
    }

    // File size check
    if ($receipt['size'] > 5000000) { // Limit to 5MB
        echo "<div class='container'><h2 class='error'>ขนาดไฟล์ใหญ่เกินไป. โปรดอัปโหลดไฟล์ที่มีขนาดไม่เกิน 5MB.</h2></div>";
        exit();
    }

    // Move uploaded file
    if (move_uploaded_file($receipt['tmp_name'], $upload_file)) {
        // Send email notification
        sendEmailNotification($upload_file, $order_id, $total_price);
        
        // Send LINE Notify
        sendLineNotify("การอัปโหลดสลิปการชำระเงินสำเร็จ: $upload_file");
        
        // Redirect to process_order.php
        header("Location: process_order.php?order_id=$order_id&total_price=$total_price");
        exit();
    } else {
        echo "<div class='container'><h2 class='error'>ไม่สามารถอัปโหลดสลิปได้.</h2></div>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Payment</title>
    <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Itim', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .qr-code img {
            max-width: 100%;
            height: auto;
        }

        .order-details {
            margin-top: 20px;
            text-align: left;
        }

        .order-details p {
            margin: 5px 0;
        }

        .upload-receipt {
            margin-top: 20px;
        }

        .button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            text-decoration: none;
        }

        .submit-button {
            background-color: #28a745;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>ชำระเงินผ่าน QR Code</h2>
        <div class="qr-code">
            <img src="qr_code_image.png" alt="QR Code">
        </div>
        <div class="order-details">
            <p><strong>หมายเลขคำสั่งซื้อ:</strong> <?php echo $order_id; ?></p>
            <p><strong>ยอดชำระ:</strong> ฿<?php echo $total_price; ?></p>
        </div>

        <div class="upload-receipt">
            <form action="qr_payment.php?order_id=<?php echo $order_id; ?>&total_price=<?php echo $total_price; ?>" method="post" enctype="multipart/form-data">
                <label for="receipt">อัปโหลดสลิปการชำระเงิน:</label>
                <input type="file" name="receipt" id="receipt" required>
                <br>
                <button type="submit" class="button submit-button">ส่งสลิป</button>
            </form>
        </div>
    </div>
</body>
</html>

<?php
// Function to send email notification
function sendEmailNotification($filePath, $orderId, $totalPrice) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'baanfrankbaker@gmail.com'; // Your email here
        $mail->Password = 'saik pihg veeb qwnj'; // Your email password here
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('baanfrankbaker@gmail.com', 'baanfrankbaker');
        $mail->addAddress('baanfrankbaker@gmail.com');

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'สลิปการชำระเงินที่อัปโหลด';
        $mail->Body = "<p>สลิปการชำระเงินสำหรับคำสั่งซื้อ #$orderId ได้ถูกอัปโหลดสำเร็จ. ยอดชำระ: ฿$totalPrice</p>";
        $mail->addAttachment($filePath);

        $mail->send();
    } catch (Exception $e) {
        echo "<div class='container'><h2 class='error'>ไม่สามารถส่งอีเมลได้: {$mail->ErrorInfo}</h2></div>";
    }
}

// Function to send LINE Notify
function sendLineNotify($message) {
    $lineToken = 'your_line_notify_token'; // Your LINE Notify token here
    $lineApiUrl = "https://notify-api.line.me/api/notify";
    
    $headers = [
        "Authorization: Bearer " . $lineToken,
        "Content-Type: application/x-www-form-urlencoded"
    ];
    
    $postData = http_build_query([
        'message' => $message
    ]);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $lineApiUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $result = curl_exec($ch);
    curl_close($ch);
    
    return $result;
}
?>
