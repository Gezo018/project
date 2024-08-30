<?php
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['item_id']) && isset($_POST['change'])) {
    $item_id = intval($_POST['item_id']);
    $change = intval($_POST['change']);

    // Update the quantity in the database
    $stmt = $pdo->prepare("
        UPDATE cart_items 
        SET quantity = quantity + ?
        WHERE id = ? AND cart_id = (SELECT id FROM cart WHERE customer_id = ? AND status = 'active')
    ");
    $stmt->execute([$change, $item_id, $user_id]);

    if ($stmt->rowCount() > 0) {
        echo "Quantity updated";
    } else {
        http_response_code(400);
        echo "Error updating quantity";
    }
} else {
    http_response_code(400);
    echo "Invalid request";
}
?>
