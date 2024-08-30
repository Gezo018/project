<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bakery_shop";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add error logging for the query execution
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

if (!$result) {
    die("Error fetching products: " . $conn->error);
}

$cake_types = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cake_types[] = $row;
    }
}

$title = "ประเภทเค้ก - ร้านเบเกอรี่ของเรา";

// Handle form submission for adding a new product
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price_type = $_POST['price_type'];
    $image = $_FILES['image']['name'];
    
    // Determine the price based on selection
    if ($price_type == 'Pound') {
        $price_1_pound = 250;
        $price_2_pound = 500;
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $name, $description, $price_1_pound, $image);
    } else {
        $custom_price = $_POST['custom_price'];
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $name, $description, $custom_price, $image);
    }

    // Save the image to the server
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($image);
    move_uploaded_file($_FILES['image']['tmp_name'], $target_file);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Product added successfully!";
    } else {
        $_SESSION['error'] = "Failed to add product. Please try again.";
    }
    header("Location: type.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Itim&display=swap">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Itim', sans-serif;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            background-color: #fff8f0;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #fff8f0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .cake-types {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-between;
        }

        .cake-item {
            flex: 1 1 calc(33% - 20px);
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .cake-item:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .cake-item .image-container {
            width: 100%;
            height: 200px;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .cake-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
            transition: transform 0.3s ease;
        }

        .cake-item:hover img {
            transform: scale(1.1);
        }

        .cake-item h3 {
            font-size: 20px;
            font-weight: 400;
            color: #555;
            margin-bottom: 10px;
        }

        .cake-item p {
            font-size: 14px;
            color: #777;
            margin-bottom: 20px;
        }

        .cake-item .price {
            font-size: 18px;
            color: #b38b6d;
            font-weight: 500;
            margin-bottom: 20px;
        }

        .cake-item .order-button {
            display: inline-block;
            padding: 10px 30px;
            background-color: #b38b6d;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .cake-item .order-button:hover {
            background-color: #d4a373;
        }

        .admin-actions {
            text-align: center;
            margin-bottom: 40px;
        }

        .add-product-button {
            background-color: #b38b6d;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .add-product-button:hover {
            background-color: #d4a373;
        }

        .message {
            text-align: center;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: bold;
        }

        .message.success {
            background-color: #2ecc71;
            color: #fff;
        }

        .message.error {
            background-color: #e74c3c;
            color: #fff;
        }

        footer {
            text-align: center;
            padding: 20px;
            background-color: #222;
            color: #fff;
            font-size: 14px;
            margin-top: 40px;
            border-top: 2px solid #d4a373;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 10px;
            text-align: center;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }

        .add-product-container input[type="text"],
        .add-product-container textarea,
        .add-product-container input[type="number"],
        .add-product-container input[type="file"],
        .add-product-container select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .add-product-container button {
            width: 100%;
            padding: 10px;
            background-color: #b38b6d;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .add-product-container button:hover {
            background-color: #d4a373;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message success">
                <?php 
                    echo $_SESSION['message'];
                    unset($_SESSION['message']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="message error">
                <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <div class="admin-actions">
                <button id="openModal" class="add-product-button">Add New Product</button>
            </div>
        <?php endif; ?>

        <div class="cake-types">
            <?php foreach ($cake_types as $cake): ?>
                <div class="cake-item">
                    <div class="image-container">
                        <img src="<?php echo htmlspecialchars($cake['image']); ?>" alt="<?php echo htmlspecialchars($cake['name']); ?>">
                    </div>
                    <h3><?php echo htmlspecialchars($cake['name']); ?></h3>
                    <p><?php echo htmlspecialchars($cake['description']); ?></p>
                    <p class="price">เริ่มต้นที่ ฿<?php echo number_format($cake['price'], 2); ?></p>
                    <form action="order.php" method="GET">
                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($cake['id']); ?>">
                        <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($cake['name']); ?>">
                        <input type="hidden" name="product_price" id="hidden-price-<?php echo htmlspecialchars($cake['id']); ?>" value="<?php echo number_format($cake['price'], 2); ?>">
                        <button type="submit" class="order-button">สั่งซื้อ</button>
                    </form>

                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <form action="delete_product.php" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($cake['id']); ?>">
                            <button type="submit" class="delete-button">Delete</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

   

    <!-- The Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add New Product</h2>
            <form action="type.php" method="POST" enctype="multipart/form-data" class="add-product-container">
                <input type="text" name="name" placeholder="Product Name" required>
                <textarea name="description" placeholder="Product Description" required></textarea>
                <select name="price_type" id="price_type" required>
                    <option value="Pound">Pound</option>
                    <option value="Each">Each</option>
                </select>
                <input type="number" name="custom_price" id="custom_price_input" placeholder="Custom Price" step="0.01" style="display:none;">
                <input type="file" name="image" accept="image/*" required>
                <button type="submit">Add Product</button>
            </form>
        </div>
    </div>

    <script>
        var modal = document.getElementById("myModal");
        var btn = document.getElementById("openModal");
        var span = document.getElementsByClassName("close")[0];

        btn.onclick = function() {
            modal.style.display = "block";
        }

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        // Show custom price input if "Each" is selected
        const priceTypeSelect = document.getElementById('price_type');
        const customPriceInput = document.getElementById('custom_price_input');

        priceTypeSelect.addEventListener('change', function() {
            if (this.value === 'Each') {
                customPriceInput.style.display = 'block';
            } else {
                customPriceInput.style.display = 'none';
            }
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>
