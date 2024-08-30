<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    echo "<div class='container'><h2>Please log in to add items to your cart.</h2></div>";
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id']);
$size = $_POST['size'];
$flavor = $_POST['flavor'];
$custom_message = $_POST['custom_message'];
$quantity = intval($_POST['quantity']);
$price = floatval($_POST['price']);

// Check if user has an active cart
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

// Check if the item already exists in the cart
$stmt = $pdo->prepare("
    SELECT id, quantity 
    FROM cart_items 
    WHERE cart_id = ? AND product_id = ?
");
$stmt->execute([$cart_id, $product_id]);
$existing_item = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existing_item) {
    // Update quantity if item exists
    $new_quantity = $existing_item['quantity'] + $quantity;
    $stmt = $pdo->prepare("
        UPDATE cart_items 
        SET quantity = ?, size = ?, flavor = ?, custom_message = ? 
        WHERE id = ?
    ");
    $stmt->execute([$new_quantity, $size, $flavor, $custom_message, $existing_item['id']]);
} else {
    // Add new item to the cart
    $stmt = $pdo->prepare("
        INSERT INTO cart_items (cart_id, product_id, size, flavor, custom_message, price, quantity) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$cart_id, $product_id, $size, $flavor, $custom_message, $price, $quantity]);
}

header("Location: cart.php");
exit();
?>
