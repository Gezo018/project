<?php
session_start();
include 'db_connection.php';
include 'navbar.php';

// การเชื่อมต่อกับฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bakery_shop";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ฟังก์ชันการส่งรีวิว
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $customer_name = $_POST['customer_name'];
    $customer_email = $_POST['customer_email'];
    $rating = $_POST['rating'];
    $review_text = $_POST['review_text'];
    $image_path = '';

    // จัดการการอัปโหลดรูปภาพ
    if(isset($_FILES['review_image']) && $_FILES['review_image']['error'] == 0) {
        $allowed = array('jpg', 'jpeg', 'png', 'gif');
        $filename = $_FILES['review_image']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if(in_array($ext, $allowed)) {
            $upload_dir = 'uploads/';
            $new_filename = uniqid() . '.' . $ext;
            $image_path = $upload_dir . $new_filename;

            if(move_uploaded_file($_FILES['review_image']['tmp_name'], $image_path)) {
                // อัปโหลดสำเร็จ
            } else {
                echo "<script>alert('เกิดข้อผิดพลาดในการอัปโหลดรูปภาพ');</script>";
                $image_path = '';
            }
        } else {
            echo "<script>alert('ไฟล์รูปภาพไม่ถูกต้อง กรุณาใช้ไฟล์ .jpg, .jpeg, .png หรือ .gif');</script>";
        }
    }

    $stmt = $conn->prepare("INSERT INTO reviews (customer_name, customer_email, rating, review_text, image_path) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiss", $customer_name, $customer_email, $rating, $review_text, $image_path);

    if ($stmt->execute()) {
        echo "<script>alert('ขอบคุณสำหรับรีวิวของคุณ!');</script>";
    } else {
        echo "<script>alert('การส่งรีวิวล้มเหลว กรุณาลองใหม่อีกครั้ง');</script>";
    }

    $stmt->close();
}

// ฟังก์ชันลบรีวิว
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_review']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $review_id = $_POST['review_id'];
    $stmt = $conn->prepare("DELETE FROM reviews WHERE id = ?");
    $stmt->bind_param("i", $review_id);

    if ($stmt->execute()) {
        echo "<script>alert('รีวิวถูกลบเรียบร้อยแล้ว');</script>";
    } else {
        echo "<script>alert('การลบรีวิวล้มเหลว กรุณาลองใหม่อีกครั้ง');</script>";
    }
    $stmt->close();
}

// ดึงข้อมูลรีวิวจากฐานข้อมูล
$reviews = $conn->query("SELECT id, customer_name, rating, review_text, review_date, image_path FROM reviews ORDER BY review_date DESC");

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รีวิวขนม - baanfrankbaker</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
         :root {
            --primary-color: #d35400;
            --secondary-color: #f39c12;
            --background-color: #f7f7f7;
            --card-background: #ffffff;
            --text-color: #333333;
        }

        body {
            font-family: 'Itim', sans-serif;
            background-color: #F9DBBA;
            color: var(--text-color);
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        h1, h2 {
            text-align: center;
            color: var(--primary-color);
        }

        form {
            background-color: var(--card-background);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
            transition: transform 0.3s ease;
        }

        form:hover {
            transform: translateY(-5px);
        }

        input, textarea, select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 6px;
            border: 1px solid #ddd;
            font-family: 'Prompt', sans-serif;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
        }

        input[type="file"] {
            padding: 10px;
        }

        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: var(--secondary-color);
        }

        button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-family: 'Prompt', sans-serif;
            font-weight: 600;
            width: 100%;
        }

        button:hover {
            background-color: var(--secondary-color);
        }

        .review-card {
            background-color: var(--card-background);
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .review-card:hover {
            transform: translateY(-5px);
        }

        .review-card h3 {
            margin: 0;
            color: var(--primary-color);
        }

        .review-card p {
            margin: 10px 0;
        }

        .rating {
            color: var(--secondary-color);
            font-size: 1.2em;
        }

        .review-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
            font-size: 0.9em;
            color: #777;
        }

        .review-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-top: 10px;
        }

        .delete-button {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .delete-button:hover {
            background-color: #c0392b;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            h1 {
                font-size: 24px;
            }

            h2 {
                font-size: 20px;
            }

            form, .review-card {
                padding: 15px;
            }

            input, textarea, select, button {
                font-size: 16px;
            }

            .review-meta {
                flex-direction: column;
                align-items: flex-start;
            }

            .review-meta span {
                margin-bottom: 5px;
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 22px;
            }

            h2 {
                font-size: 18px;
            }

            form, .review-card {
                padding: 12px;
            }

            .rating {
                font-size: 1em;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>รีวิวร้านหนมบ้านแฟรงค์</h1>

    <form method="POST" action="" enctype="multipart/form-data">
        <h2>เขียนรีวิวของคุณ</h2>
        <label for="customer_name">ชื่อ:</label>
        <input type="text" name="customer_name" id="customer_name" required>

        <label for="customer_email">อีเมล:</label>
        <input type="email" name="customer_email" id="customer_email" required>

        <label for="rating">คะแนน:</label>
        <select name="rating" id="rating" required>
            <option value="5">5 ดาว</option>
            <option value="4">4 ดาว</option>
            <option value="3">3 ดาว</option>
            <option value="2">2 ดาว</option>
            <option value="1">1 ดาว</option>
        </select>

        <label for="review_text">รีวิว:</label>
        <textarea name="review_text" id="review_text" rows="5" required></textarea>

        <label for="review_image">แนบรูปภาพ (ไม่บังคับ):</label>
        <input type="file" name="review_image" id="review_image" accept="image/*">

        <button type="submit" name="submit_review">ส่งรีวิว</button>
    </form>

    <h2>รีวิวล่าสุด</h2>
    <?php while($review = $reviews->fetch_assoc()): ?>
        <div class="review-card">
            <h3><?php echo htmlspecialchars($review['customer_name']); ?></h3>
            <p class="rating">
                <?php 
                for($i = 1; $i <= 5; $i++) {
                    echo $i <= $review['rating'] ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                }
                ?>
            </p>
            <p><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></p>
            <?php if (!empty($review['image_path'])): ?>
                <img src="<?php echo htmlspecialchars($review['image_path']); ?>" alt="รูปภาพรีวิว" class="review-image">
            <?php endif; ?>
            <div class="review-meta">
                <span><i class="far fa-calendar-alt"></i> <?php echo date("d M Y", strtotime($review['review_date'])); ?></span>
                <span><i class="far fa-clock"></i> <?php echo date("H:i", strtotime($review['review_date'])); ?></span>
            </div>

            <!-- ปุ่มลบรีวิว (สำหรับ admin) -->
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <form method="POST" action="">
                    <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                    <button type="submit" name="delete_review" class="delete-button">ลบรีวิว</button>
                </form>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/js/all.min.js"></script>
</body>
</html>
