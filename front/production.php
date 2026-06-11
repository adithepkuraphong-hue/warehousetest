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
                <div><div class="stat-label">In Progress</div><div class="stat-value" id="progressCount">0</div></div>
            </div>
            <div class="stat-card stat-empty">
                <div class="stat-icon"><svg width="19" height="19" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg></div>
                <div><div class="stat-label">Completed</div><div class="stat-value" id="completedCount">0</div></div>
            </div>
        </section>

        <section class="machine-board" id="machineBoard">
            <div class="state-container">
                <div style="margin-bottom:12px"><div class="loading-dots"><span></span><span></span><span></span></div></div>
                <p>กำลังโหลดรายการผลิต...</p>
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
                <select id="completeDestination">
                    <option value="FP Warehouse">FP Warehouse</option>
                    <option value="Printer">Printer</option>
                    <option value="Cutter">Cutter</option>
                </select>
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

    document.addEventListener('DOMContentLoaded', () => {
        loadProduction();
        setupMobileDetect();
        setInterval(loadProduction, 8000);
    });

    function loadProduction() {
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
        document.getElementById('pendingCount').textContent = orders.filter(o => o.status === 'รอผลิต').length;
        document.getElementById('progressCount').textContent = orders.filter(o => o.status === 'กำลังผลิต').length;
        document.getElementById('completedCount').textContent = orders.filter(o => o.status === 'เสร็จสิ้น').length;
    }

    function renderMachineBoard() {
        document.getElementById('machineBoard').innerHTML = machines.map(machine => {
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
                    ${order.status === 'กำลังผลิต' ? `<button class="btn-row btn-row-edit" onclick="openCompleteModal(${Number(order.id)})">เสร็จสิ้น</button>` : ''}
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
        fetch(API_URL, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id: Number(completingOrder.id),
                action: 'complete',
                destination: document.getElementById('completeDestination').value
            })
        })
        .then(r => r.json())
        .then(res => {
            showToast(res.status === 'success' ? 'ปิดงานผลิตสำเร็จ' : res.message, res.status === 'success' ? 'success' : 'error');
            closeCompleteModal();
            loadProduction();
        })
        .catch(err => showToast(err.message, 'error'));
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
</body>
</html>
