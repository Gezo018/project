<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Define the order page variables
$title = "สั่งออเดอร์เค้กช็อกโกแลต - ร้านเบเกอรี่ของเรา";
$cake_name = "เค้กช็อกโกแลต";
$cake_image = "choc.jpg";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Save the order to the database or send the order details via email
    // (For this example, we'll just display the order confirmation)
    $_SESSION['order_confirmation'] = [
        'name' => $_POST['name'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'],
        'flavor' => $_POST['flavor'],
        'pound' => $_POST['pound'],
        'date' => $_POST['date'],
        'message' => $_POST['message'],
        'price' => $_POST['price']
    ];
    header('Location: order_confirmation.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            color: #4a4a4a;
            background-color: #fff8f0;
        }

        header {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 10px 20px;
            background-color: #f4e1d2;
            border-bottom: 2px solid #d4a373;
            text-align: center;
        }

        header h1 {
            margin: 0;
            color: #a56336;
        }

        nav {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            background-color: #f4e1d2;
        }

        .nav-links {
            list-style: none;
            padding: 0;
            display: flex;
            justify-content: center;
            margin: 0;
        }

        .nav-links li {
            margin: 0 15px;
        }

        .nav-links li a {
            text-decoration: none;
            color: #4a4a4a;
            font-weight: bold;
            font-size: 18px;
            transition: color 0.3s ease;
        }

        .nav-links li a:hover {
            color: #d4a373;
        }

        .container {
            width: 80%;
            margin: 20px auto;
        }

        .order-form {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .order-form h2 {
            text-align: center;
            color: #a56336;
            margin-bottom: 20px;
            font-size: 24px;
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
        .form-group input[type="email"],
        .form-group input[type="tel"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-group input[type="checkbox"], .form-group input[type="radio"] {
            margin-right: 10px;
        }

        .form-group .flavors,
        .form-group .pounds {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .form-group .flavors label,
        .form-group .pounds label {
            display: flex;
            align-items: center;
            font-size: 16px;
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
        }

        .form-group button:hover {
            background-color: #d4a373;
        }

        footer {
            text-align: center;
            background-color: #f4e1d2;
            color: #4a4a4a;
            padding: 10px 0;
            border-top: 2px solid #d4a373;
            margin-top: 20px;
        }

        .cake-images {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 20px;
        }

        .cake-images img {
            width: 200px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* Popup styles */
        .popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            visibility: hidden;
            opacity: 0;
            transition: visibility 0s, opacity 0.3s ease-in-out;
        }

        .popup-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .popup-content p {
            font-size: 18px;
            color: #a56336;
        }

        .popup-content button {
            background-color: #a56336;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .popup-content button:hover {
            background-color: #d4a373;
        }

        .popup.show {
            visibility: visible;
            opacity: 1;
        }
    </style>
</head>
<body>
    <header>
        <h1><?php echo $title; ?></h1>
        <nav>
            <ul class="nav-links">
                <li><a href="index.php">หน้าหลัก</a></li>
                <li><a href="type.php">ประเภท</a></li>
                <li><a href="menu.php">เมนูแนะนำ</a></li>
                <li><a href="order_guide.php">วิธีการสั่งซื้อ</a></li>
                <li><a href="about.php">เกี่ยวกับเรา</a></li>
                <li><a href="cart.php">ตะกร้าสินค้า</a></li>
                <?php if (isset($_SESSION['username'])): ?>
                    <li><a href="index.php?logout=true">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="order-form">
            <h2>กรอกรายละเอียดข้อมูล</h2>
            <div class="cake-images">
                <img src="<?php echo $cake_image; ?>" alt="<?php echo $cake_name; ?>">
            </div>
            <form id="orderForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                <div class="form-group">
                    <label for="name">ชื่อ-นามสกุล:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">E-mail:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="phone">เบอร์ติดต่อ:</label>
                    <input type="tel" id="phone" name="phone" required pattern="[0-9]{10}" placeholder="1234567890">
                </div>
                <div class="form-group">
                    <label for="flavor">เลือกรสชาติ:</label>
                    <div class="flavors">
                        <label><input type="radio" name="flavor" value="ช็อกโกแลต" required> ช็อกโกแลต</label>
                        <label><input type="radio" name="flavor" value="สตอเบอรี่" required> สตอเบอรี่</label>
                        <label><input type="radio" name="flavor" value="วนิลา" required> วนิลา</label>
                        <label><input type="radio" name="flavor" value="มะพร้าว" required> มะพร้าว</label>
                        <label><input type="radio" name="flavor" value="ใบเตย" required> ใบเตย</label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="pound">เลือกขนาดปอนด์:</label>
                    <div class="pounds">
                        <label><input type="radio" name="pound" value="1 ปอนด์" required> 1 ปอนด์</label>
                        <label><input type="radio" name="pound" value="2 ปอนด์" required> 2 ปอนด์</label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="date">วันที่รับ:</label>
                    <input type="date" id="date" name="date" required>
                </div>
                <div class="form-group">
                    <label for="message">ข้อความบนเค้ก:</label>
                    <textarea id="message" name="message"></textarea>
                </div>
                <div class="form-group price">
                    ราคา: <span id="price">500 บาท</span>
                    <input type="hidden" name="price" value="500 บาท">
                </div>
                <div class="form-group">
                    <button type="submit">สั่งซื้อ</button>
                </div>
            </form>
        </div>
    </div>

    

    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            const dateInput = document.getElementById('date');
            const today = new Date().toISOString().split('T')[0];
            dateInput.setAttribute('min', today);
        });

        document.querySelectorAll('input[name="pound"]').forEach(function(el) {
            el.addEventListener('change', function() {
                const price = el.value === '1 ปอนด์' ? '500 บาท' : '900 บาท';
                document.getElementById('price').innerText = price;
                document.querySelector('input[name="price"]').value = price;
            });
        });

        const form = document.getElementById('orderForm');
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            // Process the order details, save to the database or send an email, etc.
            form.submit(); // Submit the form after processing
        });
    </script>
</body>
</html>
