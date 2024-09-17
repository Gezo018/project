<?php
session_start();
include 'db_connection.php';
require 'vendor/autoload.php'; // Include Composer autoloader

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<div class='container'><h2 class='error'>กรุณาเข้าสู่ระบบเพื่อดำเนินการต่อ.</h2></div>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Retrieve customer details from the checkout form
$customer_name = $_POST['name'] ?? '';
$customer_address = $_POST['address'] ?? '';
$customer_phone = $_POST['phone'] ?? '';
$customer_email = $_POST['email'] ?? '';
$cake_details = $_POST['cake_details'] ?? '';
$payment_method = $_POST['payment_method'] ?? '';
$total_price = floatval($_POST['total_price']);

// Get the active cart for the user
$stmt = $pdo->prepare("SELECT id FROM cart WHERE customer_id = ? AND status = 'active'");
$stmt->execute([$user_id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$result) {
    echo "<div class='container'>
            <h2 class='error'>ตะกร้าสินค้าของคุณว่างเปล่า.</h2>
            <a href='index.php' class='button home-button'>กลับไปหน้าหลัก</a>
          </div>";
    exit();
}

$cart_id = $result['id'];

// Insert order into the orders table
$stmt = $pdo->prepare("
    INSERT INTO orders (customer_id, customer_name, customer_address, customer_phone, note, payment_method, total_price, status, created_at)
    VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
");
$stmt->execute([$user_id, $customer_name, $customer_address, $customer_phone, $cake_details, $payment_method, $total_price]);

$order_id = $pdo->lastInsertId();

// Move cart items to order_items table
$stmt = $pdo->prepare("
    INSERT INTO order_items (order_id, product_id, quantity, price, size, flavor, custom_message)
    SELECT ?, product_id, quantity, price, size, flavor, custom_message
    FROM cart_items
    WHERE cart_id = ?
");
$stmt->execute([$order_id, $cart_id]);

// Clear the cart
$stmt = $pdo->prepare("DELETE FROM cart_items WHERE cart_id = ?");
$stmt->execute([$cart_id]);

$stmt = $pdo->prepare("UPDATE cart SET status = 'completed' WHERE id = ?");
$stmt->execute([$cart_id]);

// Prepare and send email
$mail = new PHPMailer(true);

try {
    // SMTP server configuration
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'baanfrankbaker@gmail.com'; // Your email address
    $mail->Password = 'saik pihg veeb qwnj'; // Your email password (Consider using an App Password)
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Recipients
    $mail->setFrom('baanfrankbaker@gmail.com', 'baanfrankbaker');
    $mail->addAddress($customer_email); // Send to customer
    $mail->addAddress('baanfrankbaker@gmail.com'); // Send to your store's email

    // Attach payment receipt if uploaded
    if ($payment_method === 'bank_transfer' && isset($_FILES['receipt']) && $_FILES['receipt']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        $upload_file = $upload_dir . basename($_FILES['receipt']['name']);

        if (move_uploaded_file($_FILES['receipt']['tmp_name'], $upload_file)) {
            $mail->addAttachment($upload_file);

            // Send image via Line Notify
            sendLineNotifyWithImage(
                "คำสั่งซื้อ #$order_id\nชื่อ: $customer_name\nโทรศัพท์: $customer_phone\nที่อยู่: $customer_address\nรายละเอียดเค้ก: $cake_details\nยอดรวม: ฿" . number_format($total_price, 2) . "\nวิธีการชำระเงิน: $payment_method",
                $upload_file
            );
        } else {
            echo "<div class='container'><h2 class='error'>ไม่สามารถอัปโหลดสลิปได้</h2></div>";
            exit();
        }
    }

    // Email content in Thai
    $mail->isHTML(true);
    $mail->Subject = 'New order!';
    $mail->Body = "
        <h2>รายละเอียดการสั่งซื้อ</h2>
        <p><strong>ชื่อผู้สั่งซื้อ:</strong> $customer_name</p>
        <p><strong>โทรศัพท์:</strong> $customer_phone</p>
        <p><strong>ที่อยู่:</strong> $customer_address</p>
        <p><strong>รายละเอียดเค้ก:</strong> $cake_details</p>
        <p><strong>วิธีการชำระเงิน:</strong> $payment_method</p>
        <p><strong>ยอดรวม:</strong> ฿" . number_format($total_price, 2) . "</p>
        <p>โปรดตรวจสอบใบเสร็จที่แนบมาด้วย</p>";

    $mail->send();

    // Send LINE Notify message
    sendLineNotify(
        "คำสั่งซื้อ #$order_id\nชื่อ: $customer_name\nโทรศัพท์: $customer_phone\nที่อยู่: $customer_address\nรายละเอียดเค้ก: $cake_details\nยอดรวม: ฿" . number_format($total_price, 2) . "\nวิธีการชำระเงิน: $payment_method"
    );

    echo "<div class='container'>
            <h2 class='success'>รายละเอียดการสั่งซื้อและใบเสร็จการชำระเงินถูกส่งเรียบร้อยแล้ว</h2>
            <a href='index.php' class='button home-button'>กลับไปหน้าหลัก</a>
          </div>";
} catch (Exception $e) {
    echo "<div class='container'><h2 class='error'>ไม่สามารถส่งอีเมลได้: {$mail->ErrorInfo}</h2></div>";
}

// Line Notify functions
function sendLineNotify($message) {
    $lineToken = '0LSe9kyUCAUJxX8JigDnF7Mqdx2wMAW8nvejrXdD35Y'; // Your Line Token
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
    body {
        font-family: 'Itim', sans-serif;
        background-color: #F9DBBA;
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

    .success {
        color: #28a745;
    }

    .error {
        color: #dc3545;
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

    .home-button {
        background-color: #28a745;
    }

    .submit-button {
        background-color: #007bff;
    }
</style>
