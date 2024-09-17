<?php
session_start();
include 'db_connection.php';
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
    <title>เมนูของเรา - บ้านแฟรงค์เบเกอร์</title>
    <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">
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
            margin: 0;
            padding: 0;
            background-color: #F9DBBA;
            color: #4a4a4a;
        }

        header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 30px;
    background-color: #FFD4DB;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

        header h1 {
            margin: 0;
            color: #a56336;
            font-size: 2.5em;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 2em auto;
            padding: 20px;
        }

        .product-section {
            margin: 3em 0;
        }

        .product-section h2 {
            text-align: center;
            color: #a56336;
            font-size: 2em;
            margin-bottom: 1em;
        }

        .products {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2em;
        }

        .product {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .product:hover {
            transform: translateY(-5px);
        }

        .product img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .product-info {
            padding: 1.5em;
        }

        .product h3 {
            margin: 0 0 0.5em;
            color: #a56336;
        }

        .product p {
            margin: 0 0 1em;
            font-size: 0.9em;
        }

        .order-button {
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
        }

        .order-button:hover {
            background: linear-gradient(45deg, var(--secondary-color), var(--primary-color));
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 8px 20px rgba(255, 154, 139, 0.6);
        }

        footer {
            background: #333;
            color: #fff;
            text-align: center;
            padding: 1em 0;
            margin-top: 2em;
        }

        @media (max-width: 768px) {
            .products {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
        }
    </style>
</head>
<body>
    <div class="container">
            <h2>เค้กครึ่งปอนด์</h2>
        <section id="cakes" class="product-section">
        
            <div class="products">
                <?php
                $sql = "SELECT * FROM products WHERE type = 'Each'";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo '<div class="product">';
                        echo '<img src="' . $row["image"] . '" alt="' . $row["name"] . '">';
                        echo '<div class="product-info">';
                        echo '<h3>' . $row["name"] . '</h3>';
                        echo '<p>' . $row["description"] . '</p>';
                        echo '<a href="order.php?product_id=' . $row["id"] . '&product_name=' . urlencode($row["name"]) . '&product_price=' . urlencode($row["price"]) . '" class="order-button">สั่งซื้อ</a>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>ขออภัย ขณะนี้ไม่มีสินค้าในหมวดหมู่นี้</p>';
                }
                ?>
            </div>
        </section>
    </div>
</body>
</html>