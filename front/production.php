<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Production</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php
$activePage = 'production';
$pageTitle = 'Production';
$showInventoryToolbar = false;
?>

<div class="mobile-overlay" id="mobileOverlay" onclick="closeMobileSidebar()"></div>
<?php include __DIR__ . "/components/sidebar.php"; ?>

<div class="main-wrapper" id="mainWrapper">
    <?php include __DIR__ . "/components/navbar.php"; ?>

    <main class="page-content">
        <section class="ops-header">
            <div>
                <p class="layout-kicker">Production Control</p>
                <h1>จัดการใบสั่งผลิต</h1>
            </div>
            <button class="btn btn-ghost" onclick="loadProduction()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/><path d="M23 4v6h-6"/></svg>
                Refresh
            </button>
        </section>

        <section class="stats-row">
            <div class="stat-card stat-all">
                <div class="stat-icon"><svg width="19" height="19" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg></div>
                <div><div class="stat-label">Pending</div><div class="stat-value" id="pendingCount">0</div></div>
            </div>
            <div class="stat-card stat-keep">
                <div class="stat-icon"><svg width="19" height="19" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg></div>
                <div>
                    <div class="stat-label">In Progress</div>
                    <div class="stat-value" id="progressCount">0</div>
                    <div class="stat-subline" id="progressMachines">No machine active</div>
                </div>
            </div>
            <div class="stat-card stat-empty">
                <div class="stat-icon"><svg width="19" height="19" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg></div>
                <div><div class="stat-label">Completed</div><div class="stat-value" id="completedCount">0</div></div>
            </div>
        </section>

        <section class="machine-picker">
            <div>
                <label for="machineSelector">เลือกเครื่องจักร</label>
                <select id="machineSelector" onchange="selectMachine(this.value)">
                    <option value="">-- เลือก Printer หรือ Cutter --</option>
                    <option value="Printer">Printer</option>
                    <option value="Cutter">Cutter</option>
                </select>
            </div>
            <div class="machine-hint" id="machineHint">เลือกเครื่องเพื่อแสดงหน้าจอรับงานและฟิลด์ที่เกี่ยวข้อง</div>
        </section>

        <section class="machine-board machine-board-single" id="machineBoard">
            <div class="state-container">
                <p>กรุณาเลือกเครื่องจักรก่อนเริ่มงาน</p>
            </div>
        </section>
    </main>
</div>

<div id="completeModal" class="modal">
    <div class="modal-content" style="max-width:520px;">
        <div class="modal-header">
            <h3 id="completeTitle">Complete Production</h3>
            <button class="close-modal" onclick="closeCompleteModal()">×</button>
        </div>
        <div class="modal-body">
            <div class="dispatch-info-grid" id="completeInfo"></div>
            <div class="form-group" style="margin-bottom:0;">
                <label>ส่งชิ้นงานต่อไปที่</label>
                <select id="completeDestination" onchange="toggleFpLocationFields()">
                    <option value="FP Warehouse">FP Warehouse</option>
                    <option value="Printer">Printer</option>
                    <option value="Cutter">Cutter</option>
                </select>
            </div>
            <div class="fp-location-panel" id="fpLocationPanel">
                <div class="form-section-title" style="margin-top:18px;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><circle cx="12" cy="11" r="3"/></svg>
                    ตำแหน่งจัดเก็บ FP Warehouse
                </div>
                <div class="form-grid form-grid-compact">
                    <div class="form-group">
                        <label>อาคารคลังสินค้า</label>
                        <select id="fpWarehouseCode" onchange="generateFpLocationId()">
                            <option value="A">คลังสินค้า A</option>
                            <option value="B">คลังสินค้า B</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>โซน (Zone)</label>
                        <select id="fpRowLocation" onchange="generateFpLocationId()">
                            <option value="A">โซน A</option>
                            <option value="B">โซน B</option>
                            <option value="C">โซน C</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>แถว (Row)</label>
                        <select id="fpColumnLocation" onchange="generateFpLocationId()">
                            <option value="1">แถว 1</option>
                            <option value="2">แถว 2</option>
                            <option value="3">แถว 3</option>
                            <option value="4">แถว 4</option>
                            <option value="5">แถว 5</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>ชั้นความสูง (Level)</label>
                        <select id="fpLevel" onchange="generateFpLocationId()">
                            <option value="0">ชั้น 0 (พื้นดิน)</option>
                            <option value="1">ชั้น 1</option>
                            <option value="2">ชั้น 2</option>
                            <option value="3">ชั้น 3</option>
                        </select>
                    </div>
                    <div class="form-group fp-location-id-field">
                        <label>รหัสพิกัด (Location ID)</label>
                        <input type="text" id="fpLocationId" readonly>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-cancel" onclick="closeCompleteModal()">ยกเลิก</button>
            <button class="btn btn-success" onclick="confirmComplete()">เสร็จสิ้น</button>
        </div>
    </div>
</div>

<div id="toastAlert" class="toast"></div>

<script>
    const API_URL = '/testapi/api/production.php';
    const machines = ['Printer', 'Cutter'];
    let orders = [];
    let completingOrder = null;
    let sidebarCollapsed = false;
    let selectedMachine = '';

    document.addEventListener('DOMContentLoaded', () => {
        loadProduction();
        setupMobileDetect();
        window.LiveUpdates?.on('production.changed', () => loadProduction({ silent: true }));
        setInterval(() => loadProduction({ silent: true }), 30000);
    });

    function loadProduction(options = {}) {
        fetch(API_URL)
            .then(r => r.json())
            .then(res => {
                if (res.status !== 'success') throw new Error(res.message || 'Load failed');
                orders = res.data;
                updateStats();
                renderMachineBoard();
            })
            .catch(err => {
                document.getElementById('machineBoard').innerHTML = `<div class="state-container"><div class="state-icon">!</div><p>${escHtml(err.message)}</p></div>`;
            });
    }

    function updateStats() {
        const inProgress = orders.filter(o => o.status === 'กำลังผลิต');
        const activeMachines = [...new Set(inProgress.map(o => o.machine_type))];
        document.getElementById('pendingCount').textContent = orders.filter(o => o.status === 'รอผลิต').length;
        document.getElementById('progressCount').textContent = inProgress.length;
        document.getElementById('completedCount').textContent = orders.filter(o => o.status === 'เสร็จสิ้น').length;
        document.getElementById('progressMachines').textContent = activeMachines.length ? activeMachines.join(', ') : 'No machine active';
    }

    function renderMachineBoard() {
        if (!selectedMachine) {
            document.getElementById('machineBoard').innerHTML = '<div class="state-container"><p>กรุณาเลือกเครื่องจักรก่อนเริ่มงาน</p></div>';
            return;
        }

        document.getElementById('machineBoard').innerHTML = [selectedMachine].map(machine => {
            const machineOrders = orders.filter(o => o.machine_type === machine && o.status !== 'เสร็จสิ้น');
            return `
                <article class="machine-column">
                    <div class="machine-head">
                        <div><span>Machine</span><strong>${machine}</strong></div>
                        <b>${machineOrders.length} PR</b>
                    </div>
                    <div class="order-stack">
                        ${machineOrders.length ? machineOrders.map(renderOrderCard).join('') : '<div class="empty-panel">ไม่มี PR รอผลิต</div>'}
                    </div>
                </article>
            `;
        }).join('');
    }

    function selectMachine(machine) {
        selectedMachine = machine;
        const hint = document.getElementById('machineHint');
        hint.textContent = machine
            ? `${machine} พร้อมแสดงงานรอผลิตและงานที่กำลังทำอยู่`
            : 'เลือกเครื่องเพื่อแสดงหน้าจอรับงานและฟิลด์ที่เกี่ยวข้อง';
        renderMachineBoard();
    }

    function renderOrderCard(order) {
        const badge = order.status === 'กำลังผลิต' ? 'badge-warning' : 'badge-success';
        return `
            <div class="pr-card">
                <div class="pr-top">
                    <strong>${escHtml(order.pr_no)}</strong>
                    <span class="badge ${badge}">${escHtml(order.status)}</span>
                </div>
                <div class="pr-name">${escHtml(order.source_product_name)}</div>
                <div class="pr-meta">
                    <span>${escHtml(order.source_product_id)}</span>
                    <span>${Number(order.quantity).toLocaleString()} ชิ้น</span>
                </div>
                <div class="pr-actions">
                    ${order.status === 'รอผลิต' ? `<button class="btn-row btn-row-dispatch" onclick="claimOrder(${Number(order.id)})">รับการผลิต</button>` : ''}
                    ${order.status === 'กำลังผลิต' ? `<button class="btn-row btn-row-delete" onclick="cancelOrder(${Number(order.id)})">ยกเลิกการรับงาน</button><button class="btn-row btn-row-edit" onclick="openCompleteModal(${Number(order.id)})">เสร็จสิ้น</button>` : ''}
                </div>
            </div>
        `;
    }

    function claimOrder(id) {
        fetch(API_URL, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, action: 'claim' })
        })
        .then(r => r.json())
        .then(res => {
            showToast(res.status === 'success' ? 'รับการผลิตแล้ว' : res.message, res.status === 'success' ? 'success' : 'error');
            loadProduction();
        })
        .catch(err => showToast(err.message, 'error'));
    }

    function cancelOrder(id) {
        fetch(API_URL, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, action: 'cancel' })
        })
        .then(r => r.json())
        .then(res => {
            showToast(res.status === 'success' ? 'ยกเลิกการรับงานแล้ว' : res.message, res.status === 'success' ? 'success' : 'error');
            loadProduction();
        })
        .catch(err => showToast(err.message, 'error'));
    }

    function openCompleteModal(id) {
        completingOrder = orders.find(o => Number(o.id) === Number(id));
        if (!completingOrder) return;
        document.getElementById('completeTitle').textContent = `Complete ${completingOrder.pr_no}`;
        const nextMachine = completingOrder.machine_type === 'Printer' ? 'Cutter' : 'Printer';
        document.getElementById('completeDestination').innerHTML = `
            <option value="FP Warehouse">FP Warehouse</option>
            <option value="${nextMachine}">${nextMachine}</option>
        `;
        document.getElementById('completeDestination').value = 'FP Warehouse';
        toggleFpLocationFields();
        generateFpLocationId();
        document.getElementById('completeInfo').innerHTML = `
            <div class="dispatch-info-item"><div class="di-label">สินค้า</div><div class="di-value">${escHtml(completingOrder.final_product_name)}</div></div>
            <div class="dispatch-info-item"><div class="di-label">จำนวน</div><div class="di-value">${Number(completingOrder.quantity).toLocaleString()} ชิ้น</div></div>
            <div class="dispatch-info-item"><div class="di-label">เครื่องจักร</div><div class="di-value">${escHtml(completingOrder.machine_type)}</div></div>
        `;
        document.getElementById('completeModal').classList.add('open');
    }

    function closeCompleteModal() {
        document.getElementById('completeModal').classList.remove('open');
        completingOrder = null;
    }

    function confirmComplete() {
        if (!completingOrder) return;
        const destination = document.getElementById('completeDestination').value;
        const payload = {
            id: Number(completingOrder.id),
            action: 'complete',
            destination
        };

        if (destination === 'FP Warehouse') {
            payload.fpWarehouseCode = document.getElementById('fpWarehouseCode').value;
            payload.fpRowLocation = document.getElementById('fpRowLocation').value;
            payload.fpColumnLocation = Number(document.getElementById('fpColumnLocation').value);
            payload.fpLevel = Number(document.getElementById('fpLevel').value);
        }

        fetch(API_URL, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(r => r.json())
        .then(res => {
            showToast(res.status === 'success' ? 'ปิดงานผลิตสำเร็จ' : res.message, res.status === 'success' ? 'success' : 'error');
            closeCompleteModal();
            loadProduction();
        })
        .catch(err => showToast(err.message, 'error'));
    }

    function toggleFpLocationFields() {
        const show = document.getElementById('completeDestination').value === 'FP Warehouse';
        document.getElementById('fpLocationPanel').style.display = show ? 'block' : 'none';
    }

    function generateFpLocationId() {
        const wh = document.getElementById('fpWarehouseCode').value;
        const zone = document.getElementById('fpRowLocation').value;
        const row = document.getElementById('fpColumnLocation').value;
        const level = document.getElementById('fpLevel').value;
        document.getElementById('fpLocationId').value = `${wh}${zone}-${row}-${level}`;
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
    window.addEventListener('click', e => { if (e.target.id === 'completeModal') closeCompleteModal(); });
    function escHtml(s) { return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
</script>
<script src="assets/js/live-updates.js"></script>
</body>
</html>
