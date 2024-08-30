<?php
session_start();
include 'navbar.php';
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>วิธีการสั่งซื้อ</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Itim&display=swap">
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
    </style>
</head>
<body>
    <div class="container">
        <section class="order-guide">
            <h2>วิธีการสั่งซื้อ</h2>
            <p>ท่านสามารถดูวิธีการสั่งซื้อสินค้าจากร้านเบเกอรี่ของเราได้ที่นี่</p>
            <p>เรามีขั้นตอนง่ายๆ ดังนี้:</p>
            <ol>
                <li>เลือกสินค้าจากเมนูที่ท่านต้องการ</li>
                <li>เพิ่มสินค้าลงในตะกร้าสินค้า</li>
                <li>ตรวจสอบรายการสินค้าที่เพิ่มลงในตะกร้า</li>
                <li>คลิกที่ปุ่ม "สั่งซื้อ" เพื่อดำเนินการต่อ</li>
                <li>กรอกรายละเอียดที่อยู่สำหรับการจัดส่ง</li>
                <li>เลือกวิธีการชำระเงินที่ท่านต้องการ</li>
                <li>กดยืนยันการสั่งซื้อเพื่อส่งคำสั่งซื้อไปยังทางร้าน</li>
            </ol>
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
