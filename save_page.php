<?php
require 'db.php';

$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';

if (empty($title) || empty($description)) {
    echo json_encode(['status' => 'error', 'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน']);
    exit;
}

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'เชื่อมต่อฐานข้อมูลล้มเหลว: ' . $conn->connect_error]));
}

// ตรวจสอบจำนวนหน้าปัจจุบัน
$sql = "SELECT COUNT(*) FROM pages";
$result = $conn->query($sql);
$row = $result->fetch_row();
$pageCount = $row[0];

if ($pageCount >= 10) {
    echo json_encode(['status' => 'error', 'message' => 'ไม่สามารถเพิ่มข้อมูลได้เกิน 10 หน้า']);
    exit;
}

// เลือกไฟล์ที่รองรับอยู่แล้ว
$pageNumber = $pageCount + 1;
$link = "page$pageNumber.php";
$pageFile = "page$pageNumber.php";

// ตรวจสอบว่าไฟล์มีอยู่จริง
if (!file_exists($pageFile)) {
    echo json_encode(['status' => 'error', 'message' => 'ไม่พบไฟล์: ' . $pageFile]);
    exit;
}

// ✅ เพิ่มข้อมูลลงฐานข้อมูล
$stmt = $conn->prepare("INSERT INTO pages (title, description, link) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $title, $description, $link);
$stmt->execute();
$stmt->close();

// ปิดการเชื่อมต่อ
$conn->close();

echo json_encode(['status' => 'success', 'message' => 'เพิ่มข้อมูลสำเร็จ']);
?>
