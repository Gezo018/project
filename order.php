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
    
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Itim&display=swap">
    <style>
        :root {
            --primary-color: #FF9A8B;
            --secondary-color: #FF6A88;
            --accent-color: #FF99AC;
            --background-color:  #F9DBBA;
            --text-color: #4A4A4A;
            --card-bg-color: #FFFFFF;
        }
        body {
            font-family: 'Itim', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            color: #4a4a4a;
            background-color: #fff8f0;
        }

        header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 30px;
    background-color: #FFD4DB;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
            display: inline-block;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            text-align: center;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(255, 154, 139, 0.4);
            width: 100%;
        }

        .form-group button:hover {
            background: linear-gradient(45deg, var(--secondary-color), var(--primary-color));
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 8px 20px rgba(255, 154, 139, 0.6);
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
                    <label for="size">ขนาด:</label>
                    <select id="size" name="size" onchange="updatePrice()">
                        <?php
                        // Dynamically generate size options based on product type
                        switch ($product['type']) {
                            case 'Pound':
                                echo '<option value="1 ปอนด์" data-price="250">1 ปอนด์ - ฿250.00</option>';
                                echo '<option value="1.5 ปอนด์" data-price="350">1.5 ปอนด์ - ฿350.00</option>';
                                echo '<option value="2 ปอนด์" data-price="500">2 ปอนด์ - ฿500.00</option>';
                                break;
                            case 'Tray':
                                echo '<option value="ถาด" data-price="150">เค้กไข่ - ฿150.00</option>';
                                echo '<option value="ถาด" data-price="160">เค้กโบราณ - ฿160.00</option>';
                                echo '<option value="ถาด" data-price="200">มินิเค้ก - ฿200.00</option>';
                                break;
                            case 'Box':
                                echo '<option value="กล่อง" data-price="200">คัพเค้ก - ฿200.00</option>';
                                echo '<option value="กล่อง" data-price="240">คัพเค้กบัตเตอร์ - ฿240.00</option>';
                                echo '<option value="กล่อง" data-price="300">บราวนี่จิ๋ว - ฿300.00</option>';
                                echo '<option value="กล่อง" data-price="35">ปุยฝ้าย - ฿35.00</option>';
                                break;
                            case 'Tuy':
                                echo '<option value="อื่นๆ" data-price="50">ขนมปุยฝ้าย - ฿50.00</option>';
                                echo '<option value="อื่นๆ" data-price="100">เค้กถ้วย - ฿100.00</option>';
                                break;
                            case 'Each':
                                echo '<option value="ชิ้น" data-price="50">เค้กครึ่งปอนด์ - ฿50.00</option>';
                                break;
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="flavor">เลือกรสชาติเนื้อเค้ก:</label>
                    <select id="flavor" name="flavor" onchange="updatePrice()">
                        <option value="ปกติ">ปกติ</option>
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
                    ราคา: ฿<span id="price"><?php echo number_format($product['price'], 2); ?></span>
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
        function updatePrice() {
            const sizeSelect = document.getElementById('size');
            const quantityInput = document.getElementById('quantity');
            const flavorSelect = document.getElementById('flavor');
            const priceSpan = document.getElementById('price');
            const priceInput = document.getElementById('price_input');

            const selectedSizeOption = sizeSelect.options[sizeSelect.selectedIndex];
            const pricePerUnit = parseFloat(selectedSizeOption.getAttribute('data-price')) || 0;
            const quantity = parseInt(quantityInput.value) || 1;

            // Add logic for flavor pricing if necessary
            const selectedFlavor = flavorSelect.value;
            let flavorPrice = 0;

            // Example: adding extra cost for specific flavors (if needed)
            if (selectedFlavor === 'ช็อกโกแลต') {
                flavorPrice = 20; // ฿20 extra for chocolate flavor
            } else if (selectedFlavor === 'มะพร้าว') {
                flavorPrice = 20; // ฿20 extra for coconut flavor
            }   

            const totalPrice = (pricePerUnit + flavorPrice) * quantity;

            priceSpan.textContent = totalPrice.toFixed(2);
            priceInput.value = totalPrice.toFixed(2);
        }

        document.getElementById('quantity').addEventListener('input', updatePrice);
        document.getElementById('flavor').addEventListener('change', updatePrice);
        updatePrice(); // Initialize the price when the page loads
    </script>
</body>
</html>
