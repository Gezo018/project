<?php
session_start();
include 'navbar.php';
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>วิธีการสั่งซื้อ - baanfrankbaker</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Itim&display=swap">
    <style>
        body {
            font-family: 'Itim', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            color: #4a4a4a;
            background-color: #F9DBBA;
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
        .order-guide h2 {
            font-size: 36px; /* ขนาดฟอนต์หัวข้อ */
            color: #a56336;
            margin-bottom: 20px;
        }
        .order-guide p {
            font-size: 25px; /* ขนาดฟอนต์เนื้อหา */
            margin-bottom: 15px;
        }
        .guide-content ol {
            font-size: 20px; /* ขนาดฟอนต์สำหรับขั้นตอน */
            margin-left: 20px;
            margin-bottom: 20px;
        }
        .guide-image img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <section class="order-guide">
            <h2>วิธีการสั่งซื้อ</h2>
            <p>ท่านสามารถดูวิธีการสั่งซื้อสินค้าจากร้านเบเกอรี่ของเราได้ที่นี่</p>
            <p>เรามีขั้นตอนง่ายๆ ดังนี้:</p>
            <div class="guide-content">
            <ol>
                <li>เลือกสินค้าจากเมนูที่ท่านต้องการ</li>
                <li>เพิ่มสินค้าลงในตะกร้าสินค้า</li>
                <li>ตรวจสอบรายการสินค้าที่เพิ่มลงในตะกร้า</li>
                <li>คลิกที่ปุ่ม "สั่งซื้อ" เพื่อดำเนินการต่อ</li>
                <li>กรอกรายละเอียดที่อยู่สำหรับการจัดส่ง</li>
                <li>เลือกวิธีการชำระเงินที่ท่านต้องการ</li>
                <li>กดยืนยันการสั่งซื้อเพื่อส่งคำสั่งซื้อไปยังทางร้าน</li>
            </ol>
            <div class="guide-image">
                    <img src="howto.png" alt="วิธีการสั่งซื้อ">
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
