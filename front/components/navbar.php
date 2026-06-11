<!-- TOP NAV -->
<header class="topnav">
    <!-- Mobile hamburger -->
    <button class="btn-icon" id="mobileMenuBtn" onclick="openMobileSidebar()" style="display:none;">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>

    <span class="topnav-page-title">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline;vertical-align:-2px;margin-right:6px;color:var(--primary)"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        <?php echo htmlspecialchars($pageTitle ?? 'คลังสินค้า', ENT_QUOTES, 'UTF-8'); ?>
    </span>

    <?php if (($showInventoryToolbar ?? true) === true): ?>
    <!-- SEARCH BAR -->
    <div class="search-wrap">
        <span class="search-icon">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        </span>
        <input
            type="text"
            class="search-input"
            id="searchInput"
            placeholder="ค้นหาสินค้า, ID, Location..."
            oninput="handleSearch()"
            onfocus="showFilterBar()"
        >
    </div>

    <!-- Filter toggle button -->
    <button class="btn btn-ghost" onclick="toggleFilterBar()" id="filterToggleBtn" style="padding:8px 12px;">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 4h18M7 12h10M11 20h2"/></svg>
        <span style="font-size:0.82rem;">Filter</span>
    </button>

    <!--  Fix Refresh — pure SVG, no image render issue -->
    <button class="btn-icon" onclick="loadInventory()">
        <svg class="refresh-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="23 4 23 10 17 10"/>
            <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
        </svg>
    </button>

    <div style="margin-left:auto; display:flex; gap:8px; align-items:center;">
        <button class="btn btn-primary" onclick="openAddModal()">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"/></svg>
            New Order
        </button>
        <button class="btn btn-warning" id="toggleEditBtn" onclick="toggleEditMode()">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Edit
        </button>
    </div>
    <?php endif; ?>
</header>

<?php if (($showInventoryToolbar ?? true) === true): ?>
<!-- FILTER BAR -->
<div class="filter-bar hidden" id="filterBar">
    <span class="filter-label">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="display:inline;vertical-align:-1px;"><path d="M3 4h18M7 12h10M11 20h2"/></svg>
        กรอง:
    </span>

    <select class="filter-select" id="filterStatus" onchange="applyFilters()">
        <option value="">สถานะ: ทั้งหมด</option>
        <option value="Keep">Keep (เก็บไว้)</option>
        <option value="Empty">Empty (หมด)</option>
    </select>

    <select class="filter-select" id="filterWarehouse" onchange="applyFilters()">
        <option value="">คลัง: ทั้งหมด</option>
        <option value="A">คลัง A</option>
        <option value="B">คลัง B</option>
    </select>

    <select class="filter-select" id="filterDate" onchange="applyFilters()">
        <option value="">วันที่: ทั้งหมด</option>
        <option value="today">วันนี้</option>
        <option value="week">7 วันล่าสุด</option>
        <option value="month">30 วันล่าสุด</option>
    </select>

    <button class="filter-clear-btn" onclick="clearFilters()">
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
        ล้างตัวกรอง
    </button>

    <span class="filter-results-badge" id="filterResultsBadge">แสดงทั้งหมด</span>
</div>
<?php endif; ?>
