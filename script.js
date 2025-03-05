const fetchData = async () => {
    try {
        const response = await fetch('get_data.php');

        if (!response.ok) {
            const errorText = await response.text();
            console.error(`HTTP error: ${response.status}, Response: ${errorText}`);
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const contentType = response.headers.get("Content-Type");
        console.log('Content-Type:', contentType);

        let data;
        if (contentType && contentType.includes("application/json")) {
            data = await response.json();
        } else {
            const text = await response.text();
            console.error('Expected JSON, but got:', text);
            throw new Error("Expected JSON, but received something else.");
        }

        console.log('ข้อมูลที่ได้รับ:', JSON.stringify(data, null, 2));

        if (data && typeof data === 'object') {
            if (data.status === 'success' && Array.isArray(data.data)) {
                updateTable(data.data);
            } else {
                console.warn("⚠️ Data structure may be incorrect:", data);
            }
        }             

    } catch (error) {
        console.error('Fetch error:', error);
        alert('ไม่สามารถโหลดข้อมูลได้: ' + error.message);
    }
};

const updateTable = (data) => {
    const tableBody = document.querySelector('tbody');
    tableBody.innerHTML = ''; 

    if (!data.length) {
        tableBody.innerHTML = '<tr><td colspan="15" style="text-align: center;">ไม่พบข้อมูล</td></tr>';
        return;
    }    

    data.forEach((row, index) => {
        console.log("ID ของข้อมูลในตาราง:", row.id);
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${index + 1}</td>
            <td>${row.agent_id || ''}</td>
            <td>${row.ca_number || ''}</td>
            <td>${row.contract_number || ''}</td>
            <td>${row.registered_name || ''}</td>
            <td>${row.application || ''}</td>
            <td>${row.manager_name || ''}</td>
            <td>${row.phone || ''}</td>
            <td>${row.email || ''}</td>
            <td>${row.service_center || ''}</td>
            <td>${row.type || ''}</td>
            <td>${row.card_number || ''}</td>
            <td>${row.contract_start_date || ''}</td>
            <td>${row.note || ''}</td>
            <td>
                <button onclick="editData(${row.id})">แก้ไข</button>
                <button onclick="deleteData(${row.id})">ลบ</button>
            </td>
        `;
        tableBody.appendChild(tr);
    });
};

function setupModal() {
    const modal = document.getElementById("myModal");
    console.log("Modal:", modal);
    const closeBtn = document.querySelector(".close");
    const form = document.getElementById("dataForm");

    if (closeBtn) {
        closeBtn.onclick = function() {
            modal.style.display = "none";
            form.reset();
        }
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
            form.reset();
        }
    }

    if (form) {
        form.addEventListener("submit", submitForm);
    }
}

const submitForm = async (event) => {
    event.preventDefault();

    const formData = new FormData(document.getElementById("dataForm"));
    formData.append("page", currentPage);

    
    const submitButton = document.getElementById("submitButton");
    submitButton.disabled = true;
    submitButton.innerText = "กำลังบันทึก...";

    try {
        const response = await fetch("save_data.php", {
            method: "POST",
            body: formData,
        });

        const result = await response.json();
        console.log("Server response:", result);
        if (!response.ok || result.status === "error") {
            throw new Error(result.message || "เกิดข้อผิดพลาดในการบันทึกข้อมูล");
        }

        alert("บันทึกข้อมูลสำเร็จ!");
        document.getElementById("dataForm").reset();
        document.getElementById("editId").value = "";

        submitButton.disabled = false;
        submitButton.innerText = "บันทึก";

        location.reload();
    } catch (error) {
        console.error("เกิดข้อผิดพลาด:", error);
        alert(`ไม่สามารถบันทึกข้อมูล: ${error.message}`);

        submitButton.disabled = false;
        submitButton.innerText = "บันทึก";
    }
};

document.getElementById("dataForm").removeEventListener("submit", submitForm);
document.getElementById("dataForm").addEventListener("submit", submitForm);

const editData = async (id) => {
    try {
        const response = await fetch(`get_data.php?id=${id}`);
        const result = await response.json();

        if (!response.ok || result.status === 'error') {
            throw new Error(result.message || "เกิดข้อผิดพลาดในการโหลดข้อมูล");
        }

        const data = result.data;

        console.log("ข้อมูลที่โหลดมาเพื่อแก้ไข:", data);

        const modal = document.getElementById("myModal");
        if (modal) {
            modal.style.display = "block";
        }

        if (data) {
            document.getElementById("agentId").value = data.agent_id || "";
            document.getElementById("caNumber").value = data.ca_number || "";
            document.getElementById("contractNumber").value = data.contract_number || "";
            document.getElementById("registeredName").value = data.registered_name || "";
            document.getElementById("application").value = data.application || "";
            document.getElementById("managerName").value = data.manager_name || "";
            document.getElementById("phone").value = data.phone || "";
            document.getElementById("email").value = data.email || "";
            document.getElementById("serviceCenter").value = data.service_center || "";
            document.getElementById("type").value = data.type || "";
            document.getElementById("cardNumber").value = data.card_number || "";
            document.getElementById("contractStartDate").value = data.contract_start_date || "";
            document.getElementById("note").value = data.note || "";

            let editIdInput = document.getElementById("editId");
            if (!editIdInput) {
                editIdInput = document.createElement("input");
                editIdInput.type = "hidden";
                editIdInput.id = "editId";
                editIdInput.name = "editId";
                document.getElementById("dataForm").appendChild(editIdInput);
            }
            editIdInput.value = id;
            console.log("editId ถูกตั้งค่าเป็น:", id);
        }
    } catch (error) {
        console.error("เกิดข้อผิดพลาด:", error);
        alert(`ไม่สามารถโหลดข้อมูล: ${error.message}`);
    }
};

const deleteData = async (id) => {
    if (!confirm(`คุณแน่ใจว่าต้องการลบข้อมูล ID: ${id} ?`)) return;

    console.log("กำลังลบข้อมูล ID:", id);

    try {
        const response = await fetch("delete_data.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: id }),
        });

        const text = await response.text();
        console.log("Response จากเซิร์ฟเวอร์:", text);

        const result = JSON.parse(text);
        alert(result.message);

        if (result.status === "success") {
            fetchData();
        }
    } catch (error) {
        console.error("เกิดข้อผิดพลาด:", error);
        alert("ไม่สามารถลบข้อมูลได้");
    }
};

document.addEventListener('DOMContentLoaded', () => {

    fetchData();

    setupModal();

});

function openModal() {
    const modal = document.getElementById("myModal");
    const form = document.getElementById("dataForm");
    if (modal && form) {
        modal.style.display = "block";
        form.reset();
        const editId = document.getElementById('editId');
        if (editId) editId.value = '';
    }
}

const closeModal = () => {
    const modal = document.getElementById("myModal");
    if (modal) {
        modal.style.display = "none";
    }

    const form = document.getElementById("dataForm");
    if (form) {
        form.reset();
    }

    const editIdInput = document.getElementById("editId");
    if (editIdInput) {
        editIdInput.value = "";
    }
};

