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

// Get product ID from the URL
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

if ($product_id > 0) {
    // Fetch product details from the database
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        // Product not found
        $_SESSION['error'] = "Product not found.";
        header("Location: type.php");
        exit();
    }
} else {
    // Invalid product ID
    $_SESSION['error'] = "Invalid product selected.";
    header("Location: type.php");
    exit();
}

include('navbar.php');  // Include the updated navbar
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สั่งซื้อ - <?php echo htmlspecialchars($product['name']); ?></title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Itim&display=swap">
    <style>
        body {
            font-family: 'Itim', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            color: #4a4a4a;
            background-color: #fff8f0;
        }

        header {
            padding: 20px;
            background-color: #f4e1d2;
            color: #4a4a4a;
            text-align: center;
            font-size: 24px;
            font-weight: 300;
        }

        .navbar {
            background-color: #a56336; /* Match the button color */
            padding: 10px 20px;
        }

        .navbar .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-brand {
            color: #fff;
            font-size: 24px;
            font-weight: bold;
            text-decoration: none;
        }

        .navbar-nav {
            list-style: none;
            display: flex;
            margin: 0;
            padding: 0;
        }

        .navbar-nav li {
            margin-left: 20px;
        }

        .navbar-nav a {
            color: #fff;
            font-size: 16px;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .navbar-nav a:hover {
            color: #d4a373; /* Match the hover color of the button */
        }

        .container {
            padding: 20px;
        }

        .order-title {
            text-align: center;
            color: #a56336;
            margin-bottom: 20px;
        }

        .order-form {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-size: 16px;
            color: #333;
        }

        .form-group input[type="text"],
        .form-group textarea,
        .form-group select,
        .form-group input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-group select {
            width: 100%;
        }

        .form-group .price {
            font-size: 18px;
            color: #a56336;
            margin-top: 10px;
        }

        .form-group button {
            background-color: #a56336;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        .form-group button:hover {
            background-color: #d4a373;
        }

        .total-price {
            font-size: 18px;
            color: #a56336;
            margin-top: 20px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="order-title">สั่งซื้อ - <?php echo htmlspecialchars($product['name']); ?></h2>
        <div class="order-form">    
            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width:100%; border-radius:10px; margin-bottom:20px;">
            <form action="add_to_cart.php" method="POST">
                <div class="form-group">
                    <label for="type">ประเภท:</label>
                    <select id="type" name="type" onchange="updateSizeOptions()">
                        <option value="pound">เลือกขนาดปอนด์</option>
                        <option value="piece">เลือกเป็นชิ้น</option>
                        <option value="tray">เลือกเป็นถาด</option>
                        <option value="box">เลือกเป็นกล่อง</option>
                        <option value="tuy">เลือกเป็นถ้วย</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="size">ขนาด:</label>
                    <select id="size" name="size" onchange="updatePrice()">
                        <!-- Options will be dynamically inserted by JavaScript -->
                    </select>
                </div>

                <div class="form-group">
                    <label for="flavor">เลือกรสชาติเนื้อเค้ก:</label>
                    <select id="flavor" name="flavor">
                        <option value="ช็อกโกแลต">ช็อกโกแลต</option>
                        <option value="สตอเบอรี่">สตอเบอรี่</option>
                        <option value="วนิลา">วนิลา</option>
                        <option value="มะพร้าว">มะพร้าว</option>
                        <option value="ใบเตย">ใบเตย</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="quantity">จำนวน:</label>
                    <input type="number" id="quantity" name="quantity" value="1" min="1" required onchange="updatePrice()">
                </div>

                <div class="form-group total-price">
                    ราคา: ฿<span id="price">0.00</span>
                </div>
                <div class="form-group">
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product_id); ?>">
                    <input type="hidden" id="price_input" name="price" value="0">
                    <button type="submit">เพิ่มไปยังตะกร้า</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function updateSizeOptions() {
            const typeSelect = document.getElementById('type');
            const sizeSelect = document.getElementById('size');
            const selectedType = typeSelect.value;

            sizeSelect.innerHTML = ''; // Clear existing options

            if (selectedType === 'pound') {
                // Add pound options
                sizeSelect.innerHTML = `
                    <option value="1" data-price="250">1 ปอนด์ - ฿250.00</option>
                    <option value="1.5" data-price="350">1.5 ปอนด์( จะเป็นเค้ก 2 ชั้น ) - ฿350.00</option>
                    <option value="2" data-price="500">2 ปอนด์ - ฿500.00</option>
                `;
            } else if (selectedType === 'piece') {
                // Add piece options
                sizeSelect.innerHTML = `
                    <option value="piece" data-price="50
                    ">1 ชิ้น - ฿50.00</option>
                `;
            } else if (selectedType === 'tray') {
                // Add piece options
                sizeSelect.innerHTML = `
                    <option value="tray" data-price="150">เค้กไข่ 1 ถาด - ฿150.00</option>
                    <option value="tray" data-price="160">เค้กโบราณ 1 ถาด- ฿160.00</option>
                    <option value="tray" data-price="250">มินิเค้ก 1 ถาด - ฿250.00</option>
                `;
            }   else if (selectedType === 'box') {
                // Add piece options
                sizeSelect.innerHTML = `
                    <option value="tray" data-price="200">คัพเค้ก 1 กล่อง - ฿200.00</option>
                    <option value="tray" data-price="300">บราวนี่จิ๋ว 1 กล่อง - ฿300.00</option>
                    
                `;
            }else if (selectedType === 'tuy') {
                // Add piece options
                sizeSelect.innerHTML = `
                    <option value="tuy" data-price="100">4 ถ้วย - ฿100.00</option>
                    
                `;
            }

            // Update price based on the first available option
            updatePrice();
        }

        function updatePrice() {
            const sizeSelect = document.getElementById('size');
            const quantityInput = document.getElementById('quantity');
            const priceSpan = document.getElementById('price');
            const priceInput = document.getElementById('price_input');

            const selectedOption = sizeSelect.options[sizeSelect.selectedIndex];
            const pricePerUnit = parseFloat(selectedOption.getAttribute('data-price')) || 0;
            const quantity = parseInt(quantityInput.value) || 1;

            const totalPrice = pricePerUnit * quantity;

            priceSpan.textContent = totalPrice.toFixed(2);
            priceInput.value = totalPrice.toFixed(2);
        }

        // Initialize the size options and price on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateSizeOptions();
        });
    </script>
</body>
</html>
