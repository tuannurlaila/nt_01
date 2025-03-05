<?php
$servername = "localhost";
$username = "root"; // ค่าเริ่มต้นของ XAMPP คือ root
$password = ""; // ค่าเริ่มต้นของ XAMPP ไม่มีรหัสผ่าน
$dbname = "nt_database"; // ชื่อฐานข้อมูลที่ถูกต้อง

$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}
?>
