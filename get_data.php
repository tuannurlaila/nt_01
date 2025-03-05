<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");

// เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล
require __DIR__ . '/db.php';

// เช็คการเชื่อมต่อฐานข้อมูล
if (!$conn) {
    echo json_encode(["status" => "error", "message" => "เชื่อมต่อฐานข้อมูลล้มเหลว"]);
    exit;
}

// รับค่าพารามิเตอร์ page จาก URL (ถ้ามี)
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : null;

// ตรวจสอบว่ามีการส่งค่า id หรือไม่
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

// ถ้ามี ID → ดึงข้อมูลจากแต่ละตาราง
if ($id) {
    // ลูปหาตารางตั้งแต่ table_page2 ถึง table_page10
    for ($i = 2; $i <= 10; $i++) {
        $tableName = "table_page$i";
        $sql = "SELECT * FROM `$tableName` WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            $stmt->close();

            if ($data) {
                echo json_encode(["status" => "success", "data" => $data], JSON_UNESCAPED_UNICODE);
                $conn->close();
                exit;
            }
        }
    }

    // ถ้าไม่พบข้อมูลในทุกตาราง
    echo json_encode(["status" => "error", "message" => "ไม่พบข้อมูล ID: $id"]);
    $conn->close();
    exit;
}

// ถ้าไม่มี ID → ดึงข้อมูลจากตารางตามหน้า (ตามค่าของ page)
if ($page) {
    $tableName = "table_page" . $page;
    $sql = "SELECT * FROM `$tableName`";
    $result = $conn->query($sql);

    if ($result) {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        if (!empty($data)) {
            echo json_encode(["status" => "success", "data" => $data], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(["status" => "error", "message" => "ไม่พบข้อมูลในตาราง $tableName"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "ไม่สามารถดึงข้อมูลจากตาราง $tableName"]);
    }

    $conn->close();
    exit;
}

// ถ้าไม่มีทั้ง page และ id → ให้แจ้งเตือนและ **ไม่ต้องดึงข้อมูลทั้งหมด**
if (!$page && !$id) {
    echo json_encode(["status" => "error", "message" => "กรุณาระบุ page หรือ id"]);
    $conn->close();
    exit;
}
?>
