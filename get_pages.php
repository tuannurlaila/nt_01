<?php
header('Content-Type: application/json');

// เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูลที่ใช้ MySQLi
require 'db.php';

try {
    // ใช้ MySQLi ดึงข้อมูลจากฐานข้อมูล
    $sql = "SELECT id, title, description, link, icon_path FROM pages";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // ดึงข้อมูลทั้งหมดในรูปแบบ associative array
        $pages = [];
        while ($row = $result->fetch_assoc()) {
            $pages[] = $row;
        }

        // ส่ง JSON กลับไปที่ frontend
        echo json_encode($pages, JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(["status" => "error", "message" => "ไม่พบข้อมูล"]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Database query failed: " . $e->getMessage()]);
}

exit;
?>
