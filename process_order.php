<?php
session_start();
include 'db_connection.php';
require 'vendor/autoload.php'; // Include Composer autoloader

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<div class='container'><h2 class='error'>กรุณาเข้าสู่ระบบเพื่อดำเนินการต่อ</h2></div>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Sanitize and validate input data
$customer_name = filter_var($_POST['name'] ?? '', FILTER_SANITIZE_STRING);
$customer_address = filter_var($_POST['address'] ?? '', FILTER_SANITIZE_STRING);
$customer_phone = filter_var($_POST['phone'] ?? '', FILTER_SANITIZE_STRING);
$customer_email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$payment_method = filter_var($_POST['payment_method'] ?? '', FILTER_SANITIZE_STRING);
$total_price = floatval($_POST['total_price'] ?? 0.00);
$payment_slip = null;
$upload_file = null;

// Handle payment slip upload if payment method is bank_transfer
if ($payment_method === 'bank_transfer' && isset($_FILES['payment_slip']) && $_FILES['payment_slip']['error'] === UPLOAD_ERR_OK) {
    $payment_slip = file_get_contents($_FILES['payment_slip']['tmp_name']);
    $upload_file = $_FILES['payment_slip']['tmp_name']; // Path to the uploaded file
}

// Ensure the cart is not empty
$stmt = $pdo->prepare("SELECT id FROM cart WHERE customer_id = ? AND status = 'active'");
$stmt->execute([$user_id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$result) {
    echo "<div class='container'>
            <h2 class='error'>ตะกร้าสินค้าของคุณว่างเปล่า</h2>
            <a href='index.php' class='button home-button'>กลับไปหน้าหลัก</a>
          </div>";
    exit();
}

$cart_id = $result['id'];

try {
    // Begin transaction
    $pdo->beginTransaction();

    // Insert order into orders table
    $stmt = $pdo->prepare("INSERT INTO orders (customer_id, customer_name, customer_address, customer_phone, note, payment_method, total_price, payment_slip, status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
    $stmt->execute([$user_id, $customer_name, $customer_address, $customer_phone, '', $payment_method, $total_price, $payment_slip]);

    $order_id = $pdo->lastInsertId();

    // Move cart items to order_items table
    $stmt = $pdo->prepare("
        INSERT INTO order_items (order_id, product_id, quantity, price, size, flavor, custom_message)
        SELECT ?, product_id, quantity, price, size, flavor, custom_message
        FROM cart_items
        WHERE cart_id = ?
    ");
    $stmt->execute([$order_id, $cart_id]);

    // Fetch cake details for email and LINE Notify
    $stmt = $pdo->prepare("
        SELECT ci.quantity, p.name AS cake_name, ci.size, ci.flavor, ci.custom_message 
        FROM cart_items ci 
        JOIN products p ON ci.product_id = p.id 
        WHERE ci.cart_id = ?
    ");
    $stmt->execute([$cart_id]);
    $cake_details = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Clear the cart
    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE cart_id = ?");
    $stmt->execute([$cart_id]);

    $stmt = $pdo->prepare("UPDATE cart SET status = 'completed' WHERE id = ?");
    $stmt->execute([$cart_id]);

    // Commit transaction
    $pdo->commit();

    // Format cake details for email and LINE Notify
    $cake_details_text = "";
    foreach ($cake_details as $cake) {
        $cake_details_text .= "- เค้ก: {$cake['cake_name']} ขนาด: {$cake['size']} รสชาติ: {$cake['flavor']} จำนวน: {$cake['quantity']} " . (!empty($cake['custom_message']) ? "(ข้อความพิเศษ: {$cake['custom_message']})" : "") . "\n";
    }

    // Send email notification
    sendOrderEmail($customer_email, $order_id, $customer_name, $customer_phone, $customer_address, $cake_details_text, $payment_method, $total_price, $upload_file);

    // Send LINE notifications
    sendLineNotify("คำสั่งซื้อ #$order_id\nชื่อ: $customer_name\nโทรศัพท์: $customer_phone\nที่อยู่: $customer_address\nรายละเอียดเค้ก:\n$cake_details_text\nยอดรวม: ฿" . number_format($total_price, 2) . "\nวิธีการชำระเงิน: $payment_method");
    if ($upload_file) {
        sendLineNotifyWithImage("คำสั่งซื้อ #$order_id\nชื่อ: $customer_name\nโทรศัพท์: $customer_phone\nที่อยู่: $customer_address\nรายละเอียดเค้ก:\n$cake_details_text\nยอดรวม: ฿" . number_format($total_price, 2) . "\nวิธีการชำระเงิน: $payment_method", $upload_file);
    }

    echo "<div class='container'>
            <h2 class='success'>รายละเอียดการสั่งซื้อและใบเสร็จการชำระเงินถูกส่งเรียบร้อยแล้ว</h2>
            <a href='index.php' class='button home-button'>กลับไปหน้าหลัก</a>
          </div>";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "<div class='container'><h2 class='error'>เกิดข้อผิดพลาด: {$e->getMessage()}</h2></div>";
}

// Function to send order email
function sendOrderEmail($customer_email, $order_id, $customer_name, $customer_phone, $customer_address, $cake_details_text, $payment_method, $total_price, $upload_file = '') {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'baanfrankbaker@gmail.com'; // Your email address
        $mail->Password = 'saik pihg veeb qwnj'; // Your email password (Consider using an App Password)
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('baanfrankbaker@gmail.com', 'baanfrankbaker');
        $mail->addAddress($customer_email);
        $mail->addAddress('baanfrankbaker@gmail.com'); // Send a copy to yourself

        if ($payment_method === 'bank_transfer' && $upload_file !== '') {
            $mail->addAttachment($upload_file); 
            $cid = $mail->addEmbeddedImage($upload_file, 'receipt_image');

            $mail->isHTML(true);
            $mail->Subject = "New order #$order_id!";
            $mail->Body = "
                <h2>รายละเอียดการสั่งซื้อ</h2>
                <p><strong>เลขที่คำสั่งซื้อ:</strong> $order_id</p>
                <p><strong>ชื่อผู้สั่งซื้อ:</strong> $customer_name</p>
                <p><strong>โทรศัพท์:</strong> $customer_phone</p>
                <p><strong>ที่อยู่:</strong> $customer_address</p>
                <p><strong>รายละเอียดเค้ก:</strong><br>$cake_details_text</p>
                <p><strong>วิธีการชำระเงิน:</strong> $payment_method</p>
                <p><strong>ยอดรวม:</strong> ฿" . number_format($total_price, 2) . "</p>
                <p><img src='cid:receipt_image' alt='Payment Slip' style='width:100%; max-width:600px;' /></p>";
        } else {
            $mail->isHTML(true);
            $mail->Subject = "New order #$order_id!";
            $mail->Body = "
                <h2>รายละเอียดการสั่งซื้อ</h2>
                <p><strong>เลขที่คำสั่งซื้อ:</strong> $order_id</p>
                <p><strong>ชื่อผู้สั่งซื้อ:</strong> $customer_name</p>
                <p><strong>โทรศัพท์:</strong> $customer_phone</p>
                <p><strong>ที่อยู่:</strong> $customer_address</p>
                <p><strong>รายละเอียดเค้ก:</strong><br>$cake_details_text</p>
                <p><strong>วิธีการชำระเงิน:</strong> $payment_method</p>
                <p><strong>ยอดรวม:</strong> ฿" . number_format($total_price, 2) . "</p>";
        }

        $mail->send();
    } catch (Exception $e) {
        echo "<div class='container'><h2 class='error'>ไม่สามารถส่งอีเมลได้: {$mail->ErrorInfo}</h2></div>";
    }
}

// LINE Notify functions
function sendLineNotify($message) {
    $lineToken = '0LSe9kyUCAUJxX8JigDnF7Mqdx2wMAW8nvejrXdD35Y'; // Your Line Token
    $lineApiUrl = "https://notify-api.line.me/api/notify";

    $headers = [
        "Authorization: Bearer " . $lineToken,
        "Content-Type: application/x-www-form-urlencoded"
    ];

    $postData = http_build_query(['message' => $message]);

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

function sendLineNotifyWithImage($message, $imageFilePath) {
    $lineToken = '0LSe9kyUCAUJxX8JigDnF7Mqdx2wMAW8nvejrXdD35Y'; // Your Line Token
    $lineApiUrl = "https://notify-api.line.me/api/notify";

    $headers = [
        "Authorization: Bearer " . $lineToken,
        "Content-Type: multipart/form-data"
    ];

    $postData = [
        'message' => $message,
        'imageFile' => curl_file_create($imageFilePath)
    ];

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
<style>
    .container {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
        font-family: 'Itim', sans-serif;
    }

    h2.success {
        color: #4CAF50;
        text-align: center;
    }

    h2.error {
        color: #F44336;
        text-align: center;
    }

    .button {
        display: inline-block;
        background-color: #FF9800;
        color: white;
        padding: 10px 20px;
        margin: 20px 0;
        text-align: center;
        text-decoration: none;
        border-radius: 4px;
        font-size: 18px;
        font-family: 'Itim', sans-serif;
        width: 100%;
    }

    .button:hover {
        background-color: #F57C00;
    }

    .home-button {
        text-align: center;
        display: block;
        width: 100%;
    }
</style>