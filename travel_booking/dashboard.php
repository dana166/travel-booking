<?php
include 'db.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// تسجيل الخروج
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

$conn = connect_db();

// حذف الحجز
if (isset($_GET['delete_booking_id'])) {
    $booking_id = $_GET['delete_booking_id'];
    $delete_sql = "DELETE FROM bookings WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $booking_id);
    if ($stmt->execute()) {
        $success_message = "تم حذف الحجز بنجاح!";
    } else {
        $error_message = "حدث خطأ أثناء الحذف.";
    }
    $stmt->close();
}

// البحث عن الحجوزات
$search_term = isset($_GET['search']) ? $_GET['search'] : '';
$bookings_sql = "SELECT b.id, u.username, d.name AS destination, b.travel_date, b.num_people, b.created_at 
                 FROM bookings b 
                 JOIN users u ON b.user_id = u.id 
                 JOIN destinations d ON b.destination_id = d.id";

if (!empty($search_term)) {
    $bookings_sql .= " WHERE d.name LIKE ?";
    $stmt = $conn->prepare($bookings_sql);
    $like_term = "%" . $search_term . "%";
    $stmt->bind_param("s", $like_term);
    $stmt->execute();
    $bookings_result = $stmt->get_result();
} else {
    $bookings_result = $conn->query($bookings_sql);
}

// جلب بيانات الاتصال
$contact_sql = "SELECT * FROM contact_us";
$contact_result = $conn->query($contact_sql);
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم</title>
    <link rel="stylesheet" href="dashboard-styles.css">
    <style>
        /* تنسيق الأزرار */
        .btn-edit {
            background-color: #007BFF;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 3px;
        }
        .btn-edit:hover {
            background-color: #0056b3;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 3px;
        }
        .btn-delete:hover {
            background-color: #c82333;
        }

        .btn-search {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .btn-search:hover {
            background-color: #218838;
        }

        .search-container {
            margin: 20px 0;
            display: flex;
            justify-content: center;
        }

        .search-container input[type="text"] {
            padding: 10px;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }

        .search-container button {
            margin-left: 10px;
        }

        /* تنسيق الجداول */
        table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
            background-color: #fff;
        }

        table th,
        table td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }

        table th {
            background: #f4f4f4;
        }

        table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        table tbody tr:hover {
            background: #e9ecef;
        }

        /* تنسيق الرسائل */
        .message {
            padding: 10px;
            margin: 20px 0;
            border-radius: 5px;
        }

        .message.success {
            background: #28a745;
            color: #fff;
        }

        .message.error {
            background: #dc3545;
            color: #fff;
        }

        /* تنسيق بيانات الاتصال */
        h2.mt-5 {
            margin-top: 30px;
            color: #333;
        }

        footer {
            background: #333;
            color: #fff;
            text-align: center;
            padding: 20px 0;
            margin-top: 20px;
        }

        footer p {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>لوحة التحكم</h1>
            <a href="?logout=true" class="btn-logout">تسجيل الخروج</a>
        </div>

        <!-- رسائل النجاح أو الخطأ -->
        <?php if (isset($success_message)): ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- نموذج البحث -->
        <div class="search-container">
            <form method="get">
                <input type="text" name="search" placeholder="ابحث عن الوجهة..." value="<?php echo htmlspecialchars($search_term); ?>">
                <button type="submit" class="btn-search">بحث</button>
            </form>
        </div>

        <!-- بيانات الحجوزات -->
        <h2 class="mt-5">بيانات الحجوزات</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>رقم الحجز</th>
                    <th>اسم المستخدم</th>
                    <th>الوجهة السياحية</th>
                    <th>تاريخ السفر</th>
                    <th>عدد الأشخاص</th>
                    <th>تاريخ التسجيل</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($bookings_result->num_rows > 0) {
                    while($row = $bookings_result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['username']}</td>
                                <td>{$row['destination']}</td>
                                <td>{$row['travel_date']}</td>
                                <td>{$row['num_people']}</td>
                                <td>{$row['created_at']}</td>
                                <td>
                                    <a href='edit_booking.php?edit_booking_id={$row['id']}' class='btn-edit'>تعديل</a>
                                    <a href='?delete_booking_id={$row['id']}' class='btn-delete'>حذف</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>لا توجد حجوزات</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- بيانات الاتصال بنا -->
        <h2 class="mt-5">بيانات الاتصال بنا</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>رقم الرسالة</th>
                    <th>الاسم</th>
                    <th>البريد الإلكتروني</th>
                    <th>الرسالة</th>
                    <th>تاريخ الإرسال</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($contact_result->num_rows > 0) {
                    while($row = $contact_result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['name']}</td>
                                <td>{$row['email']}</td>
                                <td>{$row['message']}</td>
                                <td>{$row['submitted_at']}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>لا توجد رسائل</td></tr>";
                }
                ?>
            </tbody>
        </table>

    </div>
</body>
</html>
