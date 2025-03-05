document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const dataTable = document.getElementById('dataTable');

    function resetRowNumbers() {
        const visibleRows = Array.from(dataTable.querySelectorAll('tbody tr')).filter(row => row.style.display !== 'none');
        visibleRows.forEach((row, index) => {
            row.cells[0].textContent = index + 1; // อัปเดตลำดับที่ใหม่
        });
    }

    if (searchInput && dataTable) {
        searchInput.addEventListener('input', function () {
            const filter = this.value.toLowerCase();
            const rows = dataTable.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const cell = row.cells[6]; // คอลัมน์ "ชื่อผู้ดูแล" (index 6)
                if (cell) {
                    const text = cell.textContent || cell.innerText;
                    row.style.display = text.toLowerCase().includes(filter) ? '' : 'none';
                }
            });

            resetRowNumbers(); // รีเซ็ตลำดับที่หลังจากกรองข้อมูล
        });
    }

    window.addRow = function (data) {
        const tbody = dataTable.querySelector('tbody');
        const newRow = tbody.insertRow();

        data.forEach((text, index) => {
            const cell = newRow.insertCell(index);
            cell.textContent = text;
        });

        resetRowNumbers(); // รีเซ็ตลำดับที่หลังเพิ่มข้อมูล
    };

    dataTable.addEventListener('click', function (event) {
        if (event.target.classList.contains('delete-btn')) {
            event.target.closest('tr').remove();
            resetRowNumbers(); // รีเซ็ตลำดับที่หลังลบข้อมูล
        }
    });

    resetRowNumbers(); // ให้รันตอนโหลดหน้าเว็บ
});
