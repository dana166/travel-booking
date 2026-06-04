<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    
    $conn = connect_db();

    // تشفير كلمة المرور قبل تخزينها
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // التحقق من أن اسم المستخدم أو البريد الإلكتروني غير موجود مسبقًا
    $check_sql = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "اسم المستخدم أو البريد الإلكتروني مستخدم بالفعل.";
    } else {
        // إدخال البيانات في قاعدة البيانات
        $sql = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $username, $hashed_password, $email);

        if ($stmt->execute() === TRUE) {
            header("Location: login.php"); // إعادة التوجيه إلى صفحة تسجيل الدخول بعد التسجيل الناجح
            exit();
        } else {
            $error = "حدث خطأ أثناء التسجيل: " . $stmt->error;
        }
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تسجيل</title>
    <style>
        /* تنسيق عام */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        input[type="text"]:focus,
        input[type="password"]:focus,
        input[type="email"]:focus {
            border-color: #5b9bd5;
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .alert {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8d7da;
            color: #721c24;
            border-radius: 4px;
        }

        .text-center {
            text-align: center;
        }

        .mb-4 {
            margin-bottom: 20px;
        }

        .mt-3 {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center mb-4">تسجيل حساب جديد</h2>

        <?php 
        if (isset($error)) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
        ?>

        <form method="post" action="register.php">
            <div class="form-group">
                <label for="username">اسم المستخدم:</label>
                <input type="text" id="username" name="username" required class="form-control">
            </div>
            <div class="form-group">
                <label for="password">كلمة المرور:</label>
                <input type="password" id="password" name="password" required class="form-control">
            </div>
            <div class="form-group">
                <label for="email">البريد الإلكتروني:</label>
                <input type="email" id="email" name="email" required class="form-control">
            </div>
            <button type="submit" class="btn btn-primary btn-block">تسجيل</button>
        </form>
        
        <p class="text-center mt-3">لديك حساب؟ <a href="login.php">تسجيل الدخول</a></p>
    </div>
</body>
</html>
