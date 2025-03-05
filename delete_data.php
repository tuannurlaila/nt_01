<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");

include __DIR__ . '/db.php';

$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['id'])) {
    try {
        $id = intval($data['id']);
        $foundTable = null;

        for ($i = 2; $i <= 10; $i++) {
            $tableName = "table_page$i";
            $checkSql = "SELECT id FROM $tableName WHERE id = ?";
            $checkStmt = $conn->prepare($checkSql);

            if ($checkStmt) {
                $checkStmt->bind_param("i", $id);
                $checkStmt->execute();
                $checkResult = $checkStmt->get_result();

                if ($checkResult->num_rows > 0) {
                    $foundTable = $tableName;
                    $checkStmt->close();
                    break;
                }
                $checkStmt->close();
            } else {
                echo json_encode(["status" => "error", "message" => "เกิดข้อผิดพลาดในการเตรียม SQL: " . $conn->error]);
                exit;
            }
        }

        if (!$foundTable) {
            echo json_encode(["status" => "error", "message" => "ไม่พบข้อมูล ID: $id"]);
            exit;
        }

        $sql = "DELETE FROM $foundTable WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            echo json_encode(["status" => "error", "message" => "เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: " . $conn->error]);
            exit;
        }

        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "ลบ ID: $id จาก $foundTable สำเร็จ"]);
        } else {
            echo json_encode(["status" => "error", "message" => "เกิดข้อผิดพลาดในการลบ ID: $id - " . $stmt->error]);
        }

        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "ต้องใช้ POST method และระบุ ID"]);
}
?>
