<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nt_database";

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ทะเบียนข้อมูลตัวแทน (รวม)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/NT_1/styles_1.css"> 
</head>
<body>
    <div class="header">
        <h1>ทะเบียนข้อมูลตัวแทน (รวม)</h1>
        <p>ส่วนขายและบริการลูกค้าปัตตานี</p>
    </div>

    <div class="back-button">
        <a href="index.php" class="btn btn-primary">กลับหน้าหลัก</a>
    </div>

    <div class="table-container">
        <?php
        // สร้างคำสั่ง SQL เพื่อรวมข้อมูลจากทุกตาราง
        $tables = ["table_page2", "table_page3", "table_page4", "table_page5", "table_page6", "table_page7", "table_page8", "table_page9", "table_page10"];
        
        $unionQuery = "";
        foreach ($tables as $index => $table) {
            // ตรวจสอบว่าตารางมีอยู่หรือไม่
            $checkTable = "SHOW TABLES LIKE '$table'";
            $tableExists = $conn->query($checkTable);
            
            if ($tableExists->num_rows > 0) {
                if ($unionQuery != "") {
                    $unionQuery .= " UNION ALL ";
                }
                $unionQuery .= "SELECT agent_id, ca_number, contract_number, registered_name, 
                               application, manager_name, phone, email, service_center, 
                               type, card_number, contract_start_date, note 
                               FROM $table";
            }
        }

        if ($unionQuery != "") {
            $sql = "SELECT (@row_number:=@row_number + 1) AS id, t.* 
                   FROM ($unionQuery) t, 
                   (SELECT @row_number:=0) r";
            
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                echo "<table>
                      <tr class='table-header'>
                        <th>ลำดับที่</th>
                        <th>เลขรหัสตัวแทนขาย</th>
                        <th>หมายาเลข CA</th>
                        <th>เลขรหัสเลขที่สัญญา</th>
                        <th>จดทะเบียนในนาม</th>
                        <th>ใบสมัคร</th>
                        <th>ชื่อผู้ดูแล</th>
                        <th>โทรศัพท์</th>
                        <th>Email</th>
                        <th>ศูนย์บริการหลัก</th>
                        <th>ประเภท</th>
                        <th>เลขที่บัตร/ทะเบียนเลขที่</th>
                        <th>เริ่มสัญญา</th>
                        <th>หมายเหตุ</th>
                      </tr>";

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['agent_id']}</td>
                            <td>{$row['ca_number']}</td>
                            <td>{$row['contract_number']}</td>
                            <td>{$row['registered_name']}</td>
                            <td>{$row['application']}</td>
                            <td>{$row['manager_name']}</td>
                            <td>{$row['phone']}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['service_center']}</td>
                            <td>{$row['type']}</td>
                            <td>{$row['card_number']}</td>
                            <td>{$row['contract_start_date']}</td>
                            <td>{$row['note']}</td>
                          </tr>";
                }
                echo "</table>";
            } else {
                echo "<p>ไม่พบข้อมูล</p>";
            }
        } else {
            echo "<p>ไม่พบตารางข้อมูล</p>";
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>