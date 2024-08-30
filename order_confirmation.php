<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$title = "ตะกร้าสินค้า - บ้านแฟรงค์เบเกอร์";
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
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            color: #4a4a4a;
            background-color: #fff8f0;
        }

        header {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 10px 20px;
            background-color: #f4e1d2;
            border-bottom: 2px solid #d4a373;
            text-align: center;
        }

        header h1 {
            margin: 0;
            color: #a56336;
        }

        nav {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            background-color: #f4e1d2;
        }

        .nav-links {
            list-style: none;
            padding: 0;
            display: flex;
            justify-content: center;
            margin: 0;
        }

        .nav-links li {
            margin: 0 15px;
        }

        .nav-links li a {
            text-decoration: none;
            color: #4a4a4a;
            font-weight: bold;
            font-size: 18px;
            transition: color 0.3s ease;
        }

        .nav-links li a:hover {
            color: #d4a373;
        }

        .container {
            padding: 20px;
        }

        .cart-title {
            text-align: center;
            color: #a56336;
            margin-bottom: 20px;
        }

        .cart-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .cart-table th, .cart-table td {
            border: 1px solid #d4a373;
            padding: 10px;
            text-align: left;
        }

        .cart-table th {
            background-color: #f4e1d2;
        }

        .cart-table td img {
            width: 100px;
            height: auto;
            border-radius: 5px;
        }

        .cart-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }

        .cart-actions .update-button,
        .cart-actions .checkout-button {
            padding: 10px 20px;
            background-color: #a56336;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .cart-actions .update-button:hover,
        .cart-actions .checkout-button:hover {
            background-color: #d4a373;
        }

        .cart-total {
            text-align: right;
            font-size: 18px;
            color: #a56336;
        }

        .remove-button {
            background-color: #e76f51;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .remove-button:hover {
            background-color: #f4a261;
        }
    </style>
</head>
<body>
    <header>
        <h1><?php echo $title; ?></h1>
        <nav>
            <ul class="nav-links">
                <li><a href="index.php">หน้าหลัก</a></li>
                <li><a href="type.php">ประเภท</a></li>
                <li><a href="menu.php">เมนูแนะนำ</a></li>
                <li><a href="order_guide.php">วิธีการสั่งซื้อ</a></li>
                <li><a href="about.php">เกี่ยวกับเรา</a></li>
                <li><a href="cart.php">ตะกร้าสินค้า</a></li>
                <?php if (isset($_SESSION['username'])): ?>
                    <li><a href="index.php?logout=true">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h2 class="cart-title">ตะกร้าสินค้าของคุณ</h2>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>สินค้า</th>
                    <th>ราคา</th>
                    <th>จำนวน</th>
                    <th>รวม</th>
                    <th>ลบ</th>
                </tr>
            </thead>
            <tbody id="cart-items">
                <!-- สินค้าจะถูกเพิ่มที่นี่ผ่าน JavaScript -->
            </tbody>
        </table>
        <div class="cart-actions">
            <div class="cart-total" id="cart-total">ยอดรวม: ฿0</div>
            <div>
                <a href="#" class="checkout-button">สั่งซื้อ</a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            loadCart();
            updateTotal();
        });

        function loadCart() {
            const cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];
            const cartTable = document.getElementById('cart-items');
            cartTable.innerHTML = '';

            cartItems.forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td><img src="${item.image}" alt="${item.name}"> ${item.name}</td>
                    <td>฿${item.price}</td>
                    <td><input type="number" value="${item.quantity}" min="1" onchange="updateItemQuantity('${item.name}', this.value)"></td>
                    <td>฿${item.price * item.quantity}</td>
                    <td><button class="remove-button" onclick="removeItem('${item.name}')">ลบ</button></td>
                `;
                cartTable.appendChild(row);
            });

            loadLastOrder();
        }

        function updateItemQuantity(name, quantity) {
            let cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];
            cartItems = cartItems.map(item => {
                if (item.name === name) {
                    item.quantity = parseInt(quantity);
                }
                return item;
            });
            localStorage.setItem('cartItems', JSON.stringify(cartItems));
            updateTotal();
        }

        function removeItem(name) {
            let cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];
            cartItems = cartItems.filter(item => item.name !== name);
            localStorage.setItem('cartItems', JSON.stringify(cartItems));
            loadCart();
            updateTotal();
        }

        function updateTotal() {
            const cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];
            let total = 0;
            cartItems.forEach(item => {
                total += item.price * item.quantity;
            });
            document.getElementById('cart-total').innerText = 'ยอดรวม: ฿' + total;
        }

        function loadLastOrder() {
            const lastOrder = JSON.parse(localStorage.getItem('lastOrder'));
            if (lastOrder) {
                const cartTable = document.getElementById('cart-items');
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>เลขออเดอร์: ${lastOrder.orderNumber}<br>ชื่อผู้สั่ง: ${lastOrder.name}<br>รสชาติ: ${lastOrder.flavor}<br>ขนาด: ${lastOrder.pound}<br>ข้อความบนเค้ก: ${lastOrder.message}</td>
                    <td>${lastOrder.price}</td>
                    <td>1</td>
                    <td>${lastOrder.price}</td>
                    <td><button class="remove-button" onclick="removeOrder()">ลบ</button></td>
                `;
                cartTable.appendChild(row);
            }
        }

        function removeOrder() {
            localStorage.removeItem('lastOrder');
            loadCart();
            updateTotal();
        }
    </script>
</body>
</html>
