async function handleFormSubmit(event) {
    event.preventDefault();
    
    const title = document.getElementById('title').value;
    const description = document.getElementById('description').value;
    
    const formData = new FormData();
    formData.append('title', title);
    formData.append('description', description);

    try {
        const response = await fetch('save_page.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.status === 'error') {
            alert(result.message);
            return;
        }

        alert('เพิ่มข้อมูลสำเร็จ');
        document.getElementById('addDataForm').reset();
        await loadDashboardCards();
        
        // เพิ่มโค้ดนี้เพื่อปิด Modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('addDataModal'));
        if (modal) {
            modal.hide();
        }

    } catch (error) {
        console.error('Error:', error);
        alert('เกิดข้อผิดพลาด: ' + error.message);
    }
}

// ฟังก์ชันโหลดข้อมูล cards
async function loadDashboardCards() {
    try {
        const response = await fetch('get_pages.php');
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const pages = await response.json();
        updateDashboardUI(pages);
    } catch (error) {
        console.error('Error loading cards:', error);
        alert('ไม่สามารถโหลดข้อมูลได้: ' + error.message);
    }
}

// ฟังก์ชันอัพเดต UI
function updateDashboardUI(pages) {
    const container = document.querySelector('.button-container');
    if (!container) return;

    container.innerHTML = ''; // เคลียร์ข้อมูลเก่า

    pages.forEach((page, index) => {
        const pageNumber = index + 1;
        const cardHTML = `
            <div class="card shadow-sm mb-3">
                <button class="card-btn" onclick="window.location.href='/NT_1/page${pageNumber}.php'">
                    <div class="card-body d-flex align-items-center">
                        <img src="${page.icon_path || 'default-icon.png'}" alt="Icon" class="me-3" style="width: 48px; height: 48px;">
                        <div>
                            <h5 class="card-title mb-1">${page.title}</h5>
                            <p class="card-text text-muted mb-0">${page.description}</p>
                        </div>
                    </div>
                </button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', cardHTML);
    });
}

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    const addDataForm = document.getElementById('addDataForm');
    if (addDataForm) {
        addDataForm.addEventListener('submit', handleFormSubmit);
    }
    
    // โหลดข้อมูลเริ่มต้น
    loadDashboardCards();
});