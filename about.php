<?php 
session_start();
include('navbar.php'); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <link rel="stylesheet" href="styles.css">
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

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .about-us, .team {
            margin-bottom: 40px;
        }

        .about-us h2, .team h2 {
            font-size: 32px;
            margin-bottom: 20px;
            color: #d35400;
            text-align: center;
        }

        .about-us p, .team p {
            font-size: 18px;
            line-height: 1.8;
            text-align: justify;
        }

        .team-member h3 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #e67e22;
            text-align: center;
        }

        .social-buttons {
            text-align: center;
            margin-top: 20px;
        }

        .social-buttons a {
            display: inline-block;
            margin: 0 10px;
            transition: transform 0.3s ease;
        }

        .social-buttons a:hover {
            transform: scale(1.1);
        }

        .social-buttons img {
            height: 50px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <section class="about-us">
            <h2>Our Story</h2>
            <p>ที่ "หนมบ้านแฟรงค์เบเกอรี่" เรามุ่งมั่นที่จะนำเสนอความอร่อยที่เต็มไปด้วยความรักและความใส่ใจในทุกๆ ชิ้นขนมของเรา ขนมทุกชิ้นถูกอบขึ้นจากวัตถุดิบที่สดใหม่และมีคุณภาพ เพื่อให้ลูกค้าได้สัมผัสกับรสชาติที่แท้จริงของเบเกอรี่ที่ทำด้วยใจ</p>
            <p>เราเป็นร้านเบเกอรี่เล็กๆ แต่มุ่งมั่นที่จะส่งมอบความอร่อยและความสุขถึงบ้านของคุณโดยตรง ไม่ว่าคุณจะสั่งขนมเพื่อตัวเองหรือเพื่อคนที่คุณรัก คุณจะได้รับขนมที่ทำด้วยความพิถีพิถันในทุกขั้นตอน เรามีขนมหลากหลายชนิด ตั้งแต่เค้ก คุกกี้ ไปจนถึงขนมปัง ที่สามารถเลือกอร่อยได้ตามความชอบ</p>
            <p>เพียงแค่สั่งขนมจาก "หนมบ้านแฟรงค์เบเกอรี่" แล้วให้เราได้เป็นส่วนหนึ่งในการสร้างความสุขเล็กๆ น้อยๆ ในทุกวันของคุณ ส่งตรงถึงหน้าประตูบ้านของคุณอย่างอบอุ่น</p>
        </section>

        <section class="team">
            <h2>ติดต่อเรา</h2>
            <div class="team-member">
                <h3>📍พิกัด หมู่บ้านกรีนวิว 3 ถนน 9 กิโลศรีราชา</h3>
                <p>🧁ติดต่อ ☎️0806137730 ไม่มีหน้าร้าน รบกวนสั่งล่วงหน้า 1 - 2 วัน 
                <br>✅ขนมทำสด - ใหม่ตามออเดอร์
                <br>✅ไม่มีสารกันบูด - กันรา ไม่มีไขมันทรานส์</p>
            </div>
        </section>

        <section class="social-buttons">
            <a href="https://www.facebook.com/busarin.mainkul" target="_blank">
                <img src="pic/facebook.jpg" alt="Facebook">
            </a>
            <a href="https://lin.ee/D6PGpS5" target="_blank">
                <img src="https://scdn.line-apps.com/n/line_add_friends/btn/th.png" alt="เพิ่มเพื่อน">
            </a>
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
