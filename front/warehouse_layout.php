<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warehouse Layout Overview</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php
$activePage = 'layout';
$pageTitle = 'Warehouse Layout';
$showInventoryToolbar = false;
?>

<div class="mobile-overlay" id="mobileOverlay" onclick="closeMobileSidebar()"></div>

<?php include __DIR__ . "/components/sidebar.php"; ?>

<div class="main-wrapper" id="mainWrapper">
    <?php include __DIR__ . "/components/navbar.php"; ?>

    <main class="page-content layout-page">
        <section class="layout-summary">
            <div>
                <p class="layout-kicker">Overview</p>
                <h1>ภาพรวมตำแหน่งสินค้าในโกดัง</h1>
            </div>
            <div class="layout-actions">
                <button class="btn btn-ghost" onclick="loadLayoutData()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="23 4 23 10 17 10"/>
                        <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                    </svg>
                    Refresh
                </button>
            </div>
        </section>

        <section class="layout-metrics" aria-label="Warehouse summary">
            <div class="layout-metric">
                <span>สินค้าทั้งหมด</span>
                <strong id="layoutTotalItems">0</strong>
            </div>
            <div class="layout-metric">
                <span>ตำแหน่งที่มีสินค้า</span>
                <strong id="layoutOccupiedCells">0</strong>
            </div>
            <div class="layout-metric">
                <span>จำนวนรวม</span>
                <strong id="layoutTotalQty">0</strong>
            </div>
            <div class="layout-legend">
                <span><i class="legend-dot legend-a"></i>Warehouse A</span>
                <span><i class="legend-dot legend-b"></i>Warehouse B</span>
            </div>
        </section>

        <section class="warehouse-overview" id="warehouseOverview">
            <div class="state-container">
                <div style="margin-bottom:12px"><div class="loading-dots"><span></span><span></span><span></span></div></div>
                <p>กำลังโหลดแผนผังโกดัง...</p>
            </div>
        </section>
    </main>
</div>

<div id="layoutModal" class="modal">
    <div class="modal-content layout-modal-content">
        <div class="modal-header">
            <h3 id="layoutModalTitle">Location</h3>
            <button class="close-modal" onclick="closeLayoutModal()">x</button>
        </div>
        <div class="modal-body">
            <div class="level-list" id="levelList"></div>
        </div>
    </div>
</div>

<div id="toastAlert" class="toast"></div>

<script>
    const API_URL = '/testapi/api/inventory.php';
    const warehouses = ['A', 'B'];
    const zones = ['A', 'B', 'C'];
    const rows = [1, 2, 3, 4, 5];
    const levels = [0, 1, 2, 3];
    let layoutItems = [];
    let sidebarCollapsed = false;

    document.addEventListener('DOMContentLoaded', () => {
        loadLayoutData();
        setupMobileDetect();
    });

    function loadLayoutData() {
        const overview = document.getElementById('warehouseOverview');
        overview.innerHTML = `<div class="state-container"><div style="margin-bottom:12px"><div class="loading-dots"><span></span><span></span><span></span></div></div><p>กำลังโหลดแผนผังโกดัง...</p></div>`;

        fetch(API_URL, { method: 'GET' })
            .then(r => r.json())
            .then(res => {
                if (res.status !== 'success') throw new Error(res.message || 'Load failed');
                layoutItems = res.data.filter(item => ['A', 'B'].includes(String(item.warehouse || '').toUpperCase()));
                updateLayoutMetrics();
                renderLayout();
            })
            .catch(err => {
                overview.innerHTML = `<div class="state-container"><div class="state-icon">!</div><p>โหลดข้อมูล Layout ไม่สำเร็จ: ${escHtml(err.message)}</p></div>`;
            });
    }

    function renderLayout() {
        const overview = document.getElementById('warehouseOverview');
        overview.innerHTML = warehouses.map(warehouse => {
            const totalQty = getWarehouseItems(warehouse).reduce((sum, item) => sum + Number(item.quantity || 0), 0);
            return `
                <article class="warehouse-block warehouse-${warehouse.toLowerCase()}">
                    <div class="warehouse-block-header">
                        <div>
                            <span>Warehouse</span>
                            <strong>${warehouse}</strong>
                        </div>
                        <small>${totalQty.toLocaleString()} ชิ้น</small>
                    </div>
                    <div class="zone-grid">
                        ${zones.map(zone => `
                            <div class="zone-column zone-${zone.toLowerCase()}">
                                <div class="zone-label">Zone ${zone}</div>
                                ${rows.map(row => renderLocationButton(warehouse, zone, row)).join('')}
                            </div>
                        `).join('')}
                    </div>
                </article>
            `;
        }).join('');
    }

    function renderLocationButton(warehouse, zone, row) {
        const items = getLocationItems(warehouse, zone, row);
        const totalQty = items.reduce((sum, item) => sum + Number(item.quantity || 0), 0);
        const occupiedLevels = new Set(items.map(item => Number(item.level || 0))).size;
        const locationLabel = `${warehouse}${zone}-${row}`;
        const isEmpty = items.length === 0;

        return `
            <button class="location-cell ${isEmpty ? 'is-empty' : 'is-filled'} zone-border-${zone.toLowerCase()}"
                onclick="openLocationModal('${warehouse}', '${zone}', ${row})">
                <span>${locationLabel}</span>
                <small>${occupiedLevels}/4 Level · ${totalQty} ชิ้น</small>
            </button>
        `;
    }

    function openLocationModal(warehouse, zone, row) {
        document.getElementById('layoutModalTitle').textContent = `Warehouse-${warehouse} Zone-${zone} Row-${row} LID: ${warehouse}${zone}-${row}-XX`;
        const list = document.getElementById('levelList');
        list.innerHTML = levels.map(level => renderLevelCard(warehouse, zone, row, level)).join('');
        document.getElementById('layoutModal').classList.add('open');
    }

    function renderLevelCard(warehouse, zone, row, level) {
        const levelItems = getLocationItems(warehouse, zone, row).filter(item => Number(item.level || 0) === level);
        const locationId = `${warehouse}${zone}-${row}-${level}`;
        const products = levelItems.length
            ? levelItems.map(item => `
                <div class="level-product">
                    <div>
                        <strong>${escHtml(item.product_id)}</strong>
                        <span>${escHtml(item.product_name)}</span>
                    </div>
                    <b>${Number(item.quantity || 0).toLocaleString()} ชิ้น</b>
                </div>
            `).join('')
            : `<div class="level-empty">ไม่มีสินค้าในชั้นนี้</div>`;

        return `
            <div class="level-row">
                <div class="level-badge">${locationId}</div>
                <div class="level-detail">
                    ${products}
                </div>
            </div>
        `;
    }

    function closeLayoutModal() {
        document.getElementById('layoutModal').classList.remove('open');
    }

    function updateLayoutMetrics() {
        const occupied = new Set(layoutItems.map(item => `${item.warehouse}${item.row_location}-${item.column_location}`)).size;
        const totalQty = layoutItems.reduce((sum, item) => sum + Number(item.quantity || 0), 0);
        document.getElementById('layoutTotalItems').textContent = layoutItems.length.toLocaleString();
        document.getElementById('layoutOccupiedCells').textContent = occupied.toLocaleString();
        document.getElementById('layoutTotalQty').textContent = totalQty.toLocaleString();
    }

    function getWarehouseItems(warehouse) {
        return layoutItems.filter(item => String(item.warehouse || '').toUpperCase() === warehouse);
    }

    function getLocationItems(warehouse, zone, row) {
        return layoutItems.filter(item =>
            String(item.warehouse || '').toUpperCase() === warehouse &&
            String(item.row_location || '').toUpperCase() === zone &&
            Number(item.column_location || 0) === Number(row)
        );
    }

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

    function showComingSoon(name) {
        showToast(`${name} - เร็วๆ นี้`, 'warning');
    }

    function showToast(msg, type = 'success') {
        const el = document.getElementById('toastAlert');
        el.className = `toast toast-${type} show`;
        el.textContent = msg;
        clearTimeout(el._timer);
        el._timer = setTimeout(() => el.classList.remove('show'), 3800);
    }

    window.addEventListener('click', e => {
        if (e.target.id === 'layoutModal') closeLayoutModal();
    });

    function escHtml(s) {
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
</script>
</body>
</html>
