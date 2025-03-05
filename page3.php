<?php
// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nt_database";

// ใช้ MySQLi เชื่อมต่อฐานข้อมูล
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// กำหนดชื่อตารางตาม page ปัจจุบัน
$page = 3; // กำหนดค่าคงที่สำหรับ page3
$tableName = "table_page" . $page;

// สร้างคำสั่ง SQL - ใช้ตัวแปร $tableName แทนชื่อตารางที่ hard-code ไว้
$sql = "SELECT * FROM `$tableName` ORDER BY id";

// เตรียม statement
$stmt = $conn->prepare($sql);

// ตรวจสอบว่าเตรียม statement สำเร็จหรือไม่
if ($stmt === false) {
    die("Error preparing query: " . $conn->error);
}

// Execute statement
$stmt->execute();

// รับผลลัพธ์จากคำสั่งที่ execute
$result = $stmt->get_result();

// เช็คว่า query สำเร็จหรือไม่
if ($result === false) {
    die("Query failed: " . $stmt->error);
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบข้อมูลตาราง</title>
    <link rel="stylesheet" href="/NT_1/styles_3.css"> 
</head>
<body>
    <div class="container">
        <div class="header-background">
            <h2>ระบบข้อมูลตาราง - หน้า <?php echo $page; ?></h2>
        </div>
        <div class="table-container">
        <div class="search-box">
            <label for="searchInput">ค้นหา:</label>
            <input type="text" id="searchInput" placeholder="พิมพ์ชื่อผู้ดูแล...">
        </div>
            <table id="dataTable">
                <thead>
                    <tr>
                        <th>ลำดับที่</th>
                        <th>เลขรหัสตัวแทนขาย</th>
                        <th>หมายเลข CA</th>
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
                        <th>การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['agent_id']}</td>";
            echo "<td>{$row['ca_number']}</td>";
            echo "<td>{$row['contract_number']}</td>";
            echo "<td>{$row['registered_name']}</td>";
            echo "<td>{$row['application']}</td>";
            echo "<td>{$row['manager_name']}</td>";
            echo "<td>{$row['phone']}</td>";
            echo "<td>{$row['email']}</td>";
            echo "<td>{$row['service_center']}</td>";
            echo "<td>{$row['type']}</td>";
            echo "<td>{$row['card_number']}</td>";
            echo "<td>{$row['contract_start_date']}</td>";
            echo "<td>{$row['note']}</td>";
            echo "<td>
                    <button onclick='editData({$row['id']})' class='edit-btn'>แก้ไข</button>
                    <button onclick='deleteData({$row['id']})' class='delete-btn'>ลบ</button>
                  </td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='15'>ไม่พบข้อมูล</td></tr>";
    }    
    ?>
</tbody>
            </table>
        </div>
    </div>

    <!-- ปุ่มเพิ่มข้อมูล -->
    <button class="add-btn" id="add-btn" onclick="openModal()">+</button>

    <div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>เพิ่มข้อมูล</h2>
        <form id="dataForm">
            <input type="text" name="agentId" id="agentId" placeholder="เลขรหัสตัวแทนขาย" required>
            <input type="text" name="caNumber" id="caNumber" placeholder="หมายเลข CA" required>
            <input type="text" name="contractNumber" id="contractNumber" placeholder="เลขรหัสเลขที่สัญญา" required>
            <input type="text" name="registeredName" id="registeredName" placeholder="จดทะเบียนในนาม" required>
            <select name="application" id="application" required>
                <option value="Mobile">Mobile</option>
                <option value="BB">BB</option>
            </select>
            <input type="text" name="managerName" id="managerName" placeholder="ชื่อผู้ดูแล" required>
            <input type="text" name="phone" id="phone" placeholder="โทรศัพท์" required>
            <input type="email" name="email" id="email" placeholder="Email" required>
            <input type="text" name="serviceCenter" id="serviceCenter" placeholder="ศูนย์บริการหลัก" required>
            <select name="type" id="type" required>
                <option value="ร้านค้า">ร้านค้า</option>
                <option value="นิติบุคคล">นิติบุคคล</option>
                <option value="บุคคลธรรมดา">บุคคลธรรมดา</option>
            </select>
            <input type="text" name="cardNumber" id="cardNumber" placeholder="เลขที่บัตร/ทะเบียนเลขที่" required>
            <input type="date" name="contractStartDate" id="contractStartDate" required="">
            <input type="text" name="note" id="note" placeholder="หมายเหตุ">
            <input type="hidden" name="page" value="3">
            <input type="hidden" id="editId" name="editId">
            <button type="submit" id="saveData">บันทึกข้อมูล</button>
        </form>
    </div>
</div>

    <script>
        document.getElementById('add-btn').addEventListener('click', function() {
            document.getElementById('myModal').style.display = 'block';
        });

        document.querySelector('.add-btn').addEventListener('click', function() {
            document.getElementById('myModal').style.display = 'block';
        });
    </script>

<script>
// ย้าย script จาก script.js มาไว้ที่นี่
document.addEventListener("DOMContentLoaded", function() {
    const modal = document.getElementById("myModal");
    const closeBtn = document.querySelector(".close");
    const form = document.getElementById("dataForm");

    // เปิด Modal
    window.openModal = function() {
        modal.style.display = "block";
        form.reset();
    }

    // ปิด Modal ด้วยปุ่มกากบาท
    closeBtn.onclick = function() {
        modal.style.display = "none";
    }

    // ปิด Modal เมื่อคลิกนอกกรอบ
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // จัดการการส่งฟอร์ม
    form.addEventListener("submit", async function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);

        try {
            const response = await fetch("save_data.php", {
                method: "POST",
                body: formData
            });

            const data = await response.json();
            console.log("Server response:", data);

            if (data.status === "success") {
                alert("บันทึกข้อมูลสำเร็จ!");
                modal.style.display = "none";
                form.reset();
                location.reload(); // รีโหลดหน้าเพื่อแสดงข้อมูลใหม่
            } else {
                alert(data.message || "เกิดข้อผิดพลาดในการบันทึกข้อมูล");
            }
        } catch (error) {
            console.error("Error:", error);
            alert("เกิดข้อผิดพลาดในการบันทึกข้อมูล");
        }
    });
});
</script>

<script src="search.js"></script>
<script src="script.js"></script>
</body>
</html>
<?php 
// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>