<?php
session_start();
include('navbar.php');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bakery_shop";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$title = "เมนูของเรา - บ้านแฟรงค์เบเกอร์";
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
            background-color: #f4e1d2;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }

        .product-section {
            margin: 2em 0;
        }

        .products {
            display: flex;
            flex-wrap: wrap;
        }

        .product {
            flex: 1 1 calc(33.333% - 1em);
            margin: 0.5em;
            text-align: center;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 1em;
            background-color: #fff;
            border-radius: 8px;
        }

        .product img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .order-button {
            display: inline-block;
            background-color: #a56336;
            color: white;
            text-align: center;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .order-button:hover {
            background-color: #d4a373;
        }

        footer {
            background: #333;
            color: #fff;
            text-align: center;
            padding: 1em 0;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
        
    </style>
</head>
<body>
    <div class="container">
        <section id="cakes" class="product-section">
            <h2>เค้กปอนด์</h2>
            <div class="products">
                <div class="product">
                    <img src="bdcake.jpg" alt="เค้กปอนด์">
                    <h3>ออกแบบเค้กของคุณเอง</h3>
                    <p>สร้างเค้กที่เหมาะกับงานเฉลิมฉลองของคุณ!</p>
                    <a href="order.php" class="order-button">สั่งซื้อ</a>
                </div>
                <!-- เพิ่มเค้กปอนด์อื่น ๆ ที่นี่ -->
            </div>
        </section>

        <section id="brownies" class="product-section">
            <h2>บราวนี่จิ๋ว</h2>
            <div class="products">
                <div class="product">
                    <img src="brownie.jpg" alt="บราวนี่จิ๋ว">
                    <h3>บราวนี่จิ๋วสุดฟิน</h3>
                    <p>บราวนี่ที่หอมหวาน ละลายในปาก</p>
                    <a href="order.php" class="order-button">สั่งซื้อ</a>
                </div>
                <!-- เพิ่มบราวนี่จิ๋วอื่น ๆ ที่นี่ -->
            </div>
        </section>

        <section id="cupcakes" class="product-section">
            <h2>มินิเค้ก</h2>
            <div class="products">
                <div class="product">
                    <img src="minicake.jpg" alt="มินิเค้ก">
                    <h3>มินิเค้กหลากรส</h3>
                    <p>หลากหลายรสชาติที่ตอบสนองความหวานในใจคุณ</p>
                    <a href="order.php" class="order-button">สั่งซื้อ</a>
                </div>
                <!-- เพิ่มมินิเค้กอื่น ๆ ที่นี่ -->
            </div>
        </section>
    </div>


    <script>
         function toggleNav() {
            const navLinks = document.querySelector('.nav-links');
            const overlay = document.querySelector('.overlay');
            navLinks.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        function closeNav() {
            const navLinks = document.querySelector('.nav-links');
            const overlay = document.querySelector('.overlay');
            navLinks.classList.remove('active');
            overlay.classList.remove('active');
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
