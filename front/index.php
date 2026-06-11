<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบบริหารคลังสินค้าอัจฉริยะ (Inventory Dashboard)</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php
$activePage = 'inventory';
$pageTitle = 'คลังสินค้า';
$showInventoryToolbar = true;
?>

<!-- Mobile overlay -->
<div class="mobile-overlay" id="mobileOverlay" onclick="closeMobileSidebar()"></div>

<?php include __DIR__ . "/components/sidebar.php"; ?>

<!-- ═══════════ MAIN WRAPPER ═══════════ -->
<div class="main-wrapper" id="mainWrapper">

    <?php include __DIR__ . "/components/navbar.php"; ?>

    <!-- PAGE CONTENT -->
    <main class="page-content">

        <!-- Stats -->
        <div class="stats-row">
            <div class="stat-card stat-all">
                <div class="stat-icon">
                    <svg width="19" height="19" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <div>
                    <div class="stat-label">All Items</div>
                    <div class="stat-value" id="totalItems">0</div>
                </div>
            </div>
            <div class="stat-card stat-keep">
                <div class="stat-icon">
                    <svg width="19" height="19" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="stat-label">In Stock</div>
                    <div class="stat-value" id="keepItems">0</div>
                </div>
            </div>
            <div class="stat-card stat-empty">
                <div class="stat-icon">
                    <svg width="19" height="19" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="stat-label">Out of Stock</div>
                    <div class="stat-value" id="emptyItems">0</div>
                </div>
            </div>
        </div>

        <!-- Toolbar -->
        <div class="toolbar">
            <div class="toolbar-left">
                <div class="card-section-title">
                    <svg width="18" height="18" fill="none" stroke="var(--primary)" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    รายการสินค้าในคลัง
                </div>
            </div>
            <div class="toolbar-right" id="dispatchAllArea" style="display:none;">
                <!-- placeholder for future bulk actions -->
            </div>
        </div>

        <!-- Table Card -->
        <div class="main-card">
            <div class="table-responsive" id="tableContent">
                <div class="state-container">
                    <div style="margin-bottom:12px;"><div class="loading-dots"><span></span><span></span><span></span></div></div>
                    <p>กำลังโหลดข้อมูล...</p>
                </div>
            </div>
        </div>

    </main>
</div>

<!-- Toast -->
<div id="toastAlert" class="toast"></div>

<!-- ════════════════════════════════════════
     MODAL: เพิ่ม / แก้ไขสินค้า
════════════════════════════════════════ -->
<div id="productModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">➕ เพิ่มรายการสินค้าใหม่</h3>
            <button class="close-modal" onclick="closeProductModal()">×</button>
        </div>
        <form id="inventoryForm" onsubmit="submitInventoryForm(event)">
            <div class="modal-body">
                <div class="form-grid">
                    <div>
                        <div class="form-section-title">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            ข้อมูลสินค้าหลัก
                        </div>
                        <div class="form-group">
                            <label>ID สินค้า *</label>
                            <input type="text" id="productId" placeholder="เช่น PROD-001" required>
                        </div>
                        <div class="form-group">
                            <label>ชื่อสินค้า *</label>
                            <input type="text" id="productName" placeholder="ชื่อสินค้า/วัตถุ" required>
                        </div>
                        <div class="form-group">
                            <label>ปริมาณชิ้น *</label>
                            <input type="number" id="quantity" min="0" placeholder="0" required>
                        </div>
                        <div class="form-group">
                            <label>สถานะ *</label>
                            <select id="status" required>
                                <option value="">-- เลือกสถานะ --</option>
                                <option value="Keep">Keep (เก็บในคลัง)</option>
                                <option value="Empty">Empty (หมด)</option>
                            </select>
                        </div>
                    </div>
                    <div style="background:var(--surface-2);padding:18px;border-radius:12px;border:1px solid var(--border);">
                        <div class="form-section-title" style="color:var(--text-1);border-bottom-color:var(--border);">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><circle cx="12" cy="11" r="3"/></svg>
                            ตำแหน่งคลัง (Location)
                        </div>
                        <div class="form-group">
                            <label>อาคารคลังสินค้า</label>
                            <select id="warehouseCode">
                                <option value="A">คลังสินค้า A</option>
                                <option value="B">คลังสินค้า B</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>โซน (Zone)</label>
                            <select id="rowLocation">
                                <option value="A">โซน A</option>
                                <option value="B">โซน B</option>
                                <option value="C">โซน C</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>แถว (Row)</label>
                            <select id="columnLocation">
                                <option value="1">แถว 1</option>
                                <option value="2">แถว 2</option>
                                <option value="3">แถว 3</option>
                                <option value="4">แถว 4</option>
                                <option value="5">แถว 5</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>ชั้นความสูง (Level)</label>
                            <select id="level">
                                <option value="0">ชั้น 0 (พื้นดิน)</option>
                                <option value="1">ชั้น 1</option>
                                <option value="2">ชั้น 2</option>
                                <option value="3">ชั้น 3</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom:0">
                            <label>รหัสพิกัด (Location ID)</label>
                            <input type="text" id="locationId" readonly>
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

<!-- ════════════════════════════════════════
     MODAL: ยืนยันการลบ
════════════════════════════════════════ -->
<div id="deleteModal" class="modal">
    <div class="modal-content" style="max-width:440px;">
        <div class="modal-header" style="background:#fff5f5;">
            <h3 style="color:#c53030;">⚠ ยืนยันการลบรายการ</h3>
            <button class="close-modal" onclick="closeDeleteModal()">×</button>
        </div>
        <div class="modal-body">
            <p id="deleteMessage" style="color:var(--text-2);line-height:1.6;"></p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-cancel" onclick="closeDeleteModal()">ยกเลิก</button>
            <button class="btn btn-danger" onclick="confirmDelete()">ยืนยันลบ</button>
        </div>
    </div>
</div>

<!-- ════════════════════════════════════════
     MODAL: เบิกสินค้า (DISPATCH)
════════════════════════════════════════ -->
<div id="dispatchModal" class="modal">
    <div class="modal-content" style="max-width:540px;">
        <div class="modal-header" style="background:linear-gradient(135deg,#f0f9ff,#e0f2fe);">
            <h3 style="color:#0369a1;">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline;vertical-align:-3px;margin-right:6px;"><path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                เบิกใช้สินค้าออกจากคลัง
            </h3>
            <button class="close-modal" onclick="closeDispatchModal()">×</button>
        </div>
        <div class="modal-body">

            <!-- Product Info Summary -->
            <div class="dispatch-info-grid" id="dispatchInfoGrid">
                <!-- filled by JS -->
            </div>

            <!-- Quantity Section -->
            <div class="dispatch-qty-section">
                <div class="dispatch-qty-label">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                    ระบุจำนวนที่ต้องการเบิก
                </div>
                <div class="qty-input-row">
                    <button type="button" class="qty-btn" onclick="adjustDispatchQty(-1)">−</button>
                    <input
                        type="number"
                        class="qty-input-field"
                        id="dispatchQtyInput"
                        min="1" value="1"
                        oninput="updateRemainingDisplay()"
                    >
                    <button type="button" class="qty-btn" onclick="adjustDispatchQty(1)">+</button>
                </div>
                <div class="qty-remaining-row">
                    <span class="qty-remaining-label">คงเหลือหลังเบิก:</span>
                    <span class="qty-remaining-value" id="remainingDisplay">—</span>
                </div>
            </div>

            <p style="font-size:0.78rem;color:var(--text-3);margin-top:12px;">
                * เมื่อกดยืนยัน ระบบจะหักจำนวนออกจากฐานข้อมูลทันที และไม่สามารถยกเลิกได้
            </p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-cancel" onclick="closeDispatchModal()">ยกเลิก</button>
            <button class="btn btn-success" onclick="confirmDispatch()">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                ยืนยันเบิกสินค้า
            </button>
        </div>
    </div>
</div>

<!-- ════════════════════════════════════════
     JAVASCRIPT
════════════════════════════════════════ -->
<script>
    const API_URL = '/testapi/api/inventory.php';

    let inventoryCachedData = [];
    let filteredData = [];
    let isEditMode = false;
    let editingId = null;
    let deleteId = null;
    let dispatchItem = null; // current item being dispatched
    let sidebarCollapsed = false;

    // ─── INIT ───
    document.addEventListener('DOMContentLoaded', () => {
        loadInventory();
        setupLocationIdGenerator();
        setupMobileDetect();
    });

    // ─── SIDEBAR ───
    function toggleSidebar() {
        sidebarCollapsed = !sidebarCollapsed;
        const sidebar = document.getElementById('sidebar');
        const wrapper = document.getElementById('mainWrapper');
        const chevron = document.getElementById('sidebarChevron');
        sidebar.classList.toggle('collapsed', sidebarCollapsed);
        wrapper.classList.toggle('collapsed', sidebarCollapsed);
        chevron.style.transform = sidebarCollapsed ? 'rotate(180deg)' : '';
    }

    function openMobileSidebar() {
        document.getElementById('sidebar').classList.add('mobile-open');
        document.getElementById('mobileOverlay').style.display = 'block';
    }

    function closeMobileSidebar() {
        document.getElementById('sidebar').classList.remove('mobile-open');
        document.getElementById('mobileOverlay').style.display = 'none';
    }

    function setupMobileDetect() {
        const mq = window.matchMedia('(max-width: 768px)');
        const update = (e) => {
            document.getElementById('mobileMenuBtn').style.display = e.matches ? 'flex' : 'none';
        };
        mq.addEventListener('change', update);
        update(mq);
    }

    // ─── FILTER BAR ───
    let filterBarVisible = false;

    function showFilterBar() {
        if (!filterBarVisible) {
            filterBarVisible = true;
            document.getElementById('filterBar').classList.remove('hidden');
        }
    }

    function toggleFilterBar() {
        filterBarVisible = !filterBarVisible;
        document.getElementById('filterBar').classList.toggle('hidden', !filterBarVisible);
    }

    function applyFilters() {
        const q = document.getElementById('searchInput').value.trim().toLowerCase();
        const statusF = document.getElementById('filterStatus').value;
        const warehouseF = document.getElementById('filterWarehouse').value;
        const dateF = document.getElementById('filterDate').value;

        const now = new Date();
        filteredData = inventoryCachedData.filter(item => {
            // text search
            if (q) {
                const haystack = `${item.product_id} ${item.product_name} ${item.location_id}`.toLowerCase();
                if (!haystack.includes(q)) return false;
            }
            // status
            if (statusF && item.status !== statusF) return false;
            if (warehouseF) {
                const wh = (item.warehouse || (item.location_id || '').charAt(0)).toUpperCase();
                if (wh !== warehouseF) return false;
            }
            // date
            if (dateF) {
                const d = new Date(item.created_at);
                const diffDays = (now - d) / 86400000;
                if (dateF === 'today' && diffDays > 1) return false;
                if (dateF === 'week' && diffDays > 7) return false;
                if (dateF === 'month' && diffDays > 30) return false;
            }
            return true;
        });

        const badge = document.getElementById('filterResultsBadge');
        if (filteredData.length === inventoryCachedData.length) {
            badge.textContent = 'แสดงทั้งหมด';
        } else {
            badge.textContent = `พบ ${filteredData.length} / ${inventoryCachedData.length} รายการ`;
        }

        displayTable(filteredData, q);
    }

    function handleSearch() {
        showFilterBar();
        applyFilters();
    }

    function clearFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('filterStatus').value = '';
        document.getElementById('filterWarehouse').value = '';
        document.getElementById('filterDate').value = '';
        applyFilters();
    }

    // ─── LOAD DATA ───
    function loadInventory() {
        const container = document.getElementById('tableContent');
        container.innerHTML = `<div class="state-container"><div style="margin-bottom:12px"><div class="loading-dots"><span></span><span></span><span></span></div></div><p>กำลังโหลดข้อมูลจากระบบ...</p></div>`;

        fetch(API_URL, { method: 'GET' })
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') {
                    inventoryCachedData = res.data;
                    filteredData = [...inventoryCachedData];
                    updateStatistics(inventoryCachedData);
                    applyFilters();
                } else {
                    container.innerHTML = `<div class="state-container"><div class="state-icon">⚠️</div><p>ไม่สามารถโหลดข้อมูลได้</p></div>`;
                }
            })
            .catch(err => {
                container.innerHTML = `<div class="state-container"><div class="state-icon">🔌</div><p>เชื่อมต่อฐานข้อมูลไม่สำเร็จ: ${err.message}</p></div>`;
            });
    }

    // ─── DISPLAY TABLE ───
    function displayTable(items, highlight = '') {
        const container = document.getElementById('tableContent');
        if (!items || items.length === 0) {
            container.innerHTML = `<div class="state-container"><div class="state-icon">🔍</div><p>ไม่พบรายการที่ตรงกับเงื่อนไข</p></div>`;
            return;
        }

        const hl = (text) => {
            if (!highlight) return escHtml(text);
            const re = new RegExp(`(${escRegex(highlight)})`, 'gi');
            return escHtml(String(text)).replace(re, '<mark class="search-highlight">$1</mark>');
        };

        let html = `<table>
            <thead><tr>
                <th>ID สินค้า</th>
                <th>ชื่อสินค้า</th>
                <th>ปริมาณ</th>
                <th>สถานะ</th>
                <th>Location ID</th>
                <th>วันที่บันทึก</th>
                <th>เบิกสินค้า</th>
                ${isEditMode ? '<th>การกระทำ</th>' : ''}
            </tr></thead><tbody>`;

        items.forEach(item => {
            const badgeClass = item.status === 'Keep' ? 'badge-success' : 'badge-danger';
            const badgeText = item.status === 'Keep' ? '✓ Keep' : '✕ Empty';
            const dateFmt = new Date(item.created_at).toLocaleString('th-TH', { hour12: false });
            const locId = item.location_id || 'AA-1-0';
            const itemId = Number(item.id);
            const quantity = Number(item.quantity);
            const canDispatch = item.status === 'Keep' && quantity > 0;

            html += `<tr>
                <td style="font-weight:700;color:var(--text-1);">${hl(item.product_id)}</td>
                <td>${hl(item.product_name)}</td>
                <td style="font-weight:600;">${quantity}</td>
                <td><span class="badge ${badgeClass}">${badgeText}</span></td>
                <td><span class="location-tag">${hl(locId)}</span></td>
                <td style="color:var(--text-3);font-size:0.82rem;">${dateFmt}</td>
                <td>
                    <button class="btn-row btn-row-dispatch"
                        ${!canDispatch ? 'disabled style="opacity:.4;cursor:not-allowed;"' : ''}
                        onclick="${canDispatch ? `openDispatchModal(${itemId})` : ''}">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline;vertical-align:-1px;"><path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        เบิก
                    </button>
                </td>
                ${isEditMode ? `<td>
                    <div class="action-cell">
                        <button class="btn-row btn-row-edit" onclick="editItem(${itemId})">แก้ไข</button>
                        <button class="btn-row btn-row-delete" onclick="openDeleteModal(${itemId}, '${escAttr(item.product_name)}')">ลบ</button>
                    </div>
                </td>` : ''}
            </tr>`;
        });

        html += '</tbody></table>';
        container.innerHTML = html;
    }

    // ─── STATS ───
    function updateStatistics(items) {
        const total = items.length;
        const keep = items.filter(i => i.status === 'Keep').length;
        const empty = items.filter(i => i.status === 'Empty').length;
        document.getElementById('totalItems').textContent = total;
        document.getElementById('keepItems').textContent = keep;
        document.getElementById('emptyItems').textContent = empty;
    }

    // ─── EDIT MODE ───
    function toggleEditMode() {
        isEditMode = !isEditMode;
        document.getElementById('toggleEditBtn').classList.toggle('active', isEditMode);
        displayTable(filteredData, document.getElementById('searchInput').value.trim().toLowerCase());
    }

    // ─── ADD / EDIT MODAL ───
    function openAddModal() {
        editingId = null;
        document.getElementById('inventoryForm').reset();
        document.getElementById('productId').readOnly = false;
        document.getElementById('modalTitle').textContent = '➕ เพิ่มรายการสินค้าใหม่';
        document.getElementById('submitBtn').textContent = 'บันทึกสินค้า';
        generateLocationId();
        document.getElementById('productModal').classList.add('open');
    }

    function closeProductModal() {
        document.getElementById('productModal').classList.remove('open');
    }

    function submitInventoryForm(e) {
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
        const body = editingId ? { id: editingId, ...payload } : payload;

        fetch(API_URL, {
            method, headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body)
        })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                showToast(editingId ? 'อัปเดตข้อมูลสำเร็จ ✓' : 'เพิ่มสินค้าสำเร็จ ✓', 'success');
                closeProductModal();
                loadInventory();
            } else {
                showToast('เกิดข้อผิดพลาด: ' + res.message, 'error');
            }
        })
        .catch(err => showToast('ส่งข้อมูลล้มเหลว: ' + err.message, 'error'));
    }

    function editItem(id) {
        fetch(`${API_URL}?id=${id}`)
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') {
                    const item = res.data;
                    editingId = id;
                    document.getElementById('productId').value = item.product_id;
                    document.getElementById('productId').readOnly = false;
                    document.getElementById('productName').value = item.product_name;
                    document.getElementById('quantity').value = item.quantity;
                    document.getElementById('status').value = item.status;
                    document.getElementById('warehouseCode').value = item.warehouse || 'A';
                    document.getElementById('rowLocation').value = item.row_location || 'A';
                    document.getElementById('columnLocation').value = item.column_location || '1';
                    document.getElementById('level').value = item.level || '0';
                    generateLocationId();
                    document.getElementById('modalTitle').textContent = '✏️ แก้ไขข้อมูลสินค้า';
                    document.getElementById('submitBtn').textContent = 'บันทึกการแก้ไข';
                    document.getElementById('productModal').classList.add('open');
                } else {
                    showToast('โหลดข้อมูลไม่สำเร็จ', 'error');
                }
            })
            .catch(err => showToast('เกิดข้อผิดพลาด: ' + err.message, 'error'));
    }

    // ─── DELETE MODAL ───
    function openDeleteModal(id, name) {
        deleteId = id;
        document.getElementById('deleteMessage').textContent = `คุณยืนยันการลบสินค้า "${name}" ออกจากฐานข้อมูลจริงหรือไม่? ขั้นตอนนี้ไม่สามารถย้อนคืนได้`;
        document.getElementById('deleteModal').classList.add('open');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('open');
        deleteId = null;
    }

    function confirmDelete() {
        if (!deleteId) return;
        fetch(API_URL, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: deleteId })
        })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                showToast('ลบสินค้าสำเร็จ ✓', 'success');
                closeDeleteModal();
                loadInventory();
            } else {
                showToast('ลบไม่สำเร็จ: ' + res.message, 'error');
            }
        })
        .catch(err => showToast('เกิดข้อผิดพลาด: ' + err.message, 'error'));
    }

    // ─── DISPATCH MODAL ───
    function openDispatchModal(id) {
        const item = inventoryCachedData.find(i => Number(i.id) === Number(id));
        if (!item) {
            showToast('ไม่พบข้อมูลสินค้าที่ต้องการเบิก กรุณาโหลดข้อมูลใหม่', 'error');
            return;
        }
        dispatchItem = item;
        dispatchItem.id = Number(dispatchItem.id);
        dispatchItem.quantity = Number(dispatchItem.quantity);

        const locId = item.location_id || 'N/A';
        const dateFmt = new Date(item.created_at).toLocaleString('th-TH', { hour12: false });

        document.getElementById('dispatchInfoGrid').innerHTML = `
            <div class="dispatch-info-item">
                <div class="di-label">ID สินค้า</div>
                <div class="di-value">${escHtml(item.product_id)}</div>
            </div>
            <div class="dispatch-info-item">
                <div class="di-label">ชื่อสินค้า</div>
                <div class="di-value">${escHtml(item.product_name)}</div>
            </div>
            <div class="dispatch-info-item">
                <div class="di-label">คงเหลือในคลัง</div>
                <div class="di-value" style="color:var(--success);">${item.quantity} ชิ้น</div>
            </div>
            <div class="dispatch-info-item">
                <div class="di-label">Location ID</div>
                <div class="di-value"><span class="location-tag">${escHtml(locId)}</span></div>
            </div>
            <div class="dispatch-info-item">
                <div class="di-label">สถานะ</div>
                <div class="di-value"><span class="badge badge-success">✓ Keep</span></div>
            </div>
            <div class="dispatch-info-item">
                <div class="di-label">บันทึกเมื่อ</div>
                <div class="di-value" style="font-size:0.8rem;">${dateFmt}</div>
            </div>
        `;

        const qtyInput = document.getElementById('dispatchQtyInput');
        qtyInput.max = item.quantity;
        qtyInput.value = 1;
        updateRemainingDisplay();

        document.getElementById('dispatchModal').classList.add('open');
    }

    function closeDispatchModal() {
        document.getElementById('dispatchModal').classList.remove('open');
        dispatchItem = null;
    }

    function adjustDispatchQty(delta) {
        const input = document.getElementById('dispatchQtyInput');
        let v = parseInt(input.value) || 0;
        v = Math.max(1, Math.min(dispatchItem.quantity, v + delta));
        input.value = v;
        updateRemainingDisplay();
    }

    function updateRemainingDisplay() {
        if (!dispatchItem) return;
        const qty = parseInt(document.getElementById('dispatchQtyInput').value) || 0;
        const remaining = dispatchItem.quantity - qty;
        const el = document.getElementById('remainingDisplay');

        el.textContent = remaining >= 0 ? `${remaining} ชิ้น` : 'เกินจำนวน!';
        el.className = 'qty-remaining-value';

        if (remaining < 0) {
            el.classList.add('qty-remaining-bad');
        } else if (remaining === 0) {
            el.classList.add('qty-remaining-warn');
        } else {
            el.classList.add('qty-remaining-ok');
        }
    }

    function confirmDispatch() {
        if (!dispatchItem) return;

        const qtyToDispatch = parseInt(document.getElementById('dispatchQtyInput').value) || 0;
        if (qtyToDispatch <= 0) { showToast('กรุณาระบุจำนวนที่ถูกต้อง', 'error'); return; }
        if (qtyToDispatch > dispatchItem.quantity) { showToast('จำนวนเบิกเกินกว่าที่มีในคลัง', 'error'); return; }

        const newQty = dispatchItem.quantity - qtyToDispatch;
        const newStatus = newQty === 0 ? 'Empty' : 'Keep';

        // Build update payload matching the existing PUT structure
        const payload = {
            id: dispatchItem.id,
            productId: dispatchItem.product_id,
            productName: dispatchItem.product_name,
            quantity: newQty,
            status: newStatus,
            warehouseCode: dispatchItem.warehouse || (dispatchItem.location_id || 'A').charAt(0),
            rowLocation: dispatchItem.row_location || 'A',
            columnLocation: parseInt(dispatchItem.column_location) || 1,
            level: parseInt(dispatchItem.level) || 0
        };

        fetch(API_URL, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                showToast(`เบิกสินค้า "${dispatchItem.product_name}" จำนวน ${qtyToDispatch} ชิ้นสำเร็จ ✓`, 'success');
                closeDispatchModal();
                loadInventory();
            } else {
                showToast('เบิกสินค้าไม่สำเร็จ: ' + res.message, 'error');
            }
        })
        .catch(err => showToast('เกิดข้อผิดพลาด: ' + err.message, 'error'));
    }

    // ─── LOCATION ID GENERATOR ───
    function setupLocationIdGenerator() {
        ['warehouseCode', 'rowLocation', 'columnLocation', 'level'].forEach(id => {
            document.getElementById(id).addEventListener('change', generateLocationId);
        });
    }

    function generateLocationId() {
        const wh = document.getElementById('warehouseCode').value;
        const row = document.getElementById('rowLocation').value;
        const col = document.getElementById('columnLocation').value;
        const lvl = document.getElementById('level').value;
        document.getElementById('locationId').value = `${wh}${row}-${col}-${lvl}`;
    }

    // ─── TOAST ───
    function showToast(msg, type = 'success') {
        const el = document.getElementById('toastAlert');
        el.className = `toast toast-${type} show`;
        el.textContent = msg;
        clearTimeout(el._timer);
        el._timer = setTimeout(() => el.classList.remove('show'), 3800);
    }

    // ─── COMING SOON ───
    function showComingSoon(name) {
        showToast(`${name} — เร็วๆ นี้ 🚧`, 'warning');
    }

    // ─── CLOSE MODALS ON OVERLAY CLICK ───
    window.addEventListener('click', e => {
        if (e.target.id === 'productModal') closeProductModal();
        if (e.target.id === 'deleteModal') closeDeleteModal();
        if (e.target.id === 'dispatchModal') closeDispatchModal();
    });

    // ─── HELPERS ───
    function escHtml(s) {
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
    function escAttr(s) { return String(s).replace(/'/g, "\\'"); }
    function escRegex(s) { return s.replace(/[.*+?^${}()|[\]\\]/g,'\\$&'); }
</script>
</body>
</html>
