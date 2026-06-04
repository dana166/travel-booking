<?php
function connect_db() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "travel_booking";
    
    // إنشاء اتصال
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // التأكد من الاتصال
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}
?>