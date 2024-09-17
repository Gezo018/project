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

$title = "เมนูของเรา - baanfrankbaker";
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    
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
            background-color: var(--background-color);
            color: var(--text-color);
            transition: background-color 0.3s ease;
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
            width: 90%;
            max-width: 1200px;
            margin: 2rem auto;
            padding: 20px;
        }

        h1, h2 {
            font-family: 'Itim', cursive;
            color: var(--secondary-color);
            text-align: center;
            margin-bottom: 2rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .product-section {
            margin: 4rem 0;
            background-color: var(--card-bg-color);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .product-section:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .products {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2.5rem;
        }

        .product {
            text-align: center;
            background-color: var(--card-bg-color);
            border-radius: 15px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        

        .product:hover::before {
            opacity: 0.1;
        }
       
        .product:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 30px rgba(0, 0, 0, 0.1);
        }

        .product img {
            max-width: 100%;
            height: auto;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease;
        }

        .product:hover img {
            transform: scale(1.05);
        }

        .product h3 {
            color: var(--secondary-color);
            margin-bottom: 0.8rem;
            font-weight: 500;
        }

        .product p {
            font-size: 0.95rem;
            margin-bottom: 1.2rem;
            color: #666;
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

        @media (max-width: 768px) {
            .products {
                grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            }
        }

        .theme-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: var(--secondary-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            font-size: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .theme-toggle:hover {
            transform: scale(1.1);
            background-color: var(--button-hover);
        }

        body.dark-mode {
            --background-color: #1A1A2E;
            --card-bg-color: #16213E;
            --text-color: #E0E0E0;
        }

        body.dark-mode .product-section,
        body.dark-mode .product {
            background-color: var(--card-bg-color);
        }

        body.dark-mode h1, 
        body.dark-mode h2, 
        body.dark-mode .product h3 {
            color: var(--accent-color);
        }

        body.dark-mode .product p {
            color: #B0B0B0;
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
                    <img src="ex2.jpg" alt="เค้กปอนด์">
                    <h3>ออกแบบเค้กของคุณเอง</h3>
                    <p>สร้างเค้กที่เหมาะกับงานเฉลิมฉลองของคุณ!</p>
                    <a href="pound.php" class="order-button">สั่งซื้อ</a>
                </div>
                <!-- เพิ่มเค้กปอนด์อื่น ๆ ที่นี่ -->
            </div>
        </section>

        <section id="brownies" class="product-section">
            <h2>เค้กกล่อง</h2>
            <div class="products">
                <div class="product">
                    <img src="cup1.jpg" alt="บราวนี่จิ๋ว">
                    <h3>บราวนี่จิ๋ว คัพเค้ก</h3>
                    <p>บราวนี่ที่หอมหวาน คัพเค้กหลากรส</p>
                    <a href="box.php" class="order-button">สั่งซื้อ</a>
                </div>
                <!-- เพิ่มบราวนี่จิ๋วอื่น ๆ ที่นี่ -->
            </div>
        </section>

        <section id="cupcakes" class="product-section">
            <h2>เค้กถาด</h2>
            <div class="products">
                <div class="product">
                    <img src="minicake.jpg" alt="มินิเค้ก">
                    <h3>มินิเค้กหลากรส เค้กโบราณ เค้กไข่</h3>
                    <p>หลากหลายรสชาติที่ตอบสนองความหวานในใจคุณ</p>
                    <a href="tray.php" class="order-button">สั่งซื้อ</a>
                </div>
                <!-- เพิ่มมินิเค้กอื่น ๆ ที่นี่ -->
            </div>    
        </section>

        <section id="half-pound" class="product-section">
            <h2>เค้กครึ่งปอนด์</h2>
            <div class="products">
                <div class="product">
                    <img src="noyy.jpg" alt="เค้กปอนด์">
                    <h3>เค้กขนาดครึ่งปอนด์</h3>
                    <p>หลากหลายรสชาติที่ตอบสนองความหวานในใจคุณ</p>
                    <a href="Each.php" class="order-button">สั่งซื้อ</a>
                </div>
                <!-- เพิ่มเค้กครึ่งปอนด์อื่น ๆ ที่นี่ -->
            </div>
        </section>

        <section id="others" class="product-section">
            <h2>อื่นๆ</h2>
            <div class="products">
                <div class="product">
                    <img src="tuyy.jpg" alt="เค้กถ้วย">
                    <h3>เค้กประเภทอื่นๆ</h3>
                    <p>หลากหลายรสชาติ ตกแต่งได้ตามใจชอบ</p>
                    <a href="tuy.php" class="order-button">สั่งซื้อ</a>
                </div>
                <!-- เพิ่มเค้กประเภทอื่น ๆ ที่นี่ -->
            </div>
        </section>
    </div>


    <script>
        function toggleTheme() {
            document.body.classList.toggle('dark-mode');
        }
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