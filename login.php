<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bakery_shop";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success = false; // Initialize success flag

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            // Password is correct, set session variables
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role']; // Store the user role in session
            $success = true;
        } else {
            $error = "รหัสผ่านไม่ถูกต้อง.";
        }
    } else {
        $error = "ไม่พบผู้ใช้งาน.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script> <!-- SweetAlert Library -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4e1d2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 280px;
            text-align: center;
        }

        .login-container h2 {
            margin-bottom: 20px;
            color: #a56336;
        }

        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: calc(100% - 20px);
            padding: 8px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        .login-container button {
            width: calc(100% - 20px);
            padding: 10px;
            background-color: #a56336;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
            font-size: 16px;
        }

        .login-container button:hover {
            background-color: #d4a373;
        }

        .register-container {
            margin-top: 20px;
        }

        .register-container a {
            text-decoration: none;
            color: #a56336;
            font-weight: bold;
        }

        .error {
            color: #e76f51;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>เข้าสู่ระบบ</h2>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <input type="text" name="username" placeholder="ชื่อผู้ใช้งาน" required>
            <input type="password" name="password" placeholder="รหัสผ่าน" required>
            <button type="submit">เข้าสู่ระบบ</button>
        </form>
        <div class="register-container">
            <p>ยังไม่มีบัญชี? <a href="register.php">สมัครสมาชิกที่นี่</a></p>
        </div>
    </div>

    <?php if ($success): ?>
    <script>
        swal({
            title: "ยินดีต้อนรับ",
            text: "เข้าสู่ระบบสำเร็จ!",
            icon: "success",
            button: "ตกลง",
        }).then(function() {
            window.location.href = 'index.php'; // Redirect to homepage after alert
        });
    </script>
    <?php endif; ?>
</body>
</html>
