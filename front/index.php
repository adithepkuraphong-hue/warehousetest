<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WereHouse Testrun (Inventory Dashboard)</title>
    <style>
        /* Modern Design System & Google Fonts */
        @import url('https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', 'Chakra+Petch', sans-serif;
        }

        body {
            background-color: #f8fafc;
            color: #1e293b;
            padding: 24px;
            min-height: 100vh;
        }

        .container {
            max-width: 1440px;
            margin: 0 auto;
        }

        /* Top Bar Layout */
        .top-bar {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-bottom: 24px;
        }

        @media (min-width: 1024px) {
            .top-bar {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }
        }

        /* Beautiful Stats Grid (ภาพที่ 1) */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            width: 100%;
        }

        @media (min-width: 768px) {
            .stats-grid {
                width: auto;
                min-width: 550px;
                gap: 16px;
            }
        }

        .stat-card {
            background: white;
            padding: 14px 18px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            overflow: hidden;
        }

        .stat-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 10px;
        }

        .stat-info {
            display: flex;
            flex-direction: column;
        }

        .stat-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            font-weight: 600;
        }

        .stat-value {
            font-size: 1.3rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.2;
        }

        /* Stats Color Themes */
        .stat-all .stat-icon { background: #eef2f6; color: #475569; }
        .stat-keep .stat-icon { background: #ecfdf5; color: #059669; }
        .stat-empty .stat-icon { background: #fff1f2; color: #e11d48; }

        /* Action Buttons Area */
        .action-controls {
            display: flex;
            gap: 12px;
            width: 100%;
            justify-content: flex-end;
        }

        @media (min-width: 1024px) {
            .action-controls {
                width: auto;
            }
        }

        /* Core Buttons Style */
        .btn {
            padding: 10px 18px;
            border: none;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .btn-primary {
            background-color: #4f46e5;
            color: white;
        }

        .btn-primary:hover {
            background-color: #4338ca;
            transform: translateY(-1px);
        }

        .btn-warning {
            background-color: white;
            color: #d97706;
            border: 1px solid #fcd34d;
        }

        .btn-warning:hover {
            background-color: #fffbeb;
        }

        .btn-warning.active {
            background-color: #fef3c7;
            border-color: #f59e0b;
            color: #b45309;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
        }

        .btn-refresh {
            background: white;
            border: 1px solid #e2e8f0;
            color: #64748b;
            padding: 8px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-refresh:hover {
            background: #f1f5f9;
            color: #1e293b;
        }

        /* Content Card / Table Layout */
        .main-card {
            background: white;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            padding: 24px;
            overflow: hidden;
        }

        .card-header-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Responsive Table */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.9rem;
        }

        table th {
            background-color: #f8fafc;
            padding: 14px 20px;
            font-weight: 600;
            color: #64748b;
            border-bottom: 1px solid #e2e8f0;
            white-space: nowrap;
        }

        table td {
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            white-space: nowrap;
            vertical-align: middle;
        }

        table tbody tr:last-child td {
            border-bottom: none;
        }

        table tbody tr:hover {
            background-color: #f8fafc;
        }

        /* Status Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge-success { background-color: #d1fae5; color: #065f46; }
        .badge-danger { background-color: #ffe4e6; color: #9f1239; }

        .location-tag {
            color: #3b82f6;
            font-weight: 600;
            background: #eff6ff;
            padding: 4px 8px;
            border-radius: 6px;
            border: 1px solid #bfdbfe;
        }

        /* Dynamic Actions Buttons (ภาพที่ 2) */
        .action-cell-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-row-edit {
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid #bfdbfe;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.8rem;
            cursor: pointer;
        }
        .btn-row-edit:hover { background: #dbeafe; }

        .btn-row-delete {
            background: #fff1f2;
            color: #e11d48;
            border: 1px solid #fecdd3;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.8rem;
            cursor: pointer;
        }
        .btn-row-delete:hover { background: #ffe4e6; }

        /* Professional Modal Setup (ภาพที่ 4) */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(4px);
        }

        .modal-content {
            background-color: white;
            margin: 4% auto;
            border-radius: 16px;
            width: 92%;
            max-width: 800px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            animation: modalFadeIn 0.25s ease-out;
        }

        @keyframes modalFadeIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 24px;
            border-bottom: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        .modal-header h3 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #0f172a;
        }

        .close-modal {
            color: #94a3b8;
            font-size: 24px;
            cursor: pointer;
            transition: color 0.2s;
        }
        .close-modal:hover { color: #475569; }

        .modal-body {
            padding: 24px;
        }

        /* 2-Column Responsive Grid Form Layout */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
        }

        @media (min-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        .form-column-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #4f46e5;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 6px;
            border-bottom: 2px solid #e0e7ff;
            padding-bottom: 6px;
        }

        .form-group {
            margin-bottom: 14px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: #475569;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 0.9rem;
            color: #1e293b;
            transition: all 0.2s;
            background-color: white;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        }

        .form-group input[readonly] {
            background-color: #f1f5f9;
            color: #64748b;
            font-weight: 600;
            border-color: #e2e8f0;
        }

        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid #e2e8f0;
            background: #f8fafc;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .btn-cancel {
            background: white;
            border: 1px solid #cbd5e1;
            color: #475569;
        }
        .btn-cancel:hover { background: #f1f5f9; }

        /* Toast Modern Notification */
        .toast {
            position: fixed;
            top: 24px;
            right: 24px;
            padding: 16px 24px;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
            z-index: 1100;
            transform: translateY(-20px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            display: none;
        }

        .toast.show {
            display: block;
            transform: translateY(0);
            opacity: 1;
        }

        .toast-success { background-color: #10b981; }
        .toast-error { background-color: #ef4444; }

        /* Status States */
        .loading-state { text-align: center; padding: 40px; color: #4f46e5; font-weight: 600; }
        .empty-state { text-align: center; padding: 40px; color: #94a3b8; }
    </style>
</head>
<body>

    <div class="container">
        <div id="toastAlert" class="toast"></div>

        <div class="top-bar">
            <div style="display: flex; align-items: center; gap: 12px; width: 100%; justify-content: space-between;">
                <div class="stats-grid">
                    <div class="stat-card stat-all">
                        <div class="stat-icon">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <div class="stat-info">
                            <span class="stat-label">All</span>
                            <span class="stat-value" id="totalItems">0</span>
                        </div>
                    </div>
                    <div class="stat-card stat-keep">
                        <div class="stat-icon">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="stat-info">
                            <span class="stat-label">Stock</span>
                            <span class="stat-value" id="keepItems">0</span>
                        </div>
                    </div>
                    <div class="stat-card stat-empty">
                        <div class="stat-icon">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="stat-info">
                            <span class="stat-label">Remove</span>
                            <span class="stat-value" id="emptyItems">0</span>
                        </div>
                    </div>
                </div>
                <button class="btn-refresh" title="รีเฟรชข้อมูล" onclick="loadInventory()">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H17"></path></svg>
                </button>
            </div>

            <div class="action-controls">
                <button class="btn btn-primary" onclick="openAddModal()">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
                    New Order
                </button>
                <button class="btn btn-warning" id="toggleEditBtn" onclick="toggleEditMode()">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Edit
                </button>
            </div>
        </div>

        <div class="main-card">
            <div class="card-header-title">
                <svg width="22" height="22" fill="none" stroke="#4f46e5" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                รายการข้อมูลในระบบ Database
            </div>
            <div class="table-responsive" id="tableContent">
                </div>
        </div>
    </div>

    <div id="productModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">เพิ่มรายการสินค้า</h3>
                <span class="close-modal" onclick="closeProductModal()">&times;</span>
            </div>
            <form id="inventoryForm">
                <div class="modal-body">
                    <div class="form-grid">
                        <div>
                            <div class="form-column-title">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                ข้อมูลสินค้าหลัก
                            </div>
                            <div class="form-group">
                                <label for="productId">ID สินค้า *</label>
                                <input type="text" id="productId" name="productId" placeholder="ตัวอย่าง: PROD-001" required>
                            </div>
                            <div class="form-group">
                                <label for="productName">ชื่อสินค้า *</label>
                                <input type="text" id="productName" name="productName" placeholder="ระบุชื่อเรียกของวัตถุ/สินค้า" required>
                            </div>
                            <div class="form-group">
                                <label for="quantity">ปริมาณชิ้น *</label>
                                <input type="number" id="quantity" name="quantity" min="0" placeholder="0" required>
                            </div>
                            <div class="form-group">
                                <label for="status">สถานะจัดเก็บ *</label>
                                <select id="status" name="status" required>
                                    <option value="">-- กรุณาเลือกสถานะ --</option>
                                    <option value="Keep">Keep (เก็บในคลัง)</option>
                                    <option value="Empty">Empty (หมดชั่วคราว)</option>
                                </select>
                            </div>
                        </div>

                        <div style="background-color: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0;">
                            <div class="form-column-title" style="color: #0f172a; border-bottom-color: #cbd5e1;">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                ตำแหน่งคลังจัดเก็บ (Location)
                            </div>
                            <div class="form-group">
                                <label for="warehouseCode">อาคารคลังสินค้า (Warehouse)</label>
                                <select id="warehouseCode" name="warehouseCode">
                                    <option value="A">คลังสินค้า A</option>
                                    <option value="B">คลังสินค้า B</option>
                                    <option value="C">คลังสินค้า C</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="rowLocation">แถวชั้นวาง (Row)</label>
                                <select id="rowLocation" name="rowLocation">
                                    <option value="A">แถว A</option>
                                    <option value="B">แถว B</option>
                                    <option value="C">แถว C</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="columnLocation">ช่องแนวตั้ง (Column)</label>
                                <select id="columnLocation" name="columnLocation">
                                    <option value="1">ช่องที่ 1</option>
                                    <option value="2">ช่องที่ 2</option>
                                    <option value="3">ช่องที่ 3</option>
                                    <option value="4">ช่องที่ 4</option>
                                    <option value="5">ช่องที่ 5</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="level">ชั้นความสูง (Level)</label>
                                <select id="level" name="level">
                                    <option value="0">ชั้น 0</option>
                                    <option value="1">ชั้น 1</option>
                                    <option value="2">ชั้น 2</option>
                                    <option value="3">ชั้น 3</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label for="locationId">รหัสพิกัดอ้างอิง (Location ID)</label>
                                <input type="text" id="locationId" name="locationId" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" onclick="closeProductModal()">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">บันทึกสินค้า</button>
                </div>
            </form>
        </div>
    </div>

    <div id="deleteModal" class="modal">
        <div class="modal-content" style="max-width: 440px;">
            <div class="modal-header" style="background: #fff5f5;">
                <h3 style="color: #c53030;">⚠ ยืนยันการลบรายการ</h3>
                <span class="close-modal" onclick="closeDeleteModal()">&times;</span>
            </div>
            <div class="modal-body">
                <p id="deleteMessage" style="color: #4a5568; line-height: 1.5;">คุณยืนยันการลบสินค้าชิ้นนี้ออกจากระบบฐานข้อมูลจริงหรือไม่? ขั้นตอนนี้ไม่สามารถย้อนคืนได้</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-cancel" onclick="closeDeleteModal()">ยกเลิก</button>
                <button class="btn" style="background-color: #e11d48; color: white;" onclick="confirmDelete()">ยืนยันลบ</button>
            </div>
        </div>
    </div>

    <script>
        const API_URL = '/testapi/api/inventory.php';
        let inventoryCachedData = []; 
        let isEditMode = false;       
        let editingId = null;
        let deleteId = null;

        document.addEventListener('DOMContentLoaded', function() {
            loadInventory();
            setupLocationIdGenerator();
            setupFormSubmission();
        });

        // จัดการเปิด/ปิดแท็บโหมดแก้ไขข้อมูล เพื่อสลับคอลัมน์การกระทำแบบ Dynamic (Image 1 <-> Image 2)
        function toggleEditMode() {
            isEditMode = !isEditMode;
            const toggleEditBtn = document.getElementById('toggleEditBtn');
            if (isEditMode) {
                toggleEditBtn.classList.add('active');
            } else {
                toggleEditBtn.classList.remove('active');
            }
            displayTable(inventoryCachedData);
        }

        // เชื่อมผูกพิกัด Location ID Generator 
        function setupLocationIdGenerator() {
            const fields = ['warehouseCode', 'rowLocation', 'columnLocation', 'level'];
            fields.forEach(f => document.getElementById(f).addEventListener('change', generateLocationId));
        }

        function generateLocationId() {
            const wh = document.getElementById('warehouseCode').value;
            const row = document.getElementById('rowLocation').value;
            const col = document.getElementById('columnLocation').value;
            const lvl = document.getElementById('level').value;
            document.getElementById('locationId').value = `${wh}${row}-${col}-${lvl}`;
        }

        // โจมตีดึงข้อมูลผ่าน API คลังสินค้า
        function loadInventory() {
            const container = document.getElementById('tableContent');
            container.innerHTML = '<div class="loading-state">กำลังดึงข้อมูลเรียลไทม์จากระบบ Database...</div>';

            fetch(API_URL, { method: 'GET' })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    inventoryCachedData = res.data;
                    displayTable(inventoryCachedData);
                    updateStatistics(inventoryCachedData);
                } else {
                    container.innerHTML = '<div class="empty-state">ไม่สามารถอ่านข้อมูลโครงสร้างระบบได้</div>';
                }
            })
            .catch(err => {
                console.error(err);
                container.innerHTML = `<div class="empty-state">เกิดข้อผิดพลาดในการเชื่อมต่อ: ${err.message}</div>`;
            });
        }

        // ประกอบร่างตาราง HTML (Image 1 พื้นฐานจะซ่อนการกระทำ, Image 2 จะเปิดแสดง)
        function displayTable(items) {
            const container = document.getElementById('tableContent');
            
            if (!items || items.length === 0) {
                container.innerHTML = '<div class="empty-state">ไม่พบรายการข้อมูลสินค้าใดๆ ในคลัง</div>';
                return;
            }

            let html = `<table>
                <thead>
                    <tr>
                        <th>ID สินค้า</th>
                        <th>ชื่อสินค้า</th>
                        <th>ปริมาณชิ้น</th>
                        <th>สถานะจัดเก็บ</th>
                        <th>รหัสพิกัด (Location ID)</th>
                        <th>วันที่บันทึกระบบ</th>
                        ${isEditMode ? '<th>การกระทำ [Action]</th>' : ''}
                    </tr>
                </thead>
                <tbody>`;

            items.forEach(item => {
                const badgeClass = item.status === 'Keep' ? 'badge-success' : 'badge-danger';
                const badgeText = item.status === 'Keep' ? 'Keep (เก็บไว้)' : 'Empty (หมด)';
                const dateFmt = new Date(item.created_at).toLocaleString('th-TH', { hour12: false });
                const locId = item.location_id || 'AA-1-0';

                html += `<tr>
                    <td style="font-weight:600; color:#0f172a;">${item.product_id}</td>
                    <td>${item.product_name}</td>
                    <td style="font-weight:500;">${item.quantity}</td>
                    <td><span class="badge ${badgeClass}">${badgeText}</span></td>
                    <td><span class="location-tag">${locId}</span></td>
                    <td style="color:#64748b; font-size:0.85rem;">${dateFmt}</td>
                    ${isEditMode ? `
                    <td>
                        <div class="action-cell-buttons">
                            <button type="button" class="btn-row-edit" onclick="editItem(${item.id})">แก้ไข</button>
                            <button type="button" class="btn-row-delete" onclick="openDeleteModal(${item.id}, '${item.product_name}')">ลบ</button>
                        </div>
                    </td>` : ''}
                </tr>`;
            });

            html += '</tbody></table>';
            container.innerHTML = html;
        }

        // อัปเดตตัวเลขการ์ดสถิติตัวชี้วัดข้อมูลแดชบอร์ดด้านบนสุด
        function updateStatistics(items) {
            document.getElementById('totalItems').textContent = items.length;
            document.getElementById('keepItems').textContent = items.filter(i => i.status === 'Keep').length;
            document.getElementById('emptyItems').textContent = items.filter(i => i.status === 'Empty').length;
        }

        // เปิด-ปิดการควบคุม Modal Popup ฟอร์มหลัก (Image 4)
        function openAddModal() {
            editingId = null;
            document.getElementById('inventoryForm').reset();
            document.getElementById('productId').readOnly = false;
            document.getElementById('modalTitle').textContent = '➕ เพิ่มรายการสินค้าชิ้นใหม่';
            document.getElementById('submitBtn').textContent = 'บันทึกสินค้าใหม่';
            generateLocationId();
            document.getElementById('productModal').style.display = 'block';
        }

        function closeProductModal() {
            document.getElementById('productModal').style.display = 'none';
        }

        // ฟังก์ชันส่ง Request ไปจัดเก็บบันทึกบนฐานข้อมูล (ทำงานเสร็จฟอร์มจะยุบลงพร้อม Toast แจ้งเตือน)
        function setupFormSubmission() {
            document.getElementById('inventoryForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const payload = {
                    productId: document.getElementById('productId').value.trim(),
                    productName: document.getElementById('productName').value.trim(),
                    quantity: parseInt(document.getElementById('quantity').value),
                    status: document.getElementById('status').value,
                    warehouseCode: document.getElementById('warehouseCode').value,
                    rowLocation: document.getElementById('rowLocation').value,
                    columnLocation: parseInt(document.getElementById('columnLocation').value),
                    level: parseInt(document.getElementById('level').value)
                };

                const method = editingId ? 'PUT' : 'POST';
                const finalBody = editingId ? { id: editingId, ...payload } : payload;

                fetch(API_URL, {
                    method: method,
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(finalBody)
                })
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        showToast(editingId ? 'อัปเดตแก้ไขข้อมูลสินค้าสำเร็จเรียบร้อย ✓' : 'บันทึกเสร็จสิ้น! ข้อมูลเข้าสู่ฐานข้อมูลคลังแล้ว ✓', 'success');
                        closeProductModal();
                        loadInventory();
                    } else {
                        showToast('เกิดข้อผิดพลาดจากระบบ: ' + res.message, 'error');
                    }
                })
                .catch(err => showToast('ไม่สามารถบันทึกข้อมูลได้: ' + err.message, 'error'));
            });
        }

        // เรียกดูข้อมูลเก่ารายชิ้นเพื่อโหลดขึ้นมาแสดงบนฟอร์ม Popup แก้ไข (Image 4)
        function editItem(id) {
            fetch(`${API_URL}?id=${id}`, { method: 'GET' })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    const item = res.data;
                    editingId = id;
                    
                    document.getElementById('productId').value = item.product_id;
                    document.getElementById('productId').readOnly = true; 
                    document.getElementById('productName').value = item.product_name;
                    document.getElementById('quantity').value = item.quantity;
                    document.getElementById('status').value = item.status;
                    
                    document.getElementById('warehouseCode').value = item.warehouse || 'A';
                    document.getElementById('rowLocation').value = item.row_location || 'A';
                    document.getElementById('columnLocation').value = item.column_location || '1';
                    document.getElementById('level').value = item.level || '0';
                    
                    generateLocationId();
                    
                    document.getElementById('modalTitle').textContent = '✏️ แก้ไขและอัปเดตข้อมูลสินค้า';
                    document.getElementById('submitBtn').textContent = 'บันทึกการแก้ไขข้อมูล';
                    document.getElementById('productModal').style.display = 'block';
                } else {
                    showToast('ไม่สามารถดึงข้อมูลสินค้าชิ้นนี้ได้', 'error');
                }
            })
            .catch(err => showToast(`พบปัญหาเซิร์ฟเวอร์: ${err.message}`, 'error'));
        }

        // ป๊อปอัพยืนยันการลบข้อมูลคลังสินค้าออกจากระบบจริง
        function openDeleteModal(id, pName) {
            deleteId = id;
            document.getElementById('deleteMessage').textContent = `คุณยืนยันการลบสินค้า "${pName}" ออกจากระบบฐานข้อมูลจริงหรือไม่?`;
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
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: deleteId })
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    showToast('ลบสินค้าออกจากระบบฐานข้อมูลสำเร็จแล้ว ✓', 'success');
                    closeDeleteModal();
                    loadInventory();
                } else {
                    showToast('ไม่สามารถลบข้อมูลได้: ' + res.message, 'error');
                }
            })
            .catch(err => showToast(`พบปัญหา: ${err.message}`, 'error'));
        }

        // แสดงผล Toast แจ้งเตือนแบบโมเดิร์น (เด้งเตือนมุมบนขวาเมื่อทำรายการเสร็จสิ้น)
        function showToast(msg, type) {
            const element = document.getElementById('toastAlert');
            element.className = `toast toast-${type} show`;
            element.textContent = msg;

            setTimeout(() => {
                element.classList.remove('show');
            }, 3500);
        }

        // ตรวจจับการกดพื้นที่นอกหน้าต่างป๊อปอัพ (Modal) เพื่อสั่งปิดหน้าต่างอัตโนมัติ
        window.onclick = function(e) {
            const prodM = document.getElementById('productModal');
            const delM = document.getElementById('deleteModal');
            if (e.target == prodM) closeProductModal();
            if (e.target == delM) closeDeleteModal();
        }
    </script>
</body>
</html>