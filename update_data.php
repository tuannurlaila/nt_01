<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");

include __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // ตรวจสอบว่ามี ID
        if (!isset($_POST['editId']) || empty($_POST['editId'])) {
            throw new Exception("ไม่พบ ID ที่ต้องการแก้ไข");
        }
        
        $id = intval($_POST['editId']);
        
        // ข้อมูลที่ต้องการอัปเดต
        $agent_id = $_POST['agentId'] ?? '';
        $ca_number = $_POST['caNumber'] ?? '';
        $contract_number = $_POST['contractNumber'] ?? '';
        $registered_name = $_POST['registeredName'] ?? '';
        $application = $_POST['application'] ?? '';
        $manager_name = $_POST['managerName'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $email = $_POST['email'] ?? '';
        $service_center = $_POST['serviceCenter'] ?? '';
        $type = $_POST['type'] ?? '';
        $card_number = $_POST['cardNumber'] ?? '';
        $contract_start_date = $_POST['contractStartDate'] ?? '';
        $note = $_POST['note'] ?? '';
        
        // ค้นหาว่า ID นี้อยู่ในตารางใด
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
            }
        }
        
        if (!$foundTable) {
            throw new Exception("ไม่พบข้อมูล ID: $id ในฐานข้อมูล");
        }
        
        // อัปเดตข้อมูล
        $sql = "UPDATE $foundTable SET 
                agent_id = ?, 
                ca_number = ?, 
                contract_number = ?, 
                registered_name = ?, 
                application = ?, 
                manager_name = ?, 
                phone = ?, 
                email = ?, 
                service_center = ?, 
                type = ?, 
                card_number = ?, 
                contract_start_date = ?, 
                note = ? 
                WHERE id = ?";
                
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: " . $conn->error);
        }
        
        $stmt->bind_param("sssssssssssssi", 
            $agent_id, $ca_number, $contract_number, $registered_name, 
            $application, $manager_name, $phone, $email, 
            $service_center, $type, $card_number, $contract_start_date, $note, $id
        );
        
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "อัปเดตข้อมูลสำเร็จ!", "table" => $foundTable]);
        } else {
            throw new Exception("ไม่สามารถอัปเดตข้อมูลได้: " . $stmt->error);
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
    
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "ต้องใช้ POST method"]);
}
?>