<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FG Warehouse</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php
$activePage = 'fg';
$pageTitle = 'FG Warehouse';
$showInventoryToolbar = false;
?>

<div class="mobile-overlay" id="mobileOverlay" onclick="closeMobileSidebar()"></div>
<?php include __DIR__ . "/components/sidebar.php"; ?>

<div class="main-wrapper" id="mainWrapper">
    <?php include __DIR__ . "/components/navbar.php"; ?>

    <main class="page-content">
        <section class="ops-header">
            <div>
                <p class="layout-kicker">Finish Goods</p>
                <h1>คลังสินค้าสำเร็จรูป</h1>
            </div>
            <button class="btn btn-ghost" onclick="loadFGWarehouse()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/><path d="M23 4v6h-6"/></svg>
                Refresh
            </button>
        </section>

        <section class="stats-row">
            <div class="stat-card stat-all">
                <div class="stat-icon"><svg width="19" height="19" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 7l9-4 9 4-9 4-9-4z"/><path d="M3 7v10l9 4 9-4V7"/></svg></div>
                <div><div class="stat-label">FG Lots</div><div class="stat-value" id="fgLots">0</div></div>
            </div>
            <div class="stat-card stat-keep">
                <div class="stat-icon"><svg width="19" height="19" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 19h16M4 15h16M4 11h16M4 7h16"/></svg></div>
                <div><div class="stat-label">FG SKUs</div><div class="stat-value" id="fgSku">0</div></div>
            </div>
            <div class="stat-card stat-empty">
                <div class="stat-icon"><svg width="19" height="19" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 3v18M5 10l7-7 7 7"/></svg></div>
                <div><div class="stat-label">Total Qty</div><div class="stat-value" id="fgQty">0</div></div>
            </div>
        </section>

        <section class="ops-grid">
            <div class="main-card">
                <div class="ops-card-head">
                    <div class="card-section-title">สรุปสินค้าสำเร็จรูป</div>
                </div>
                <div class="table-responsive" id="fgSummaryTable"></div>
            </div>
            <div class="main-card">
                <div class="ops-card-head">
                    <div class="card-section-title">รายการรับเข้าล่าสุด</div>
                </div>
                <div class="table-responsive" id="fgLotTable"></div>
            </div>
        </section>
    </main>
</div>

<div id="toastAlert" class="toast"></div>

<script>
    const API_URL = '/testapi/api/fg_warehouse.php';
    let sidebarCollapsed = false;

    document.addEventListener('DOMContentLoaded', () => {
        loadFGWarehouse();
        setupMobileDetect();
        window.LiveUpdates?.on('fg.changed', () => loadFGWarehouse());
        setInterval(loadFGWarehouse, 30000);
    });

    function loadFGWarehouse() {
        fetch(API_URL)
            .then(r => r.json())
            .then(res => {
                if (res.status !== 'success') throw new Error(res.message || 'Load failed');
                renderFG(res.data, res.summary);
            })
            .catch(err => showToast(err.message, 'error'));
    }

    function renderFG(lots, summary) {
        const totalQty = lots.reduce((sum, item) => sum + Number(item.quantity || 0), 0);
        document.getElementById('fgLots').textContent = lots.length.toLocaleString();
        document.getElementById('fgSku').textContent = summary.length.toLocaleString();
        document.getElementById('fgQty').textContent = totalQty.toLocaleString();

        document.getElementById('fgSummaryTable').innerHTML = summary.length ? `
            <table><thead><tr><th>FG ID</th><th>ชื่อสินค้า</th><th>Lots</th><th>จำนวนรวม</th></tr></thead><tbody>
            ${summary.map(item => `<tr>
                <td><span class="location-tag">${escHtml(item.fg_product_id)}</span></td>
                <td>${escHtml(item.fg_product_name)}</td>
                <td>${Number(item.lots).toLocaleString()}</td>
                <td style="font-weight:800;color:var(--success);">${Number(item.total_quantity).toLocaleString()}</td>
            </tr>`).join('')}
            </tbody></table>
        ` : emptyState('ยังไม่มีสินค้าสำเร็จรูป');

        document.getElementById('fgLotTable').innerHTML = lots.length ? `
            <table><thead><tr><th>PR</th><th>FG</th><th>จำนวน</th><th>Location</th><th>เครื่องต้นทาง</th><th>รับเข้า</th></tr></thead><tbody>
            ${lots.map(item => `<tr>
                <td style="font-weight:700;">${escHtml(item.pr_no)}</td>
                <td>${escHtml(item.fg_product_name)}<br><span class="text-muted">${escHtml(item.fg_product_id)}</span></td>
                <td style="font-weight:800;">${Number(item.quantity).toLocaleString()}</td>
                <td><span class="location-tag">${escHtml(item.location_id || '-')}</span></td>
                <td><span class="badge badge-success">${escHtml(item.source_machine)}</span></td>
                <td class="text-muted">${formatDate(item.received_at)}</td>
            </tr>`).join('')}
            </tbody></table>
        ` : emptyState('ยังไม่มี lot ที่รับเข้า');
    }

    function emptyState(text) { return `<div class="state-container"><p>${escHtml(text)}</p></div>`; }
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
