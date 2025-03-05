<?php
include __DIR__ . '/db.php'; // แก้ไขให้มี / ระหว่าง __DIR__ และ 'db.php'

$data = [];

try {
    // ✅ วนลูปดึงข้อมูลจาก table_page2 - table_page10
    for ($i = 2; $i <= 10; $i++) {
        $tableName = "table_page" . $i;
        $sql = "SELECT * FROM $tableName";
        $result = $conn->query($sql);

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
    }

    // ตรวจสอบชื่อคอลัมน์ในตารางจริงและแก้ไขให้ถูกต้อง
    $columns = "column1, column2, column3"; // ระบุชื่อคอลัมน์ที่แท้จริง
    $placeholders = "?, ?, ?"; // ใช้ placeholder

    // ✅ วนลูปนำข้อมูลทั้งหมดไป INSERT ลง table_page1
    $sql_insert = "INSERT INTO table_page1 ($columns) VALUES ($placeholders)";
    $stmt_insert = $conn->prepare($sql_insert);

    if ($stmt_insert) {
        foreach ($data as $row) {
            $stmt_insert->bind_param("sss", $row['column1'], $row['column2'], $row['column3']);
            $stmt_insert->execute();
        }

        // ✅ แสดง JSON ของข้อมูลที่นำไปบันทึก
        echo json_encode(["status" => "success", "message" => "อัปเดต table_page1 สำเร็จ"]);
    } else {
        throw new Exception("ไม่สามารถเตรียมคำสั่ง SQL สำหรับการแทรกข้อมูลได้");
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "เกิดข้อผิดพลาด: " . $e->getMessage()]);
}
?>
