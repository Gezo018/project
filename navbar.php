<!-- navbar.php -->
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
            padding: 10px 20px;
            background-color: #f4e1d2;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo img {
            height: 60px;
            margin-right: 10px;
        }

        .logo h1 {
            font-size: 24px;
            color: #000; /* สีดำ */
            margin: 0;
        }


        .nav-links li {
            margin: 0 15px;
        }

        .nav-links a {
            text-decoration: none;
            color: #4a4a4a;
            font-size: 18px;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #a56336;
        }

        .user-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 18px;
        }

        .user-controls a {
            text-decoration: none;
            color: #4a4a4a;
            transition: color 0.3s ease;
        }

        .user-controls a:hover {
            color: #a56336;
        }

        .user-controls img {
            border-radius: 50%;
            height: 30px;
            width: 30px;
            object-fit: cover;
        }

        .hamburger {
            display: none;
            flex-direction: column;
            cursor: pointer;
            z-index: 1001;
        }

        .hamburger .line {
            width: 25px;
            height: 3px;
            background-color: #d4a373;
            margin: 4px 0;
            transition: background-color 0.3s ease;
        }

        .hamburger:hover .line {
            background-color: #a56336;
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none;
                flex-direction: column;
                background-color: #f4e1d2;
                position: absolute;
                top: 60px;
                right: 0;
                width: 100%;
                padding: 10px 0;
                border-radius: 5px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
                z-index: 1000;
            }

            .nav-links.active {
                display: flex;
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
        }

        .overlay.active {
            display: block;
        }
    </style>
</head>
<body>
<header>
    <div class="logo">
        <img src="logo.png" alt="บ้านแฟรงค์เบเกอร์">
        <h1>หนมบ้านแฟรงค์</h1>
    </div>
    <nav>
        <ul class="nav-links">
            <li><a href="index.php">หน้าหลัก</a></li>
            <li><a href="type.php">เมนู</a></li>
            <li><a href="order_guide.php">วิธีการสั่งซื้อ</a></li>
            <li><a href="about.php">เกี่ยวกับเรา</a></li>
            <li><a href="review.php">รีวิวขนม</a></li>
            <li><a href="cart.php">ตะกร้าสินค้า</a></li>
            <li><a href="order_history.php">ประวัติการสั่งซื้อ</a></li>
        </ul>
    </nav>
    <div class="user-controls">
        <?php if (isset($_SESSION['username'])): ?>
            <a href=""><?php echo $_SESSION['username']; ?></a>
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
