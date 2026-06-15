<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เปิด Product Order</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php
$activePage = 'product_order';
$pageTitle = 'เปิด Product Order';
$showInventoryToolbar = false;
?>

<div class="mobile-overlay" id="mobileOverlay" onclick="closeMobileSidebar()"></div>
<?php include __DIR__ . "/components/sidebar.php"; ?>

<div class="main-wrapper" id="mainWrapper">
    <?php include __DIR__ . "/components/navbar.php"; ?>

    <main class="page-content">
        <section class="ops-header">
            <div>
                <p class="layout-kicker">Production Request</p>
                <h1>ใบสั่งผลิตและเบิกสินค้า</h1>
            </div>
            <div class="live-pill"><span></span>RID linked workflow</div>
        </section>

        <section class="product-order-grid">
            <div class="main-card">
                <div class="ops-card-head">
                    <div class="card-section-title">
                        <svg width="18" height="18" fill="none" stroke="var(--primary)" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        เปิดคำสั่งผลิต
                    </div>
                </div>

                <form class="product-order-form" id="productOrderForm" onsubmit="submitProductOrder(event)">
                    <div class="form-group">
                        <label>ชื่อสินค้าที่จะผลิต</label>
                        <select id="productName" required>
                            <option value="">-- เลือกสินค้า --</option>
                            <option value="กล่อง A">กล่อง A</option>
                            <option value="ลัง B">ลัง B</option>
                            <option value="พาเลต C">พาเลต C</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>วัสดุที่ใช้ผลิต</label>
                        <select id="materialName" required onchange="handleMaterialChange()">
                            <option value="">กำลังโหลดวัสดุ...</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>จำนวน</label>
                        <input type="number" id="quantity" min="1" placeholder="ระบุจำนวนที่ต้องการผลิต" required oninput="updateOrderSummary()">
                    </div>

                    <div class="form-group">
                        <label>ตำแหน่งสินค้าที่จะเบิก (LID)</label>
                        <select id="locationId" required disabled onchange="updateOrderSummary()">
                            <option value="">เลือกวัสดุก่อน</option>
                        </select>
                    </div>

                    <div class="form-grid form-grid-compact">
                        <div class="form-group">
                            <label>วันที่สั่งผลิต</label>
                            <input type="date" id="orderDate" required onchange="refreshRidPreview()">
                        </div>
                        <div class="form-group">
                            <label>เวลาที่สั่ง</label>
                            <input type="time" id="orderTime" required>
                        </div>
                    </div>

                    <div class="product-order-rid">
                        <span>RID</span>
                        <strong id="ridPreview">PD-----------</strong>
                    </div>

                    <div class="modal-footer product-order-actions">
                        <button type="button" class="btn btn-cancel" onclick="resetProductOrderForm()">ล้างข้อมูล</button>
                        <button type="submit" class="btn btn-primary btn-new-order" id="submitOrderBtn">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                            บันทึก Product Order
                        </button>
                    </div>
                </form>
            </div>

            <aside class="main-card product-order-summary">
                <div class="ops-card-head">
                    <div class="card-section-title">สรุปคำสั่ง</div>
                </div>
                <div class="po-summary-body">
                    <div class="po-summary-row"><span>สินค้า</span><strong id="summaryProduct">-</strong></div>
                    <div class="po-summary-row"><span>วัสดุ</span><strong id="summaryMaterial">-</strong></div>
                    <div class="po-summary-row"><span>LID</span><strong id="summaryLocation">-</strong></div>
                    <div class="po-summary-row"><span>จำนวน</span><strong id="summaryQuantity">0</strong></div>
                    <div class="po-summary-note" id="summaryAvailability">เลือกวัสดุเพื่อดูตำแหน่งจัดเก็บ</div>
                </div>
            </aside>
        </section>
    </main>
</div>

<div id="toastAlert" class="toast"></div>

<script>
    const API_URL = '../api/product_order.php';
    let materials = [];
    let materialLocations = [];
    let sidebarCollapsed = false;

    document.addEventListener('DOMContentLoaded', () => {
        setupDefaults();
        setupMobileDetect();
        loadMaterials();
        refreshRidPreview();
        window.LiveUpdates?.on('inventory.changed', () => loadMaterials());
    });

    function setupDefaults() {
        const now = new Date();
        document.getElementById('orderDate').value = now.toISOString().slice(0, 10);
        document.getElementById('orderTime').value = now.toTimeString().slice(0, 5);
    }

    function loadMaterials() {
        fetch(`${API_URL}?action=materials`)
            .then(r => r.json())
            .then(res => {
                if (res.status !== 'success') throw new Error(res.message || 'Load failed');
                materials = res.data;
                renderMaterials();
            })
            .catch(err => showToast(err.message, 'error'));
    }

    function renderMaterials() {
        const selected = document.getElementById('materialName').value;
        const options = ['<option value="">-- เลือกวัสดุ --</option>'].concat(materials.map(item => {
            const disabled = item.disabled ? 'disabled class="option-disabled"' : '';
            const suffix = item.disabled ? ' (หมด)' : ` (${Number(item.total_quantity).toLocaleString()} ชิ้น)`;
            return `<option value="${escAttr(item.product_name)}" ${disabled}>${escHtml(item.product_name)}${suffix}</option>`;
        }));
        document.getElementById('materialName').innerHTML = options.join('');
        if (selected) document.getElementById('materialName').value = selected;
    }

    function handleMaterialChange() {
        const material = document.getElementById('materialName').value;
        const locationSelect = document.getElementById('locationId');
        materialLocations = [];
        locationSelect.disabled = true;
        locationSelect.innerHTML = '<option value="">กำลังโหลด LID...</option>';
        updateOrderSummary();

        if (!material) {
            locationSelect.innerHTML = '<option value="">เลือกวัสดุก่อน</option>';
            return;
        }

        fetch(`${API_URL}?action=locations&material=${encodeURIComponent(material)}`)
            .then(r => r.json())
            .then(res => {
                if (res.status !== 'success') throw new Error(res.message || 'Load failed');
                materialLocations = res.data;
                renderLocations();
            })
            .catch(err => {
                locationSelect.innerHTML = '<option value="">โหลด LID ไม่สำเร็จ</option>';
                showToast(err.message, 'error');
            });
    }

    function renderLocations() {
        const locationSelect = document.getElementById('locationId');
        if (!materialLocations.length) {
            locationSelect.disabled = true;
            locationSelect.innerHTML = '<option value="">ไม่มี LID ที่มีสต็อก</option>';
            updateOrderSummary();
            return;
        }

        locationSelect.disabled = false;
        locationSelect.innerHTML = '<option value="">-- เลือก LID --</option>' + materialLocations.map(item => `
            <option value="${escAttr(item.location_id)}" data-id="${Number(item.id)}" data-qty="${Number(item.quantity)}">
                ${escHtml(item.location_id)} · ${Number(item.quantity).toLocaleString()} ชิ้น
            </option>
        `).join('');
        updateOrderSummary();
    }

    function refreshRidPreview() {
        const date = document.getElementById('orderDate').value;
        if (!date) return;
        fetch(`${API_URL}?action=rid&date=${encodeURIComponent(date)}`)
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') {
                    document.getElementById('ridPreview').textContent = res.rid;
                }
            });
    }

    function submitProductOrder(e) {
        e.preventDefault();
        const locationSelect = document.getElementById('locationId');
        const selectedOption = locationSelect.options[locationSelect.selectedIndex];
        const payload = {
            productName: document.getElementById('productName').value,
            materialName: document.getElementById('materialName').value,
            quantity: Number(document.getElementById('quantity').value),
            materialInventoryId: Number(selectedOption?.dataset.id || 0),
            locationId: locationSelect.value,
            orderDate: document.getElementById('orderDate').value,
            orderTime: document.getElementById('orderTime').value
        };

        const submitBtn = document.getElementById('submitOrderBtn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'กำลังบันทึก...';

        fetch(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(r => r.json())
        .then(res => {
            if (res.status !== 'success') throw new Error(res.message || 'Save failed');
            showToast(`เปิดคำสั่ง ${res.rid} สำเร็จ`, 'success');
            resetProductOrderForm();
            loadMaterials();
        })
        .catch(err => showToast(err.message, 'error'))
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg> บันทึก Product Order';
        });
    }

    function resetProductOrderForm() {
        document.getElementById('productOrderForm').reset();
        setupDefaults();
        document.getElementById('locationId').disabled = true;
        document.getElementById('locationId').innerHTML = '<option value="">เลือกวัสดุก่อน</option>';
        materialLocations = [];
        refreshRidPreview();
        updateOrderSummary();
    }

    function updateOrderSummary() {
        const selectedOption = document.getElementById('locationId').options[document.getElementById('locationId').selectedIndex];
        document.getElementById('summaryProduct').textContent = document.getElementById('productName').value || '-';
        document.getElementById('summaryMaterial').textContent = document.getElementById('materialName').value || '-';
        document.getElementById('summaryLocation').textContent = document.getElementById('locationId').value || '-';
        document.getElementById('summaryQuantity').textContent = Number(document.getElementById('quantity').value || 0).toLocaleString();
        document.getElementById('summaryAvailability').textContent = selectedOption?.dataset.qty
            ? `คงเหลือที่ LID นี้ ${Number(selectedOption.dataset.qty).toLocaleString()} ชิ้น`
            : 'เลือกวัสดุเพื่อดูตำแหน่งจัดเก็บ';
    }

    function toggleSidebar() {
        sidebarCollapsed = !sidebarCollapsed;
        document.getElementById('sidebar').classList.toggle('collapsed', sidebarCollapsed);
        document.getElementById('mainWrapper').classList.toggle('collapsed', sidebarCollapsed);
        document.getElementById('sidebarChevron').style.transform = sidebarCollapsed ? 'rotate(180deg)' : '';
    }
    function openMobileSidebar() { document.getElementById('sidebar').classList.add('mobile-open'); document.getElementById('mobileOverlay').style.display = 'block'; }
    function closeMobileSidebar() { document.getElementById('sidebar').classList.remove('mobile-open'); document.getElementById('mobileOverlay').style.display = 'none'; }
    function setupMobileDetect() {
        const mq = window.matchMedia('(max-width: 768px)');
        const update = e => { document.getElementById('mobileMenuBtn').style.display = e.matches ? 'flex' : 'none'; };
        mq.addEventListener('change', update);
        update(mq);
    }
    function showComingSoon(name) { showToast(`${name} - เร็วๆ นี้`, 'warning'); }
    function showToast(msg, type = 'success') {
        const el = document.getElementById('toastAlert');
        el.className = `toast toast-${type} show`;
        el.textContent = msg;
        clearTimeout(el._timer);
        el._timer = setTimeout(() => el.classList.remove('show'), 3800);
    }
    function escHtml(s) { return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
    function escAttr(s) { return escHtml(s).replace(/'/g, '&#39;'); }
</script>
<script src="assets/js/live-updates.js"></script>
</body>
</html>
