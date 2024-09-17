<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header('Location: register.php');
    exit();
}

// Database connection
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "bakery_shop";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user data from database using user_id stored in session
$user_id = $_SESSION['user_id'];
$sql = "SELECT username, email, phone FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $phone);
$stmt->fetch();
$stmt->close();

// Handle form submission for updating profile
$update_success = false; // Variable to track if update is successful
$update_error = ''; // Variable to store error message

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_email = $_POST['email'];
    $new_phone = $_POST['phone'];
    $new_password = $_POST['password'];
    $hashed_password = !empty($new_password) ? password_hash($new_password, PASSWORD_DEFAULT) : null;

    // Update query
    if ($hashed_password) {
        $update_sql = "UPDATE users SET email = ?, phone = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sssi", $new_email, $new_phone, $hashed_password, $user_id);
    } else {
        $update_sql = "UPDATE users SET email = ?, phone = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssi", $new_email, $new_phone, $user_id);
    }

    if ($stmt->execute()) {
        $update_success = true; // Set flag to show success message
        // Fetch updated user data
        $sql = "SELECT username, email, phone FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($username, $email, $phone);
        $stmt->fetch();
    } else {
        $update_error = "เกิดข้อผิดพลาดในการอัปเดตโปรไฟล์: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โปรไฟล์ผู้ใช้</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;600&display=swap">
    <style>
        :root {
            --background-color: #f5e6d3;
            --card-background: #ffffff;
            --text-color: #4a4a4a;
            --primary-color: #d35400;
            --secondary-color: #e67e22;
            --success-color: #2ecc71;
            --error-color: #e74c3c;
            --shadow-color: rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: 'Prompt', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            width: 90%;
            max-width: 500px;
            padding: 2rem;
            background-color: var(--card-background);
            border-radius: 15px;
            box-shadow: 0 10px 20px var(--shadow-color);
        }

        h1 {
            text-align: center;
            color: var(--primary-color);
            font-size: 2rem;
            margin-bottom: 1.5rem;
        }

        .profile-info {
            background-color: #f9f3ed;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }

        .profile-info p {
            font-size: 1rem;
            margin: 0.5rem 0;
        }

        .profile-info p strong {
            color: var(--secondary-color);
        }

        .update-success {
            background-color: var(--success-color);
            color: white;
            text-align: center;
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
        }

        .update-error {
            background-color: var(--error-color);
            color: white;
            text-align: center;
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-size: 1rem;
            margin-bottom: 0.5rem;
            color: var(--secondary-color);
        }

        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus {
            outline: none;
            border-color: var(--secondary-color);
        }

        .btn-group {
    display: flex;
    justify-content: space-between;
    margin-top: 1rem;
}

.btn {
    flex: 1;
    padding: 0.75rem;
    border: none;
    border-radius: 5px;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.1s ease;
    text-align: center;
    text-decoration: none;
    display: inline-block; /* เพิ่มบรรทัดนี้ */
}

.btn-submit {
    background-color: var(--primary-color);
    color: white;
    margin-right: 0.5rem;
}

.btn-logout {
    background-color: #e74c3c;
    color: white;
}

.btn-back {
    background-color: #3498db;
    color: white;
    margin-top: 1rem;
    width: 100%; /* เพิ่มบรรทัดนี้ */
}

/* แก้ไข media query สำหรับหน้าจอขนาดเล็ก */
@media (max-width: 480px) {
    .btn-group {
        flex-direction: column;
    }

    .btn {
        margin: 0.5rem 0;
        width: 100%; /* เพิ่มบรรทัดนี้ */
    }

    .btn-submit {
        margin-right: 0; /* ลบ margin-right สำหรับหน้าจอขนาดเล็ก */
    }
}

        @media (max-width: 480px) {
            .container {
                width: 95%;
                padding: 1.5rem;
            }

            h1 {
                font-size: 1.75rem;
            }

            .btn-group {
                flex-direction: column;
            }

            .btn {
                margin: 0.5rem 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>โปรไฟล์ของคุณ</h1>
        
        <?php if ($update_success): ?>
            <div class="update-success">บันทึกข้อมูลสำเร็จ!</div>
        <?php endif; ?>

        <?php if ($update_error): ?>
            <div class="update-error"><?php echo $update_error; ?></div>
        <?php endif; ?>

        <div class="profile-info">
            <p><strong>ชื่อผู้ใช้:</strong> <?php echo htmlspecialchars($username); ?></p>
        </div>

        <form method="post" action="">
            <label for="email">อีเมล</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" required>

            <label for="phone">เบอร์โทรศัพท์</label>
            <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($phone); ?>" required>

            <label for="password">รหัสผ่านใหม่ (เว้นว่างไว้หากไม่ต้องการเปลี่ยน)</label>
            <input type="password" name="password" id="password" placeholder="รหัสผ่านใหม่">

            <div class="btn-group">
                <button type="submit" class="btn btn-submit">บันทึกข้อมูล</button>
                <a href="logout.php" class="btn btn-logout">ออกจากระบบ</a>
            </div>
        </form>

        <a href="index.php" class="btn btn-back">กลับสู่หน้าหลัก</a>
    </div>
</body>
</html>