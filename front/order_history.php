<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php
$activePage = 'history';
$pageTitle = 'Order History';
$showInventoryToolbar = false;
?>

<div class="mobile-overlay" id="mobileOverlay" onclick="closeMobileSidebar()"></div>
<?php include __DIR__ . "/components/sidebar.php"; ?>

<div class="main-wrapper" id="mainWrapper">
    <?php include __DIR__ . "/components/navbar.php"; ?>

    <main class="page-content">
        <section class="ops-header">
            <div>
                <p class="layout-kicker">Live Log</p>
                <h1>ประวัติการทำรายการ</h1>
            </div>
            <div class="live-pill"><span></span>Live update</div>
        </section>

        <section class="history-filters">
            <div class="form-group">
                <label>วันที่เริ่มต้น</label>
                <input type="date" id="filterDateFrom" onchange="loadAllHistory()">
            </div>
            <div class="form-group">
                <label>วันที่สิ้นสุด</label>
                <input type="date" id="filterDateTo" onchange="loadAllHistory()">
            </div>
            <div class="form-group">
                <label>ประเภทรายการ</label>
                <select id="filterLogType" onchange="loadAllHistory()">
                    <option value="">ทั้งหมด</option>
                    <option value="Inbound">Inbound</option>
                    <option value="Outbound">Outbound</option>
                </select>
            </div>
            <div class="history-filter-actions">
                <button class="btn btn-primary" onclick="loadAllHistory()">กรองข้อมูล</button>
                <button class="btn btn-ghost" onclick="clearHistoryFilters()">ล้างตัวกรอง</button>
            </div>
        </section>

        <section class="ops-grid history-grid">
            <div class="main-card">
                <div class="ops-card-head">
                    <div class="card-section-title">Inbound Log</div>
                    <button class="btn-row btn-row-edit" onclick="loadHistory('Inbound')">Refresh</button>
                </div>
                <div class="log-list" id="inboundLog"></div>
            </div>
            <div class="main-card">
                <div class="ops-card-head">
                    <div class="card-section-title">Outbound / Dispatch Log</div>
                    <button class="btn-row btn-row-edit" onclick="loadHistory('Outbound')">Refresh</button>
                </div>
                <div class="log-list" id="outboundLog"></div>
            </div>
        </section>
    </main>
</div>

<div id="toastAlert" class="toast"></div>

<script>
    const API_URL = '/testapi/api/order_history.php';
    let sidebarCollapsed = false;
    let latestIds = { Inbound: 0, Outbound: 0 };

    document.addEventListener('DOMContentLoaded', () => {
        loadAllHistory();
        setupMobileDetect();
        window.LiveUpdates?.on('history.changed', () => loadAllHistory());
        setInterval(loadAllHistory, 30000);
    });

    function loadHistory(type) {
        const params = getHistoryParams(type);
        fetch(`${API_URL}?${params.toString()}`)
            .then(r => r.json())
            .then(res => {
                if (res.status !== 'success') throw new Error(res.message || 'Load failed');
                renderLog(type, res.data);
            })
            .catch(err => showToast(err.message, 'error'));
    }

    function loadAllHistory() {
        const selectedType = document.getElementById('filterLogType').value;
        if (!selectedType || selectedType === 'Inbound') {
            loadHistory('Inbound');
        } else {
            renderLog('Inbound', []);
        }
        if (!selectedType || selectedType === 'Outbound') {
            loadHistory('Outbound');
        } else {
            renderLog('Outbound', []);
        }
    }

    function getHistoryParams(type) {
        const params = new URLSearchParams({
            type,
            limit: '80'
        });
        const dateFrom = document.getElementById('filterDateFrom').value;
        const dateTo = document.getElementById('filterDateTo').value;
        if (dateFrom) params.set('date_from', dateFrom);
        if (dateTo) params.set('date_to', dateTo);
        return params;
    }

    function clearHistoryFilters() {
        document.getElementById('filterDateFrom').value = '';
        document.getElementById('filterDateTo').value = '';
        document.getElementById('filterLogType').value = '';
        loadAllHistory();
    }

    function renderLog(type, rows) {
        const target = type === 'Inbound' ? 'inboundLog' : 'outboundLog';
        const newest = rows.length ? Number(rows[0].id) : 0;
        const hasNew = latestIds[type] && newest > latestIds[type];
        latestIds[type] = Math.max(latestIds[type], newest);

        document.getElementById(target).innerHTML = rows.length ? rows.map(row => `
            <article class="log-item ${hasNew && Number(row.id) === newest ? 'is-new' : ''}">
                <div class="log-main">
                    <div>
                        <strong>${escHtml(row.action)}</strong>
                        <span>${escHtml(row.product_name || '-')} · ${Number(row.quantity || 0).toLocaleString()} ชิ้น</span>
                    </div>
                    <span class="badge ${type === 'Inbound' ? 'badge-success' : 'badge-warning'}">${escHtml(row.log_type)}</span>
                </div>
                <div class="log-route">
                    <span>${escHtml(row.source || '-')}</span>
                    <b>→</b>
                    <span>${escHtml(row.destination || '-')}</span>
                </div>
                <div class="log-foot">
                    <span>${escHtml(row.reference_type || '')} ${escHtml(row.reference_id || '')}</span>
                    <span>${formatDate(row.created_at)}</span>
                </div>
            </article>
        `).join('') : `<div class="state-container"><p>ยังไม่มี ${escHtml(type)} log</p></div>`;
    }

    function formatDate(value) { return new Date(value).toLocaleString('th-TH', { hour12: false }); }
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
</script>
<script src="assets/js/live-updates.js"></script>
</body>
</html>
