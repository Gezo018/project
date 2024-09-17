<?php
    $currentPage = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Itim&display=swap">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Itim', sans-serif;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            background-color: #fafafa;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            background-color: #FFD4DB;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .logo img {
            height: 120px;
            margin-right: 10px;
            transition: transform 0.3s ease;
        }

        .logo img:hover {
            transform: scale(1.05);
        }

        .nav-links {
            display: flex;
            list-style-type: none;
            margin: 0;
            padding: 0;
        }

        .nav-links li {
            margin: 0 20px;
        }

        .nav-links a {
            text-decoration: none;
            color: #2c3e50;
            font-size: 18px;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-links a:before {
            content: "";
            position: absolute;
            width: 100%;
            height: 2px;
            bottom: -5px;
            left: 0;
            background-color: #2c3e50;
            visibility: hidden;
            transform: scaleX(0);
            transition: all 0.3s ease-in-out;
        }

        .nav-links a:hover {
            color: #1abc9c;
        }

        .nav-links a:hover:before {
            visibility: visible;
            transform: scaleX(1);
        }

        .nav-links .active {
            color: #1abc9c;
        }

        .nav-links .active:before {
            visibility: visible;
            transform: scaleX(1);
        }

        .user-controls {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 18px;
        }

        .user-controls a {
            text-decoration: none;
            color: #2c3e50;
            transition: all 0.3s ease;
            padding: 8px 12px;
            border-radius: 20px;
        }

        .user-controls a:hover {
            color: #1abc9c;
            background-color: rgba(255, 255, 255, 0.2);
        }

        .hamburger {
            display: none;
            flex-direction: column;
            cursor: pointer;
            z-index: 1001;
        }

        .hamburger .line {
            width: 30px;
            height: 3px;
            background-color: #2c3e50;
            margin: 5px 0;
            transition: all 0.3s ease;
        }

        .hamburger:hover .line {
            background-color: #1abc9c;
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none;
                flex-direction: column;
                background-color: #B4F8C8;
                position: absolute;
                top: 80px;
                right: 0;
                width: 100%;
                padding: 20px 0;
                border-radius: 0 0 10px 10px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                z-index: 1000;
            }

            .nav-links.active {
                display: flex;
            }

            .nav-links li {
                margin: 10px 0;
                text-align: center;
            }

            .hamburger {
                display: flex;
            }
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
        }

        .overlay.active {
            display: block;
        }
    </style>
</head>
<body>
<header>
<div class="logo">
    <a href="index.php">
        <img src="new.png" alt="บ้านแฟรงค์เบเกอร์">
    </a>
</div>
    </div>
    <nav>
        <ul class="nav-links">
            
            <li><a href="index.php" class="<?= ($currentPage == 'index.php') ? 'active' : '' ?>">หน้าหลัก</a></li>
            <li><a href="menu.php" class="<?= ($currentPage == 'menu.php') ? 'active' : '' ?>">เมนู</a></li>
            <li><a href="type.php" class="<?= ($currentPage == 'type.php') ? 'active' : '' ?>">สินค้าทั้งหมด</a></li>
            <li><a href="order_guide.php" class="<?= ($currentPage == 'order_guide.php') ? 'active' : '' ?>">วิธีการสั่งซื้อ</a></li>
            <li><a href="about.php" class="<?= ($currentPage == 'about.php') ? 'active' : '' ?>">เกี่ยวกับเรา</a></li>
            <li><a href="review.php" class="<?= ($currentPage == 'review.php') ? 'active' : '' ?>">รีวิวขนม</a></li>
            <li><a href="cart.php" class="<?= ($currentPage == 'cart.php') ? 'active' : '' ?>">ตะกร้าสินค้า</a></li>
            <li><a href="order_history.php" class="<?= ($currentPage == 'order_history.php') ? 'active' : '' ?>">ประวัติการสั่งซื้อ</a></li>
        </ul>
    </nav>
    <div class="user-controls">
        <?php if (isset($_SESSION['username'])): ?>
            <a href="profile.php"><?php echo $_SESSION['username']; ?></a>
            <a href="logout.php">ออกจากระบบ</a>
        <?php else: ?>  
            <a href="login.php">เข้าสู่ระบบ</a>
        <?php endif; ?>
    </div>
    <div class="hamburger" onclick="toggleNav()">
        <div class="line"></div>
        <div class="line"></div>
        <div class="line"></div>
    </div>
</header>

<div class="overlay" onclick="closeNav()"></div>

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

    document.addEventListener('click', function(event) {
        const navLinks = document.querySelector('.nav-links');
        const overlay = document.querySelector('.overlay');
        const hamburger = document.querySelector('.hamburger');
        
        if (!navLinks.contains(event.target) && !hamburger.contains(event.target)) {
            navLinks.classList.remove('active');
            overlay.classList.remove('active');
        }
    });
</script>
</body>
</html>
