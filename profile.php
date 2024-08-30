<?php
session_start();
include 'db_connection.php';

// ตรวจสอบว่าผู้ใช้ได้เข้าสู่ระบบหรือยัง
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // ถ้ายังไม่เข้าสู่ระบบ ให้กลับไปที่หน้าเข้าสู่ระบบ
    exit();
}

// ดึงข้อมูลผู้ใช้จากฐานข้อมูล
$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT username, email, phone FROM users WHERE id = ?");
$stmt->bind_param("i", $users);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "ไม่พบข้อมูลผู้ใช้";
    exit();
}

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โปรไฟล์ของฉัน</title>
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
        .container {
            padding: 20px;
        }
        .profile-info {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .profile-info h2 {
            margin-bottom: 20px;
            color: #a56336;
        }
        .profile-info p {
            margin: 10px 0;
            font-size: 18px;
        }
        .profile-info button {
            padding: 10px 20px;
            background-color: #a56336;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 18px;
            transition: background-color 0.3s ease;
        }
        .profile-info button:hover {
            background-color: #d4a373;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <div class="profile-info">
            <h2>โปรไฟล์ของฉัน</h2>
            <p><strong>ชื่อผู้ใช้:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>อีเมล:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>หมายเลขโทรศัพท์:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
            <a href="edit_profile.php">
                <button>แก้ไขข้อมูล</button>
            </a>
        </div>
    </div>
</body>
</html>
