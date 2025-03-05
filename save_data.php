<?php
header("Content-Type: application/json");
require 'db.php';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error]));
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die(json_encode(["status" => "error", "message" => "Method ไม่ถูกต้อง"]));
}

$page = isset($_POST['page']) && is_numeric($_POST['page']) ? intval($_POST['page']) : 2;
$table_name = "table_page" . $page;

$agentId = $_POST['agentId'];
$caNumber = $_POST['caNumber'];
$contractNumber = $_POST['contractNumber'];
$registeredName = $_POST['registeredName'];
$application = $_POST['application'];
$managerName = $_POST['managerName'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$serviceCenter = $_POST['serviceCenter'];
$type = $_POST['type'];
$cardNumber = $_POST['cardNumber'];
$contractStartDate = $_POST['contractStartDate'];
$note = $_POST['note'];

$editId = isset($_POST['editId']) && is_numeric($_POST['editId']) ? intval($_POST['editId']) : null;

if ($editId) {
    // ✅ ถ้ามี editId ให้ทำการ UPDATE
    $sql = "UPDATE $table_name SET 
                agent_id = ?, ca_number = ?, contract_number = ?, registered_name = ?, 
                application = ?, manager_name = ?, phone = ?, email = ?, 
                service_center = ?, type = ?, card_number = ?, contract_start_date = ?, 
                note = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssssssi", 
        $agentId, $caNumber, $contractNumber, $registeredName, 
        $application, $managerName, $phone, $email, 
        $serviceCenter, $type, $cardNumber, $contractStartDate, 
        $note, $editId);
} else {
    // ✅ ถ้าไม่มี editId ให้ทำการ INSERT
    $sql = "INSERT INTO $table_name 
            (agent_id, ca_number, contract_number, registered_name, application, manager_name, phone, email, service_center, type, card_number, contract_start_date, note) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssssss", 
        $agentId, $caNumber, $contractNumber, $registeredName, 
        $application, $managerName, $phone, $email, 
        $serviceCenter, $type, $cardNumber, $contractStartDate, 
        $note);
}

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "บันทึกข้อมูลสำเร็จ"]);
} else {
    echo json_encode(["status" => "error", "message" => "เกิดข้อผิดพลาดในการบันทึกข้อมูล"]);
}

$stmt->close();
$conn->close();
?>
