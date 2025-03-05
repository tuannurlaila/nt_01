<?php
require 'db.php'; // เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล

// ดึงข้อมูลจากฐานข้อมูลด้วย MySQLi
$sql = "SELECT * FROM pages ORDER BY id ASC";
$result = $conn->query($sql);

// เก็บข้อมูลจากฐานข้อมูล
$pages = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pages[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>index</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="styles_2.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="bg-light py-3 shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <!-- โลโก้ด้านซ้าย -->
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSZHB22SVg5AnHDTONamAVn4D6V8zmwpiYMhA&s" alt="Logo" class="header-logo">
            
            <!-- แถบค้นหาด้านขวา -->
            <input type="text" id="searchInput" placeholder="ค้นหา..." class="form-control my-3">
        </div>
    </header>

    <!-- Cards List -->
    <div class="container my-4 button-container">
        <?php foreach ($pages as $page): ?>
            <div class="card shadow-sm">
                <button class="card-btn" onclick="window.location.href='<?php echo $page['link']; ?>'">
                    <div class="card-body d-flex align-items-center">
                        <img src="<?php echo $page['icon_path']; ?>" alt="Icon" class="me-3">
                        <div>
                            <h5 class="card-title"><?php echo htmlspecialchars($page['title']); ?></h5>
                            <p class="card-text text-muted"><?php echo htmlspecialchars($page['description']); ?></p>
                        </div>
                    </div>
                </button>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="addDataModal" tabindex="-1" aria-labelledby="addDataLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDataLabel">เพิ่มข้อมูล</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addDataForm">
                        <div class="mb-3">
                            <label for="title" class="form-label">ชื่อหัวข้อ</label>
                            <input type="text" class="form-control" id="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">คำอธิบาย</label>
                            <textarea class="form-control" id="description" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">บันทึก</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Action Button -->
    <button class="fab" data-bs-toggle="modal" data-bs-target="#addDataModal">+</button>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="index.js"></script>
    <script src="searchboard.js"></script>
</body>
</html>
