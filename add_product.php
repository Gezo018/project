<?php
// Start the session
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bakery_shop";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ensure that all required fields are set
    $name = $_POST['name'] ?? null;
    $description = $_POST['description'] ?? null;
    $price = $_POST['price'] ?? null;
    $image = $_FILES['image']['name'] ?? null;

    // Check if all required fields are filled
    if (!$name || !$description || !$price || !$image) {
        $error = "All fields are required.";
    } else {
        // Save the image to the server
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($image);

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            // Insert the new product into the database
            $stmt = $conn->prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssds", $name, $description, $price, $target_file);

            if ($stmt->execute()) {
                $success = "Product added successfully!";
            } else {
                $error = "Failed to add product. Please try again.";
            }

            $stmt->close();
        } else {
            $error = "Failed to upload image.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Bakery Shop Admin</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4e1d2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .add-product-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }

        .add-product-container h2 {
            margin-bottom: 20px;
            color: #a56336;
        }

        .add-product-container input[type="text"],
        .add-product-container textarea,
        .add-product-container input[type="number"],
        .add-product-container input[type="file"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .add-product-container button {
            width: 100%;
            padding: 10px;
            background-color: #a56336;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .add-product-container button:hover {
            background-color: #d4a373;
        }

        .message {
            margin-bottom: 20px;
            color: #e76f51;
        }

        .success {
            color: #2ecc71;
        }
    </style>
</head>
<body>
    <div class="add-product-container">
        <h2>Add New Product</h2>
        <?php if (isset($error)): ?>
            <div class="message"><?php echo $error; ?></div>
        <?php elseif (isset($success)): ?>
            <div class="message success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form action="add_product.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Product Name" required>
            <textarea name="description" placeholder="Product Description" required></textarea>
            <input type="number" name="price" placeholder="Price" step="0.01" required>
            <input type="file" name="image" accept="image/*" required>
            <button type="submit">Add Product</button>
        </form>
    </div>
</body>
</html>
