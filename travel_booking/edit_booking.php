<?php
include 'db.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$conn = connect_db();

// تحديث الحجز
if (isset($_POST['update_booking'])) {
    $booking_id = $_POST['booking_id'];
    $destination = $_POST['destination'];
    $travel_date = $_POST['travel_date'];
    $num_people = $_POST['num_people'];
    
    $update_sql = "UPDATE bookings SET destination_id = ?, travel_date = ?, num_people = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("isii", $destination, $travel_date, $num_people, $booking_id);
    if ($stmt->execute()) {
        $success_message = "تم تحديث الحجز بنجاح!";
    } else {
        $error_message = "حدث خطأ أثناء التحديث.";
    }
    $stmt->close();
}

if (isset($_GET['edit_booking_id'])) {
    $edit_booking_id = $_GET['edit_booking_id'];
    $edit_sql = "SELECT b.id, b.destination_id, b.travel_date, b.num_people 
                 FROM bookings b 
                 WHERE b.id = ?";
    $stmt = $conn->prepare($edit_sql);
    $stmt->bind_param("i", $edit_booking_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_booking = $result->fetch_assoc();
    $stmt->close();
} else {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تعديل الحجز</title>
    <link rel="stylesheet" href="dashboard-styles.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            border: 1px solid #ddd;
        }
        .form-container input, .form-container select, .form-container button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            font-size: 16px;
        }
        .btn-back {
            background-color: #007bff;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block;
        }
        .btn-back:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h3>تعديل الحجز</h3>
        
        <?php if (isset($success_message)): ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <form method="post">
            <input type="hidden" name="booking_id" value="<?php echo $edit_booking['id']; ?>">
            <label>الوجهة:</label>
            <select name="destination">
                <?php
                $destinations_sql = "SELECT id, name FROM destinations";
                $destinations_result = $conn->query($destinations_sql);
                while ($destination = $destinations_result->fetch_assoc()) {
                    $selected = ($destination['id'] == $edit_booking['destination_id']) ? 'selected' : '';
                    echo "<option value='{$destination['id']}' $selected>{$destination['name']}</option>";
                }
                ?>
            </select><br>
            <label>تاريخ السفر:</label>
            <input type="date" name="travel_date" value="<?php echo $edit_booking['travel_date']; ?>"><br>
            <label>عدد الأشخاص:</label>
            <input type="number" name="num_people" value="<?php echo $edit_booking['num_people']; ?>"><br>
            <button type="submit" name="update_booking">تحديث الحجز</button>
        </form>
        
        <a href="dashboard.php" class="btn-back">الرجوع إلى لوحة التحكم</a>
    </div>
</body>
</html>
