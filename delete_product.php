<?php
session_start();

// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: type.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bakery_shop";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the product ID is set and is valid
if (isset($_POST['product_id']) && is_numeric($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Delete dependent rows in order_items
        $sql = "DELETE FROM order_items WHERE product_id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $stmt->close();
        } else {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }

        // Delete the product
        $sql = "DELETE FROM products WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $_SESSION['message'] = "Product deleted successfully.";
            $stmt->close();
        } else {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }

        // Commit the transaction
        $conn->commit();

    } catch (Exception $e) {
        // Rollback the transaction if an error occurs
        $conn->rollback();
        $_SESSION['error'] = "Failed to delete product: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "Invalid product ID.";
}

$conn->close();

// Redirect back to the type page
header("Location: type.php");
exit();
?>
