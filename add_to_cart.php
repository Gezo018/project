<?php
session_start();
include 'db_connection.php';

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือยัง
if (!isset($_SESSION['user_id'])) {
    echo "<div class='container'><h2>Please log in to add items to your cart.</h2></div>";
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id']);
$size = $_POST['size'];
$flavor = $_POST['flavor'];

// ตรวจสอบว่ามีการส่งค่าจากฟอร์มเข้ามาหรือไม่
$custom_message = isset($_POST['custom_message']) ? $_POST['custom_message'] : '';
$quantity = intval($_POST['quantity']);
$price = floatval($_POST['price']);

// ตรวจสอบว่า user_id มีอยู่ในตาราง users หรือไม่
$stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user_exists = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user_exists) {
    echo "<div class='container'><h2>Invalid user ID.</h2></div>";
    exit();
}

// ตรวจสอบว่าผู้ใช้มีตะกร้าสินค้าที่กำลังใช้งานอยู่หรือไม่
$stmt = $pdo->prepare("SELECT id FROM cart WHERE customer_id = ? AND status = 'active'");
$stmt->execute([$user_id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$result) {
    // ถ้าไม่มีตะกร้าสินค้าที่ใช้งานอยู่ สร้างตะกร้าใหม่
    $stmt = $pdo->prepare("INSERT INTO cart (customer_id, status) VALUES (?, 'active')");
    if ($stmt->execute([$user_id])) {
        $cart_id = $pdo->lastInsertId();
    } else {
        echo "<div class='container'><h2>Error: Unable to create new cart.</h2></div>";
        exit();
    }
} else {
    $cart_id = $result['id'];
}

// ตรวจสอบว่ามีสินค้านี้ในตะกร้าแล้วหรือไม่
$stmt = $pdo->prepare("
    SELECT id, quantity 
    FROM cart_items 
    WHERE cart_id = ? AND product_id = ?
");
$stmt->execute([$cart_id, $product_id]);
$existing_item = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existing_item) {
    // อัปเดตจำนวนสินค้า ถ้ามีสินค้านี้อยู่แล้ว
    $new_quantity = $existing_item['quantity'] + $quantity;
    $stmt = $pdo->prepare("
        UPDATE cart_items 
        SET quantity = ?, size = ?, flavor = ?, custom_message = ? 
        WHERE id = ?
    ");
    if (!$stmt->execute([$new_quantity, $size, $flavor, $custom_message, $existing_item['id']])) {
        echo "<div class='container'><h2>Error: Unable to update cart items.</h2></div>";
        exit();
    }
} else {
    // เพิ่มสินค้าใหม่ลงในตะกร้า
    $stmt = $pdo->prepare("
        INSERT INTO cart_items (cart_id, product_id, size, flavor, custom_message, price, quantity) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    if (!$stmt->execute([$cart_id, $product_id, $size, $flavor, $custom_message, $price, $quantity])) {
        echo "<div class='container'><h2>Error: Unable to add item to cart.</h2></div>";
        exit();
    }
}

// เมื่อทำรายการเสร็จเรียบร้อยให้ไปที่หน้าตะกร้าสินค้า
header("Location: cart.php");
exit();
?>