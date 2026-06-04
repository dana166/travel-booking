<?php
include 'db.php';

$success_message = ""; // متغير لتخزين رسالة النجاح

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    $conn = connect_db();

    // التحقق من وجود القيم المطلوبة
    if (!empty($name) && !empty($email) && !empty($message)) {
        // التحقق من صحة البريد الإلكتروني
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Invalid email format.";
        } else {
            // تأمين الرسالة من الأكواد الخبيثة
            $message = htmlspecialchars($message);

            // استخدام استعلام مُعد مسبقًا (Prepared Statement)
            $stmt = $conn->prepare("INSERT INTO contact_us (name, email, message) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $message);

            if ($stmt->execute()) {
                $success_message = "تم إرسال الرسالة بنجاح!";
            } else {
                echo "حدث خطأ أثناء إرسال الرسالة.";
            }

            $stmt->close();
        }
    } else {
        echo "جميع الحقول مطلوبة.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اتصل بنا</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* Reset and global styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f7f7f7;
            color: #333;
            line-height: 1.6;
            text-align: center;
            padding: 50px 0;
        }

        .container {
            width: 70%;
            margin: auto;
            background-color: #fff;
            padding: 30px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
            font-size: 36px;
        }

        form {
            margin: 20px 0;
        }

        form input, form textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-bottom: 15px;
            font-size: 16px;
        }

        form button {
            padding: 10px 20px;
            background-color: #77b300;
            border: none;
            color: #fff;
            font-size: 18px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        form button:hover {
            background-color: #559800;
        }

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #333;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .back-btn:hover {
            background-color: #555;
        }

        /* Success Message */
        .success-message {
            color: green;
            font-size: 20px;
            font-weight: bold;
            margin-top: 20px;
        }

        /* Hide the form if the message is sent */
        form {
            display: <?php echo $success_message ? 'none' : 'block'; ?>;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>اتصل بنا</h1>

    <!-- عرض رسالة النجاح إذا تم إرسال الرسالة بنجاح -->
    <?php if ($success_message): ?>
        <p class="success-message"><?php echo $success_message; ?></p>
    <?php endif; ?>

    <!-- عرض النموذج إذا لم يتم إرسال الرسالة بعد -->
    <?php if (!$success_message): ?>
        <form method="POST" action="">
            <input type="text" name="name" placeholder="اسمك" required><br>
            <input type="email" name="email" placeholder="بريدك الإلكتروني" required><br>
            <textarea name="message" placeholder="اكتب رسالتك هنا..." rows="5" required></textarea><br>
            <button type="submit">إرسال</button>
        </form>
    <?php endif; ?>

    <a href="index.php" class="back-btn">الرجوع إلى الصفحة الرئيسية</a>
</div>

</body>
</html>
