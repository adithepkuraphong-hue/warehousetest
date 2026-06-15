<?php
$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$appBase = preg_replace('#/front$#', '', $scriptDir);
$frontBase = ($appBase === '' ? '' : $appBase) . '/front';
?>
<!-- ═══════════ SIDEBAR ═══════════ -->
<nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <div class="sidebar-logo-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <span class="sidebar-logo-text">WMS Pro</span>
        </div>
        <button class="sidebar-toggle" onclick="toggleSidebar()" title="พับเมนู">
            <svg id="sidebarChevron" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
        </button>
    </div>

    <div class="sidebar-nav">
        <div class="nav-section-label">เมนูหลัก</div>

        <a class="nav-item <?php echo (($activePage ?? 'inventory') === 'inventory') ? 'active' : ''; ?>" href="<?php echo htmlspecialchars(($appBase === '' ? '' : $appBase) . '/index.php', ENT_QUOTES, 'UTF-8'); ?>">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <span class="nav-item-text">คลังสินค้า</span>
        </a>

        <a class="nav-item <?php echo (($activePage ?? '') === 'production') ? 'active' : ''; ?>" href="<?php echo htmlspecialchars($frontBase . '/production.php', ENT_QUOTES, 'UTF-8'); ?>">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 010 1.4l-7 7a1 1 0 01-1.4-1.4l7-7a1 1 0 011.4 0z"/><path d="M12 8l4 4m-9 4l1 4 4-1m6-16l3 3-4 4-3-3 4-4z"/></svg>
            <span class="nav-item-text">Production</span>
        </a>

        <a class="nav-item <?php echo (($activePage ?? '') === 'product_order') ? 'active' : ''; ?>" href="<?php echo htmlspecialchars($frontBase . '/product_order.php', ENT_QUOTES, 'UTF-8'); ?>">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/><path d="M17 3v6"/></svg>
            <span class="nav-item-text">เปิด Product Order</span>
        </a>

        <a class="nav-item <?php echo (($activePage ?? '') === 'fg') ? 'active' : ''; ?>" href="<?php echo htmlspecialchars($frontBase . '/fg_warehouse.php', ENT_QUOTES, 'UTF-8'); ?>">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 7l9-4 9 4-9 4-9-4z"/><path d="M3 7v10l9 4 9-4V7"/><path d="M12 11v10"/></svg>
            <span class="nav-item-text">FG Warehouse</span>
        </a>

        <a class="nav-item <?php echo (($activePage ?? '') === 'history') ? 'active' : ''; ?>" href="<?php echo htmlspecialchars($frontBase . '/order_history.php', ENT_QUOTES, 'UTF-8'); ?>">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span class="nav-item-text">Order History</span>
        </a>

        <a class="nav-item <?php echo (($activePage ?? '') === 'layout') ? 'active' : ''; ?>" href="<?php echo htmlspecialchars($frontBase . '/warehouse_layout.php', ENT_QUOTES, 'UTF-8'); ?>">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
            <span class="nav-item-text">Warehouse Layout</span>
        </a>

        <a class="nav-item <?php echo (($activePage ?? '') === 'fg_layout') ? 'active' : ''; ?>" href="<?php echo htmlspecialchars($frontBase . '/fg_warehouse_layout.php', ENT_QUOTES, 'UTF-8'); ?>">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 3h18v7H3z"/><path d="M3 14h8v7H3zM15 14h6v7h-6z"/></svg>
            <span class="nav-item-text">FG Layout</span>
        </a>

        <div class="nav-section-label" style="margin-top:8px;">ระบบ</div>

        <a class="nav-item" href="#" onclick="showComingSoon('รายงาน'); return false;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span class="nav-item-text">รายงาน</span>
        </a>

        <a class="nav-item" href="#" onclick="showComingSoon('การตั้งค่า'); return false;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
            <span class="nav-item-text">การตั้งค่า</span>
        </a>
    </div>

    <div class="sidebar-footer">
        <div class="nav-item" style="cursor:default; pointer-events:none;">
            <div style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#818cf8);display:flex;align-items:center;justify-content:center;color:white;font-size:0.75rem;font-weight:700;flex-shrink:0;">WM</div>
            <div class="nav-item-text" style="line-height:1.2;">
                <div style="font-size:0.8rem;font-weight:600;color:var(--text-1);">Warehouse Mgr</div>
                <div style="font-size:0.72rem;color:var(--text-3);">admin</div>
            </div>
        </div>
    </div>
</nav>
