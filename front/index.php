<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดเก็บข้อมูลคลังสินค้า</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            text-align: center;
        }

        .header h1 {
            color: #667eea;
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 1.1em;
        }

        .main-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        @media (max-width: 768px) {
            .main-content {
                grid-template-columns: 1fr;
            }
        }

        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 25px;
        }

        .card h2 {
            color: #667eea;
            margin-bottom: 20px;
            font-size: 1.5em;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 4px;
            font-size: 1em;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            flex: 1;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            font-size: 1em;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(102, 126, 234, 0.4);
        }

        .btn-danger {
            background: #ff6b6b;
            color: white;
        }

        .btn-danger:hover {
            background: #ff5252;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 107, 107, 0.4);
        }

        .btn-success {
            background: #51cf66;
            color: white;
        }

        .btn-success:hover {
            background: #40c057;
        }

        .btn-warning {
            background: #fcc419;
            color: white;
        }

        .btn-warning:hover {
            background: #fab005;
        }

        .table-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 25px;
            grid-column: 1 / -1;
        }

        .table-container h2 {
            color: #667eea;
            margin-bottom: 20px;
            font-size: 1.5em;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table thead {
            background: #f5f5f5;
        }

        table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #ddd;
        }

        table td {
            padding: 15px;
            border-bottom: 1px solid #ddd;
            color: #555;
        }

        table tbody tr:hover {
            background: #f9f9f9;
        }

        .status {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 600;
        }

        .status.keep {
            background: #d3f9d8;
            color: #2f9e44;
        }

        .status.empty {
            background: #ffe0e0;
            color: #c92a2a;
        }

        .action-btn {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9em;
            font-weight: 600;
            margin-right: 5px;
            transition: all 0.3s;
        }

        .action-edit {
            background: #4c6ef5;
            color: white;
        }

        .action-edit:hover {
            background: #3952d6;
        }

        .action-delete {
            background: #ff6b6b;
            color: white;
        }

        .action-delete:hover {
            background: #ff5252;
        }

        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 15px;
            display: none;
        }

        .alert-success {
            background: #d3f9d8;
            color: #2f9e44;
            border-left: 4px solid #2f9e44;
        }

        .alert-error {
            background: #ffe0e0;
            color: #c92a2a;
            border-left: 4px solid #c92a2a;
        }

        .alert-info {
            background: #d0ebff;
            color: #1971c2;
            border-left: 4px solid #1971c2;
        }

        .show {
            display: block;
        }

        .loading {
            text-align: center;
            padding: 20px;
            color: #667eea;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-header h2 {
            color: #667eea;
            margin: 0;
        }

        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s;
        }

        .close:hover {
            color: #000;
        }

        .modal-body {
            margin-bottom: 20px;
        }

        .modal-footer {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>📦 ระบบจัดเก็บข้อมูลคลังสินค้า</h1>
            <p>Inventory Management System</p>
        </div>

        <!-- Alert Messages -->
        <div id="alertContainer"></div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Add/Edit Form -->
            <div class="card">
                <h2 id="formTitle">➕ เพิ่มสินค้า</h2>
                <form id="inventoryForm">
                    <div class="form-group">
                        <label for="productId">ID สินค้า *</label>
                        <input type="text" id="productId" name="productId" required>
                    </div>

                    <div class="form-group">
                        <label for="productName">ชื่อสินค้า *</label>
                        <input type="text" id="productName" name="productName" required>
                    </div>

                    <div class="form-group">
                        <label for="quantity">ปริมาณ *</label>
                        <input type="number" id="quantity" name="quantity" min="0" required>
                    </div>

                    <div class="form-group">
                        <label for="status">สถานะ *</label>
                        <select id="status" name="status" required>
                            <option value="">-- เลือกสถานะ --</option>
                            <option value="Keep">Keep (เก็บไว้)</option>
                            <option value="Empty">Empty (หมด)</option>
                        </select>
                    </div>

                    <hr style="margin: 20px 0; border: none; border-top: 1px solid #ddd;">
                    <div style="background: #f9f9f9; padding: 15px; border-radius: 4px; margin-bottom: 15px;">
                        <p style="font-weight: 600; color: #333; margin-bottom: 15px;">📍 ข้อมูลที่อยู่ (Location)</p>
                        
                        <div class="form-group">
                            <label for="warehouseCode">Warehouse (A-C)</label>
                            <select id="warehouseCode" name="warehouseCode">
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="rowLocation">Row (A-C)</label>
                            <select id="rowLocation" name="rowLocation">
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="columnLocation">Column (1-5)</label>
                            <select id="columnLocation" name="columnLocation">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="level">Level (0-3)</label>
                            <select id="level" name="level">
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="locationId">Location ID</label>
                            <input type="text" id="locationId" name="locationId" readonly style="background: #e8e8e8; cursor: not-allowed; font-weight: 600; color: #667eea;">
                        </div>
                    </div>

                    <div class="button-group">
                        <button type="submit" class="btn btn-primary" id="submitBtn">เพิ่มสินค้า</button>
                        <button type="reset" class="btn btn-danger" id="resetBtn">ล้างข้อมูล</button>
                    </div>
                </form>
            </div>

            <!-- Statistics -->
            <div class="card">
                <h2>📊 สถิติ</h2>
                <div class="form-group">
                    <label>จำนวนสินค้าทั้งหมด</label>
                    <input type="text" id="totalItems" readonly style="background: #f5f5f5; cursor: not-allowed;">
                </div>
                <div class="form-group">
                    <label>สินค้าที่เก็บไว้</label>
                    <input type="text" id="keepItems" readonly style="background: #f5f5f5; cursor: not-allowed;">
                </div>
                <div class="form-group">
                    <label>สินค้าที่หมด</label>
                    <input type="text" id="emptyItems" readonly style="background: #f5f5f5; cursor: not-allowed;">
                </div>
                <div class="button-group">
                    <button class="btn btn-primary" onclick="loadInventory()">🔄 รีโหลดข้อมูล</button>
                </div>
            </div>
        </div>

        <!-- Table Container -->
        <div class="table-container">
            <h2>📋 รายการสินค้า</h2>
            <div id="tableContent"></div>
        </div>
    </div>

    <!-- Modal for Delete Confirmation -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>ยืนยันการลบ</h2>
                <span class="close" onclick="closeDeleteModal()">&times;</span>
            </div>
            <div class="modal-body">
                <p id="deleteMessage">คุณต้องการลบสินค้านี้ใช่หรือไม่?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" onclick="confirmDelete()">ลบ</button>
                <button class="btn btn-primary" onclick="closeDeleteModal()">ยกเลิก</button>
            </div>
        </div>
    </div>

    <script>
        const API_URL = '/testapi/api/inventory.php';
        let editingId = null;
        let deleteId = null;

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadInventory();
            setupFormHandler();
        });

        // Setup form submission
        function setupFormHandler() {
            // Setup location ID auto-generation
            const locationFields = ['warehouseCode', 'rowLocation', 'columnLocation', 'level'];
            locationFields.forEach(field => {
                document.getElementById(field).addEventListener('change', generateLocationId);
            });

            // Setup form submission
            document.getElementById('inventoryForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const productId = document.getElementById('productId').value.trim();
                const productName = document.getElementById('productName').value.trim();
                const quantity = parseInt(document.getElementById('quantity').value);
                const status = document.getElementById('status').value;
                
                // Location fields
                const warehouseCode = document.getElementById('warehouseCode').value;
                const rowLocation = document.getElementById('rowLocation').value;
                const columnLocation = parseInt(document.getElementById('columnLocation').value);
                const level = parseInt(document.getElementById('level').value);

                if (!productId || !productName || !status) {
                    showAlert('กรุณากรอกข้อมูลให้ครบถ้วน', 'error');
                    return;
                }

                const formData = {
                    productId,
                    productName,
                    quantity,
                    status,
                    warehouseCode,
                    rowLocation,
                    columnLocation,
                    level
                };

                if (editingId) {
                    updateInventory(editingId, formData);
                } else {
                    createInventory(formData);
                }
            });
        }

        // Generate Location ID
        function generateLocationId() {
            const warehouse = document.getElementById('warehouseCode').value;
            const row = document.getElementById('rowLocation').value;
            const column = document.getElementById('columnLocation').value;
            const level = document.getElementById('level').value;
            
            const locationId = warehouse + row + '-' + column + '-' + level;
            document.getElementById('locationId').value = locationId;
        }

        // Load all inventory items
        function loadInventory() {
            const tableContent = document.getElementById('tableContent');
            tableContent.innerHTML = '<div class="loading"><div class="spinner"></div><p>กำลังโหลดข้อมูล...</p></div>';

            fetch(API_URL, {
                method: 'GET'
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    displayTable(data.data);
                    updateStatistics(data.data);
                } else {
                    tableContent.innerHTML = '<div class="empty-state">ไม่สามารถโหลดข้อมูลได้</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                tableContent.innerHTML = '<div class="empty-state">เกิดข้อผิดพลาด: ' + error.message + '</div>';
            });
        }

        // Display table
        function displayTable(items) {
            const tableContent = document.getElementById('tableContent');
            
            if (items.length === 0) {
                tableContent.innerHTML = '<div class="empty-state">ไม่มีข้อมูลสินค้า</div>';
                return;
            }

            let html = '<table><thead><tr><th>ID</th><th>ชื่อสินค้า</th><th>ปริมาณ</th><th>สถานะ</th><th>📍 Location ID</th><th>วันที่สร้าง</th><th>การกระทำ</th></tr></thead><tbody>';

            items.forEach(item => {
                const statusClass = item.status === 'Keep' ? 'keep' : 'empty';
                const statusText = item.status === 'Keep' ? 'Keep (เก็บไว้)' : 'Empty (หมด)';
                const createdDate = new Date(item.created_at).toLocaleString('th-TH');
                const locationId = item.location_id || 'AA-1-0';

                html += `<tr>
                    <td>${item.product_id}</td>
                    <td>${item.product_name}</td>
                    <td>${item.quantity}</td>
                    <td><span class="status ${statusClass}">${statusText}</span></td>
                    <td><strong style="color: #667eea;">${locationId}</strong></td>
                    <td>${createdDate}</td>
                    <td>
                        <button class="action-btn action-edit" onclick="editItem(${item.id})">แก้ไข</button>
                        <button class="action-btn action-delete" onclick="openDeleteModal(${item.id}, '${item.product_name}')">ลบ</button>
                    </td>
                </tr>`;
            });

            html += '</tbody></table>';
            tableContent.innerHTML = html;
        }

        // Update statistics
        function updateStatistics(items) {
            const total = items.length;
            const keep = items.filter(item => item.status === 'Keep').length;
            const empty = items.filter(item => item.status === 'Empty').length;

            document.getElementById('totalItems').value = total + ' รายการ';
            document.getElementById('keepItems').value = keep + ' รายการ';
            document.getElementById('emptyItems').value = empty + ' รายการ';
        }

        // Create new item
        function createInventory(data) {
            fetch(API_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    showAlert('เพิ่มสินค้าสำเร็จ ✓', 'success');
                    document.getElementById('inventoryForm').reset();
                    loadInventory();
                } else {
                    showAlert('เกิดข้อผิดพลาด: ' + result.message, 'error');
                }
            })
            .catch(error => {
                showAlert('เกิดข้อผิดพลาด: ' + error.message, 'error');
            });
        }

        // Edit item
        function editItem(id) {
            fetch(API_URL + '?id=' + id, {
                method: 'GET'
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const item = data.data;
                    document.getElementById('productId').value = item.product_id;
                    document.getElementById('productId').readOnly = false;
                    document.getElementById('productName').value = item.product_name;
                    document.getElementById('quantity').value = item.quantity;
                    document.getElementById('status').value = item.status;
                    
                    // Location fields
                    document.getElementById('warehouseCode').value = item.warehouse || 'A';
                    document.getElementById('rowLocation').value = item.row_location || 'A';
                    document.getElementById('columnLocation').value = item.column_location || '1';
                    document.getElementById('level').value = item.level || '0';
                    generateLocationId(); // Update location ID display
                    
                    document.getElementById('formTitle').textContent = '✏️ แก้ไขสินค้า';
                    document.getElementById('submitBtn').textContent = 'อัปเดตสินค้า';
                    document.getElementById('resetBtn').textContent = 'ยกเลิก';
                    
                    editingId = id;
                    
                    // Scroll to form
                    document.querySelector('.card').scrollIntoView({ behavior: 'smooth' });
                } else {
                    showAlert('ไม่สามารถดึงข้อมูลได้', 'error');
                }
            })
            .catch(error => {
                showAlert('เกิดข้อผิดพลาด: ' + error.message, 'error');
            });
        }

        // Update item
        function updateInventory(id, data) {
            fetch(API_URL, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id, ...data })
            })
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    showAlert('อัปเดตสินค้าสำเร็จ ✓', 'success');
                    resetForm();
                    loadInventory();
                } else {
                    showAlert('เกิดข้อผิดพลาด: ' + result.message, 'error');
                }
            })
            .catch(error => {
                showAlert('เกิดข้อผิดพลาด: ' + error.message, 'error');
            });
        }

        // Delete modal functions
        function openDeleteModal(id, productName) {
            deleteId = id;
            document.getElementById('deleteMessage').textContent = `คุณต้องการลบสินค้า "${productName}" ใช่หรือไม่?`;
            document.getElementById('deleteModal').style.display = 'block';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
            deleteId = null;
        }

        function confirmDelete() {
            if (!deleteId) return;

            fetch(API_URL, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: deleteId })
            })
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    showAlert('ลบสินค้าสำเร็จ ✓', 'success');
                    closeDeleteModal();
                    loadInventory();
                } else {
                    showAlert('เกิดข้อผิดพลาด: ' + result.message, 'error');
                }
            })
            .catch(error => {
                showAlert('เกิดข้อผิดพลาด: ' + error.message, 'error');
            });
        }

        // Reset form
        function resetForm() {
            document.getElementById('inventoryForm').reset();
            document.getElementById('productId').readOnly = false;
            document.getElementById('formTitle').textContent = '➕ เพิ่มสินค้า';
            document.getElementById('submitBtn').textContent = 'เพิ่มสินค้า';
            document.getElementById('resetBtn').textContent = 'ล้างข้อมูล';
            generateLocationId(); // Reset location ID display
            editingId = null;
        }

        // Show alert message
        function showAlert(message, type) {
            const alertContainer = document.getElementById('alertContainer');
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} show`;
            alertDiv.textContent = message;
            alertContainer.innerHTML = '';
            alertContainer.appendChild(alertDiv);

            setTimeout(() => {
                alertDiv.classList.remove('show');
                setTimeout(() => alertDiv.remove(), 300);
            }, 4000);
        }

        // Close delete modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('deleteModal');
            if (event.target == modal) {
                closeDeleteModal();
            }
        }
    </script>
</body>
</html>
