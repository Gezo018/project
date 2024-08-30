<?php
// Start the session
session_start();

// Include database connection
include 'db_connection.php';

// Include the navbar
include 'navbar.php';

// Define page variables
$title = "ยินดีต้อนรับสู่บ้านแฟรงค์เบเกอร์";
$description = "ของหวานที่อร่อยที่สุดสำหรับคุณ";
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

        
        header .logo {
    max-width: 150px; /* Adjust this value to make the logo larger */
    height: auto; /* Maintain aspect ratio */
}

        .hero {
            text-align: center;
            padding: 50px 20px;
            background: #f4e1d2;
            margin-bottom: 20px;
            border-radius: 10px;
        }

        .hero h2 {
            color: #ffffff;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .hero p {
            color: #4a4a4a;
            font-size: 16px;
        }

        .slideshow-container {
            position: relative;
            width: 60%;
            margin: auto;
            overflow: hidden; /* Hide overflow to prevent content overflow */
            border-radius: 10px; /* Add rounded corners */
        }

        .mySlides {
            display: none;
            width: 100%;
            transition: opacity 1s ease-in-out; /* Smooth transition effect */
            opacity: 0; /* Start hidden */
        }

        .mySlides.active {
            display: block;
            opacity: 1; /* Fade in the active slide */
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
            transition: 0.6s ease;
            border-radius: 3px;
            user-select: none;
            background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
        }

        .next {
            right: 0;
        }

        .prev {
            left: 0;
        }

        .prev:hover, .next:hover {
            background-color: rgba(0, 0, 0, 0.8); /* Darker on hover */
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
            background-color: #f4e1d2;
            padding: 20px;
            text-align: left;
            margin-top: 20px;
            border-top: 2px solid #d4a373;
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
    </style>
</head>
<body>
    <div class="container">
        <section class="hero">
            <h2><?php echo $description; ?></h2>
            <p>สำรวจเมนูที่น่าตื่นเต้นของเราและสั่งซื้อออนไลน์ได้ทันที</p>
            <div class="slideshow-container">
                <div class="mySlides fade">
                    <img src="choc.jpg" style="width:100%">
                 </div>
            <div class="mySlides fade">
                <img src="cupcake.jpg" style="width:100%">
            </div>
            <div class="mySlides fade">
                <img src="bo.jpg" style="width:100%">
            </div>
            <div class="mySlides fade">
                <img src="foy.jpg" style="width:100%">
            </div>
            <div class="mySlides fade">
                <img src="noy.jpg" style="width:100%">
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
                <a href="<?php echo isset($_SESSION['username']) ? 'order.php?product_id=24&product_name=เค้กช็อกโกแล็ต&product_price=250.00' : 'login.php'; ?>">
                    <img src="hbd.jpg" alt="เค้กวันเกิด">
                    <h3>เค้กปอนด์</h3>
                    <p>เริ่มต้นทีปอนด์ละ 250 บาท ทำตามแบบที่ลูกค้าต้องการ</p>
                </a>
            </div>
            <div class="feature">
                <a href="<?php echo isset($_SESSION['username']) ? 'order.php?product_id=27&product_name=บราวนี่จิ๋ว&product_price=300.00' : 'login.php'; ?>">
                    <img src="b2.jpg" alt="บราวนี่จิ๋ว">
                    <h3>บราวนี่จิ๋ว</h3>
                    <p>สั่งขั้นต่ำ 100 ชิ้น ราคาชิ้นละ 3 บาท</p>
                </a>
            </div>
            <div class="feature">
                <a href="<?php echo isset($_SESSION['username']) ? 'order.php?product_id=28&product_name=มินิเค้ก+รวมรส&product_price=250.00' : 'login.php'; ?>">
                    <img src="mini.jpg" alt="มินิเค้ก">
                    <h3>มินิเค้ก</h3>
                    <p>มี 25 ชิ้นต่อ 1 ถาด ราคา 250 บาท</p>
                </a>
            </div>
        </section>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="main-categories">
                <h3>MAIN CATEGORIES</h3>
                <ul>
                    <li><a href="type.php">เมนูขนมของเรา</a></li>
                    <li><a href="order_guide.php">วิธีการสั่งซื้อ</a></li>
                    <li><a href="review.php">รีวิวขนม</a></li>
                    <li><a href="about.php">ติดต่อเรา</a></li>
                </ul>
            </div>
        </div>
    </footer>

    <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d485.7202978902494!2d100.94565!3d13.114234000000002!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3102b951c23ece51%3A0x345dfbd4e5efe9c5!2z4Lir4LiZ4Lih4Lia4LmJ4Liy4LiZ4LmB4Lif4Lij4LiH4LiE4LmM!5e0!3m2!1sen!2sth!4v1723607206253!5m2!1sen!2sth" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
    </iframe>
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
