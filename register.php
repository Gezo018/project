<?php
// Start the session
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

// Initialize variables
$success = false;
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password
    
    // Check if the username, email, or phone already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ? OR phone = ?");
    $stmt->bind_param("sss", $username, $email, $phone);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $error = "ชื่อผู้ใช้, อีเมล, หรือหมายเลขโทรศัพท์นี้มีอยู่แล้ว!";
    } else {
        // Insert the new user into the database
        $stmt = $conn->prepare("INSERT INTO users (username, email, phone, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $phone, $password);
        
        if ($stmt->execute()) {
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $conn->insert_id; // Store user ID in session
            $success = true; // Set success flag to true
        } else {
            $error = "ไม่สามารถลงทะเบียนได้ กรุณาลองอีกครั้ง";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styles.css">
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
        
        .register-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px; /* Set maximum width */
            text-align: center;
        }
        
        .register-container h2 {
            margin-bottom: 20px;
            color: #a56336;
        }
        
        .register-container input[type="text"],
        .register-container input[type="email"],
        .register-container input[type="tel"],
        .register-container input[type="password"] {
            width: calc(100% - 22px); /* Adjust width for padding and border */
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box; /* Include padding and border in element's total width and height */
        }
        
        .register-container button {
            width: calc(100% - 22px); /* Adjust width for padding and border */
            padding: 10px;
            background-color: #a56336;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            box-sizing: border-box; /* Include padding and border in element's total width and height */
        }
        
        .register-container button:hover {
            background-color: #d4a373;
        }
        
        .error {
            color: #e76f51;
            margin-bottom: 20px;
        }
    </style>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script> <!-- Include SweetAlert -->
</head>
<body>
    <div class="register-container">
        <h2>ลงทะเบียน</h2>
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="register.php" method="POST">
            <input type="text" name="username" placeholder="ชื่อผู้ใช้" required>
            <input type="email" name="email" placeholder="อีเมล" required>
            <input type="tel" name="phone" placeholder="หมายเลขโทรศัพท์" required>
            <input type="password" name="password" placeholder="รหัสผ่าน" required>
            <button type="submit">ลงทะเบียน</button>
        </form>
    </div>

    <?php if ($success): ?>
    <script>
        swal({
            title: "ยินดีต้อนรับ",
            text: "ลงทะเบียนสำเร็จ!",
            icon: "success",
            button: "ตกลง",
        }).then(function() {
            window.location.href = 'index.php'; // Redirect to homepage after alert
        });
    </script>
    <?php endif; ?>
</body>
</html>
