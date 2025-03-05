<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");

// บันทึกเวลาเริ่มต้นการทำงาน
error_log("เริ่มการทำงาน insert_data.php: " . date('Y-m-d H:i:s'));

// เชื่อมต่อฐานข้อมูล
include __DIR__ . '/db.php'; 

if ($conn->connect_error) {
    error_log("การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
    die(json_encode(["status" => "error", "message" => "การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // ตรวจสอบค่าที่ได้รับจาก POST
        error_log("ค่าที่ได้รับจาก POST: " . json_encode($_POST));

        // รับค่าหน้าปัจจุบัน
        $page = isset($_POST['page']) ? intval($_POST['page']) : 2;
        error_log("ค่าของ page: " . $page);

        if ($page < 2 || $page > 10) {
            throw new Exception("หมายเลขหน้าไม่ถูกต้อง: " . $page);
        }

        // กำหนดตารางที่ต้องบันทึก
        $table = "table_page" . $page;
        error_log("บันทึกข้อมูลลงในตาราง: " . $table);

        // ตรวจสอบค่าที่ต้องการบันทึก
        $required_fields = ['agentId', 'caNumber', 'contractNumber', 'registeredName', 
                            'application', 'managerName', 'phone', 'email', 
                            'serviceCenter', 'type', 'cardNumber', 'contractStartDate', 'note'];
        
        foreach ($required_fields as $field) {
            if (!isset($_POST[$field])) {
                $_POST[$field] = ''; 
            }
        }

        // รับค่าข้อมูลจากฟอร์ม
        $agent_id = $_POST['agentId'];
        $ca_number = $_POST['caNumber'];
        $contract_number = $_POST['contractNumber'];
        $registered_name = $_POST['registeredName'];
        $application = $_POST['application'];
        $manager_name = $_POST['managerName'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $service_center = $_POST['serviceCenter'];
        $type = $_POST['type'];
        $card_number = $_POST['cardNumber'];
        $contract_start_date = $_POST['contractStartDate'];
        $note = $_POST['note'];

        // สร้าง SQL
        $sql = "INSERT INTO $table (agent_id, ca_number, contract_number, registered_name, application, manager_name, phone, email, service_center, type, card_number, contract_start_date, note) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("เกิดข้อผิดพลาด SQL: " . $conn->error);
        }

        $stmt->bind_param("sssssssssssss", 
            $agent_id, $ca_number, $contract_number, $registered_name, 
            $application, $manager_name, $phone, $email, 
            $service_center, $type, $card_number, $contract_start_date, $note
        );

        // บันทึกข้อมูล
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "บันทึกข้อมูลสำเร็จใน $table"]);
        } else {
            throw new Exception("ไม่สามารถบันทึกข้อมูลได้: " . $stmt->error);
        }

        $stmt->close();

    } catch (Exception $e) {
        error_log("ข้อผิดพลาด: " . $e->getMessage());
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }

    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "ต้องใช้ POST method"]);
}
?>
