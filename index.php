<?php
// Start the session
session_start();

// Include database connection
include 'db_connection.php';

// Include the navbar
include 'navbar.php';

// Define page variables
$title = "ยินดีต้อนรับสู่บ้านแฟรงค์เบเกอร์";
$description = "🎉สินค้ายอดนิยมของทางร้าน🎖️";
$logo_image = "logo.png";
$slides = [
    "pic/bdcake(1).jpg",
    "pic/brownie.jpg",
    "pic/minicake.jpg"
];

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
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
            margin: auto;
            padding: 20px;
        }

        .hero {
            text-align: center;
            padding: 50px 20px;
            background: #BBE7FE;
            margin-bottom: 20px;
            border-radius: 10px;
        }

        .hero h2 {
            color: #000000;;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .hero p {
            color: #4a4a4a;
            font-size: 16px;
        }

        .slideshow-container {
        position: relative;
        width: 100%;
        padding-top: 50%; /* 2:1 Aspect Ratio */
        margin: auto;
        overflow: hidden;
        border-radius: 10px;
    }

    .mySlides {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: none;
        transition: opacity 1s ease-in-out;
        opacity: 0;
    }

    .mySlides.active {
        display: block;
        opacity: 1;
    }

    .mySlides img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .prev, .next {
        cursor: pointer;
        position: absolute;
        top: 50%;
        width: auto;
        margin-top: -22px;
        padding: 16px;
        color: white;
        font-weight: bold;
        font-size: 18px;
        background-color: rgba(0, 0, 0, 0.5);
        border-radius: 3px;
        transition: 0.6s ease;
        user-select: none;
    }

    .next {
        right: 0;
    }

    .prev {
        left: 0;
    }

    .prev:hover, .next:hover {
        background-color: rgba(0, 0, 0, 0.8);
    }

    .dot-container {
        text-align: center;
        margin-top: 20px;
    }

    .dot {
        cursor: pointer;
        height: 15px;
        width: 15px;
        margin: 0 2px;
        background-color: #bbb;
        border-radius: 50%;
        display: inline-block;
        transition: background-color 0.6s ease;
    }

    .dot.active {
        background-color: #717171;
    }

        .features {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }

        .feature {
            text-align: center;
            width: 30%;
            transition: transform 0.3s ease;
            background: #fff8f0;
            border-radius: 10px;
            padding: 10px;
        }

        .feature:hover {
            transform: scale(1.05);
        }

        .feature img {
            width: 100%;
            border-radius: 10px;
        }

        .feature h3 {
            color: #a56336;
            font-size: 18px;
            margin: 10px 0;
        }

        .feature p {
            color: #4a4a4a;
            font-size: 14px;
        }

        .footer {
            background-color: #BBE7FE;
            padding: 20px;
            text-align: left;
            margin-top: 20px;
            border-top: 2px solid #BBE7FE;
        }

        .main-categories {
            margin-bottom: 20px;
        }

        .main-categories h3 {
            color: #a56336;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .main-categories ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .main-categories li {
            margin-bottom: 8px;
        }

        .main-categories li a {
            text-decoration: none;
            color: #4a4a4a;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .main-categories li a:hover {
            color: #a56336;
        }
        .footer .main-categories ul li a {
            padding: 5px 10px;
            display: inline-block;
            border-radius: 5px;
            background-color: var(--primary-color);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .footer .main-categories ul li a:hover {
            background-color: var(--accent-color);
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <section class="hero">
            
            <div class="slideshow-container">
                <div class="mySlides fade">
                    <img src="nn.png" style="width:100%">
                 </div>
            <div class="mySlides fade">
                <img src="cake1.png" style="width:100%">
            </div>
            <div class="mySlides fade">
                <img src="free.png" style="width:100%">
            </div>
            <div class="mySlides fade">
                <img src="show.png" style="width:100%">
            </div>
            <div class="mySlides fade">
                <img src="like.png" style="width:100%">
            </div>
            <a class="prev" onclick="moveSlide(-1)">&#10094;</a>
            <a class="next" onclick="moveSlide(1)">&#10095;</a>
            </div>

            <div class="dot-container">
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
            </div>

        </section>

        <section class="features">
            <div class="feature">
                <a href="<?php echo isset($_SESSION['username']) ? 'pound.php' : 'login.php'; ?>">
                    <img src="hbd.jpg" alt="เค้กวันเกิด">
                    <h3>เค้กปอนด์</h3>
                    <p>เริ่มต้นทีปอนด์ละ 250 บาท ทำตามแบบที่ลูกค้าต้องการ</p>
                </a>
            </div>
            <div class="feature">
                <a href="<?php echo isset($_SESSION['username']) ? 'box.php' : 'login.php'; ?>">
                    <img src="b2.jpg" alt="บราวนี่จิ๋ว">
                    <h3>แบบกล่อง</h3>
                    <p>สั่งขั้นต่ำ 100 ชิ้น ราคาชิ้นละ 3 บาท</p>
                </a>
            </div>
            <div class="feature">
                <a href="<?php echo isset($_SESSION['username']) ? 'Tray.php' : 'login.php'; ?>">
                    <img src="mini.jpg" alt="มินิเค้ก">
                    <h3>แบบถาด</h3>
                    <p>มี 25 ชิ้นต่อ 1 ถาด ราคา 250 บาท</p>
                </a>
            </div>
        </section>
    </div>

    <footer class="footer">
    <div class="container footer-content" style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div class="main-categories" style="flex: 1; padding-right: 20px;">
            <h3>MAIN CATEGORIES</h3>
            <ul>
                <li><a href="type.php">เมนูขนมของเรา</a></li>
                <li><a href="order_guide.php">วิธีการสั่งซื้อ</a></li>
                <li><a href="review.php">รีวิวขนม</a></li>
                <li><a href="about.php">ติดต่อเรา</a></li>
            </ul>
        </div>
        <div class="map-container" style="flex: 1; max-width: 500px; padding-left: 20px;">
            <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d485.7202978902494!2d100.94565!3d13.114234000000002!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3102b951c23ece51%3A0x345dfbd4e5efe9c5!2z4Lir4LiZ4Lih4Lia4LmJ4Liy4LiZ4LmB4Lif4Lij4LiH4LiE4LmM!5e0!3m2!1sen!2sth!4v1723607206253!5m2!1sen!2sth"
                width="100%" height="200" style="border:0; border-radius: 10px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</footer>

    <script>
        
    var slideIndex = 0;
    var slides = document.getElementsByClassName("mySlides");
    var dots = document.getElementsByClassName("dot");

    function showSlide(index) {
        if (index >= slides.length) {
            slideIndex = 0;
        } else if (index < 0) {
            slideIndex = slides.length - 1;
        } else {
            slideIndex = index;
        }

        for (var i = 0; i < slides.length; i++) {
            slides[i].classList.remove("active");
            dots[i].classList.remove("active");
        }

        slides[slideIndex].classList.add("active");
        dots[slideIndex].classList.add("active");
    }

    function moveSlide(step) {
        showSlide(slideIndex + step);
    }

    function setSlide(index) {
        showSlide(index);
    }

    // Convert dots HTMLCollection to an array to use forEach
    Array.from(dots).forEach(function(dot, index) {
        dot.addEventListener('click', function() {
            setSlide(index);
        });
    });

    // Initialize the first slide
    showSlide(slideIndex);

    // Auto slide every 3 seconds
    setInterval(function() {
        moveSlide(1);
    }, 3000);
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
?>
