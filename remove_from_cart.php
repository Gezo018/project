<?php
session_start();
include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<div class='container'><h2>Please log in to remove items from your cart.</h2></div>";
    exit();
}

if (isset($_POST['cart_item_id'])) {
    $cart_item_id = intval($_POST['cart_item_id']);
    $user_id = $_SESSION['user_id'];

    // Check if the item exists in the cart of the logged-in user
    $stmt = $pdo->prepare("
        DELETE FROM cart_items
        WHERE id = ? AND cart_id IN (
            SELECT id FROM cart WHERE customer_id = ? AND status = 'active'
        )
    ");
    $stmt->execute([$cart_item_id, $user_id]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['message'] = "Item removed from cart successfully.";
    } else {
        $_SESSION['message'] = "Failed to remove item from cart.";
    }
} else {
    $_SESSION['message'] = "No item specified for removal.";
}

// Redirect back to cart page
header('Location: cart.php');
exit();
?>
