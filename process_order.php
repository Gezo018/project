    <?php
    session_start();
    include 'db_connection.php';
    require 'vendor/autoload.php'; // Include Composer autoloader

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo "<div class='container'><h2 class='error'>กรุณาเข้าสู่ระบบเพื่อดำเนินการต่อ</h2></div>";
        exit();
    }

    $user_id = $_SESSION['user_id'];

    // Sanitize and validate input data
    $customer_name = filter_var($_POST['name'] ?? '', FILTER_SANITIZE_STRING);
    $customer_address = filter_var($_POST['address'] ?? '', FILTER_SANITIZE_STRING);
    $customer_phone = filter_var($_POST['phone'] ?? '', FILTER_SANITIZE_STRING);
    $customer_email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $payment_method = filter_var($_POST['payment_method'] ?? '', FILTER_SANITIZE_STRING);
    $total_price = floatval($_POST['total_price'] ?? 0.00);
    $cake_details = filter_var($_POST['cake_details'] ?? '', FILTER_SANITIZE_STRING);
    $delivery_date = $_POST['delivery_date'] ?? ''; // รับวันที่จัดส่งที่ลูกค้าเลือก
    $payment_slip = null;
    $upload_file = null;
    $receipt_url = ''; // Add this if you want to handle receipt URLs

    // Handle payment slip upload if payment method is bank_transfer
    if ($payment_method === 'bank_transfer' && isset($_FILES['payment_slip']) && $_FILES['payment_slip']['error'] === UPLOAD_ERR_OK) {
        $payment_slip = file_get_contents($_FILES['payment_slip']['tmp_name']);
        $upload_file = $_FILES['payment_slip']['tmp_name']; // Path to the uploaded file
    } elseif ($payment_method === 'bank_transfer') {
        echo "<div class='container'><h2 class='error'>กรุณาอัปโหลดสลิปการโอนเงิน</h2></div>";
        exit();
    }

    // Ensure the cart is not empty
    $stmt = $pdo->prepare("SELECT id FROM cart WHERE customer_id = ? AND status = 'active'");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        echo "<div class='container'>
                <h2 class='error'>ตะกร้าสินค้าของคุณว่างเปล่า</h2>
                <a href='index.php' class='button home-button'>กลับไปหน้าหลัก</a>
            </div>";
        exit();
    }

    $cart_id = $result['id'];

    try {
        // Begin transaction
        $pdo->beginTransaction();

        // Insert order into orders table
        $stmt = $pdo->prepare("INSERT INTO orders (customer_id, customer_name, customer_address, customer_phone, note, payment_method, total_price, payment_slip, status, created_at, receipt_url)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW(), ?)");
        $stmt->execute([$user_id, $customer_name, $customer_address, $customer_phone, $cake_details, $payment_method, $total_price, $payment_slip, $receipt_url]);

        $order_id = $pdo->lastInsertId();

        // Move cart items to order_items table
        $stmt = $pdo->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, price, size, flavor, custom_message)
            SELECT ?, product_id, quantity, price, size, flavor, custom_message
            FROM cart_items
            WHERE cart_id = ?
        ");
        $stmt->execute([$order_id, $cart_id]);

        // Fetch cake details for email and LINE Notify
        $stmt = $pdo->prepare("
            SELECT ci.quantity, p.name AS cake_name, ci.size, ci.flavor, ci.custom_message 
            FROM cart_items ci 
            JOIN products p ON ci.product_id = p.id 
            WHERE ci.cart_id = ?
        ");
        $stmt->execute([$cart_id]);
        $cake_details_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Clear the cart
        $stmt = $pdo->prepare("DELETE FROM cart_items WHERE cart_id = ?");
        $stmt->execute([$cart_id]);

        $stmt = $pdo->prepare("UPDATE cart SET status = 'completed' WHERE id = ?");
        $stmt->execute([$cart_id]);

        // Commit transaction
        $pdo->commit();

        // Format cake details for email and LINE Notify
        $cake_details_text = "";
        foreach ($cake_details_data as $cake) {
            $cake_details_text .= "- เค้ก: {$cake['cake_name']} ขนาด: {$cake['size']} รสชาติ: {$cake['flavor']} จำนวน: {$cake['quantity']} " . (!empty($cake['custom_message']) ? "(ข้อความพิเศษ: {$cake['custom_message']})" : "") . "\n";
        }

        // Consolidate LINE Notify messages
        $order_date = date('Y-m-d H:i:s'); // Current date and time

        $line_message = "คำสั่งซื้อ #$order_id\nชื่อ: $customer_name\nโทรศัพท์: $customer_phone\nที่อยู่: $customer_address\nวันที่จัดส่ง: $delivery_date\nวันที่สั่งซื้อ: $order_date\nรายละเอียดเค้ก:\n$cake_details_text\nเพิ่มเติม: $cake_details\nยอดรวม: ฿" . number_format($total_price, 2) . "\nวิธีการชำระเงิน: $payment_method\nสลิปการชำระเงิน:";

        // เรียกฟังก์ชันเพื่อส่ง LINE Notify
        sendLineNotify($line_message, $upload_file);

        // Send email notification
        // Correct the function call by passing the missing $cake_details argument
    sendOrderEmail(
        $customer_email, 
        $order_id, 
        $customer_name, 
        $customer_phone, 
        $customer_address, 
        $cake_details_text, 
        $payment_method, 
        $total_price, 
        $upload_file, 
        $order_date, 
        $delivery_date,
        $cake_details // Add this argument
    );

    echo "<div class='container'>
    <h2 class='success'>รายละเอียดการสั่งซื้อและใบเสร็จการชำระเงินถูกส่งเรียบร้อยแล้ว</h2>
    <a href='index.php' class='button home-button'>กลับไปหน้าหลัก</a>
    <p>หากท่านต้องการกรอกฟอร์มสำรวจความพึงพอใจในการสั่งซื้อ กรุณากรอกข้างล่าง : </p>
  </div>
  <iframe src='https://docs.google.com/forms/d/e/1FAIpQLSfTbi7S03sNZ7sYNwLUyW5CPY6Nj7dtT2YuJxmwR1D11ym2uA/viewform?embedded=true' width='640' height='1656' frameborder='0' marginheight='0' marginwidth='0'>กำลังโหลด…</iframe>";

    } catch (Exception $e) {  // Corrected this line
        $pdo->rollBack();
        echo "<div class='container'><h2 class='error'>เกิดข้อผิดพลาด: {$e->getMessage()}</h2></div>";
    }

    // Function to send order email
    function sendOrderEmail(
        $customer_email, 
        $order_id, 
        $customer_name, 
        $customer_phone, 
        $customer_address, 
        $cake_details_text, 
        $payment_method, 
        $total_price, 
        $upload_file = '', 
        $order_date, 
        $delivery_date, 
        $cake_details // Add this parameter
    ) {
        $mail = new PHPMailer(true);
        
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'baanfrankbaker@gmail.com';
            $mail->Password = 'saik pihg veeb qwnj'; // Ensure the password is correct
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('baanfrankbaker@gmail.com', 'baanfrankbaker');
            $mail->addAddress($customer_email);
            $mail->addAddress('baanfrankbaker@gmail.com');

            if ($payment_method === 'bank_transfer' && $upload_file !== '') {
                $mail->addAttachment($upload_file, 'payment_slip.jpg'); 
            }

            $mail->isHTML(true);
            $mail->Subject = "New order #$order_id!";
            $mail->Body = "
                <h2>รายละเอียดการสั่งซื้อ</h2>
                <p><strong>เลขที่คำสั่งซื้อ:</strong> $order_id</p>
                <p><strong>ชื่อผู้สั่งซื้อ:</strong> $customer_name</p>
                <p><strong>โทรศัพท์:</strong> $customer_phone</p>
                <p><strong>ที่อยู่:</strong> $customer_address</p>
                <p><strong>วันที่สั่งซื้อ:</strong> $order_date</p>
                <p><strong>วันที่จัดส่ง:</strong> $delivery_date</p>
                <p><strong>รายละเอียดเค้ก:</strong><br>$cake_details_text</p>
                <p><strong>เพิ่มเติม:</strong> $cake_details</p> <!-- เพิ่มรายละเอียดเพิ่มเติมตรงนี้ -->
                <p><strong>ยอดรวม:</strong> ฿" . number_format($total_price, 2) . "</p>
                <p><strong>วิธีการชำระเงิน:</strong> $payment_method</p>";
            
            $mail->send();
        } catch (Exception $e) {
            echo "ไม่สามารถส่งอีเมล์ได้. Error: {$mail->ErrorInfo}";
        }
    }

    // Function to send LINE notification
    function sendLineNotify($message, $image_file_path = '') {
        $line_api = "https://notify-api.line.me/api/notify";
        $line_token = "0LSe9kyUCAUJxX8JigDnF7Mqdx2wMAW8nvejrXdD35Y";

        $data = ['message' => $message];
        $headers = [
            "Authorization: Bearer $line_token"
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $line_api);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Check if image file is provided
        if ($image_file_path && file_exists($image_file_path)) {
            $data['imageFile'] = new CURLFile($image_file_path);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
    }

    ?>

    <style>
    body {
        font-family: 'Itim', sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f5f5f5;
    }

    .container {
        max-width: 800px;
        margin: 50px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    h2.success {
        color: #28a745;
    }

    h2.error {
        color: #dc3545;
    }

    p {
        margin: 10px 0;
    }

    .button {
        display: inline-block;
        padding: 10px 20px;
        background-color: #007bff;
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        transition: background-color 0.3s;
        font-size: 1rem;
    }

    .button:hover {
        background-color: #0056b3;
    }

    .home-button {
        margin-top: 20px;
        font-size: 1.1rem;
    }
    iframe {
    display: block;
    margin: 20px auto; /* Center horizontally */
    border: none;
    width: 100%; /* Responsive width */
    max-width: 640px; /* Max width of the form */
    height: 1656px; /* Height of the form */
}
    @media (max-width: 768px) {
        .container {
            padding: 15px;
            margin: 20px auto;
        }

        .button {
            padding: 8px 16px;
            font-size: 0.9rem;
        }

        .home-button {
            margin-top: 15px;
            font-size: 1rem;
        }
    }
    </style>