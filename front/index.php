<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบบริหารคลังสินค้าอัจฉริยะ (Inventory Dashboard)</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap');

        *, *::before, *::after {
            margin: 0; padding: 0; box-sizing: border-box;
        }

        :root {
            --sidebar-w: 220px;
            --sidebar-collapsed: 64px;
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --primary-light: #eef2ff;
            --success: #059669;
            --success-light: #ecfdf5;
            --danger: #e11d48;
            --danger-light: #fff1f2;
            --warning: #d97706;
            --warning-light: #fffbeb;
            --surface: #ffffff;
            --surface-2: #f8fafc;
            --border: #e2e8f0;
            --text-1: #0f172a;
            --text-2: #334155;
            --text-3: #64748b;
            --radius: 12px;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -1px rgba(0,0,0,0.04);
            --shadow-lg: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
        }

        body {
            background: var(--surface-2);
            color: var(--text-1);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }

        /* ─────────── SIDEBAR ─────────── */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--surface);
            border-right: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            transition: width 0.25s cubic-bezier(0.4,0,0.2,1);
            flex-shrink: 0;
            position: fixed;
            top: 0; left: 0;
            height: 100%;
            z-index: 200;
            overflow: hidden;
        }

        .sidebar.collapsed { width: var(--sidebar-collapsed); }

        .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 16px;
            border-bottom: 1px solid var(--border);
            min-height: 64px;
            flex-shrink: 0;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            overflow: hidden;
        }

        .sidebar-logo-icon {
            width: 32px; height: 32px;
            background: var(--primary);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: white;
            flex-shrink: 0;
        }

        .sidebar-logo-text {
            font-weight: 700;
            font-size: 0.9rem;
            color: var(--text-1);
            white-space: nowrap;
            overflow: hidden;
            transition: opacity 0.2s, width 0.2s;
        }

        .sidebar.collapsed .sidebar-logo-text { opacity: 0; width: 0; }
        .sidebar.collapsed .sidebar-logo { gap: 0; }

        .sidebar-toggle {
            background: none; border: none; cursor: pointer;
            color: var(--text-3); padding: 4px;
            border-radius: 6px; transition: all 0.2s;
            flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
        }
        .sidebar-toggle:hover { background: var(--surface-2); color: var(--text-1); }

        .sidebar-nav {
            padding: 12px 8px;
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .nav-section-label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-3);
            padding: 8px 8px 4px;
            white-space: nowrap;
            overflow: hidden;
            transition: opacity 0.2s;
        }
        .sidebar.collapsed .nav-section-label { opacity: 0; }

        .nav-item {
            display: flex; align-items: center;
            gap: 10px; padding: 10px 10px;
            border-radius: 8px; cursor: pointer;
            text-decoration: none; color: var(--text-2);
            font-size: 0.875rem; font-weight: 500;
            transition: all 0.15s; white-space: nowrap;
            overflow: hidden; position: relative;
        }
        .nav-item:hover { background: var(--surface-2); color: var(--text-1); }
        .nav-item.active { background: var(--primary-light); color: var(--primary); font-weight: 600; }
        .nav-item svg { flex-shrink: 0; }

        .nav-item-text {
            overflow: hidden; transition: opacity 0.2s, width 0.2s;
        }
        .sidebar.collapsed .nav-item-text { opacity: 0; width: 0; }

        .nav-badge {
            margin-left: auto; background: var(--primary); color: white;
            font-size: 0.7rem; font-weight: 700;
            padding: 2px 7px; border-radius: 20px;
            flex-shrink: 0;
            transition: opacity 0.2s;
        }
        .sidebar.collapsed .nav-badge { opacity: 0; }

        .sidebar-footer {
            padding: 12px 8px;
            border-top: 1px solid var(--border);
            flex-shrink: 0;
        }

        /* ─────────── MAIN LAYOUT ─────────── */
        .main-wrapper {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: margin-left 0.25s cubic-bezier(0.4,0,0.2,1);
            min-width: 0;
        }

        .main-wrapper.collapsed { margin-left: var(--sidebar-collapsed); }

        /* ─────────── TOP NAV BAR ─────────── */
        .topnav {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 0 24px;
            height: 64px;
            display: flex;
            align-items: center;
            gap: 16px;
            position: sticky; top: 0; z-index: 100;
            box-shadow: var(--shadow-sm);
        }

        .topnav-page-title {
            font-weight: 700; font-size: 1rem; color: var(--text-1);
            white-space: nowrap;
        }

        /* ─────────── SEARCH BAR ─────────── */
        .search-wrap {
            flex: 1;
            max-width: 480px;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 9px 14px 9px 38px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-size: 0.875rem;
            color: var(--text-1);
            background: var(--surface-2);
            transition: all 0.2s;
            outline: none;
        }
        .search-input:focus {
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 3px rgba(79,70,229,0.12);
        }
        .search-input::placeholder { color: var(--text-3); }

        .search-icon {
            position: absolute; left: 11px; top: 50%; transform: translateY(-50%);
            color: var(--text-3); pointer-events: none;
        }

        /* ─────────── FILTER CHIPS / DROPDOWN AREA ─────────── */
        .filter-bar {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 10px 24px;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            transition: all 0.25s ease;
        }

        .filter-bar.hidden {
            display: none;
        }

        .filter-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-3);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            flex-shrink: 0;
        }

        .filter-select {
            padding: 7px 30px 7px 12px;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            font-size: 0.82rem;
            color: var(--text-2);
            background: var(--surface-2);
            cursor: pointer;
            outline: none;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            font-family: inherit;
            transition: all 0.2s;
        }
        .filter-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79,70,229,0.12);
        }

        .filter-clear-btn {
            padding: 7px 12px;
            background: none;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            font-size: 0.82rem;
            color: var(--text-3);
            cursor: pointer;
            font-family: inherit;
            display: flex; align-items: center; gap: 5px;
            transition: all 0.2s;
        }
        .filter-clear-btn:hover { border-color: var(--danger); color: var(--danger); background: var(--danger-light); }

        .filter-results-badge {
            margin-left: auto;
            font-size: 0.8rem;
            color: var(--text-3);
            background: var(--surface-2);
            padding: 4px 10px;
            border-radius: 20px;
            border: 1px solid var(--border);
            flex-shrink: 0;
        }

        /* ─────────── PAGE CONTENT ─────────── */
        .page-content {
            padding: 20px 24px;
            flex: 1;
        }

        /* ─────────── STATS ROW ─────────── */
        .stats-row {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .stat-card {
            background: var(--surface);
            padding: 12px 18px;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            display: flex; align-items: center; gap: 12px;
            flex: 1; min-width: 130px;
        }

        .stat-icon {
            width: 38px; height: 38px; border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .stat-all .stat-icon { background: #f1f5f9; color: #475569; }
        .stat-keep .stat-icon { background: var(--success-light); color: var(--success); }
        .stat-empty .stat-icon { background: var(--danger-light); color: var(--danger); }

        .stat-label { font-size: 0.72rem; font-weight: 600; color: var(--text-3); text-transform: uppercase; letter-spacing: 0.05em; }
        .stat-value { font-size: 1.35rem; font-weight: 700; color: var(--text-1); line-height: 1.2; }

        /* ─────────── TOOLBAR ROW ─────────── */
        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
            gap: 12px;
            flex-wrap: wrap;
        }

        .toolbar-left {
            display: flex; align-items: center; gap: 10px;
        }

        .toolbar-right {
            display: flex; align-items: center; gap: 10px;
        }

        .card-section-title {
            font-size: 0.95rem; font-weight: 700; color: var(--text-1);
            display: flex; align-items: center; gap: 8px;
        }

        /* ─────────── BUTTONS ─────────── */
        .btn {
            padding: 9px 16px;
            border: none; border-radius: 9px;
            font-size: 0.855rem; font-weight: 600;
            cursor: pointer;
            transition: all 0.18s ease;
            display: inline-flex; align-items: center; gap: 7px;
            box-shadow: var(--shadow-sm);
            font-family: inherit;
            white-space: nowrap;
        }

        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-hover); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(79,70,229,0.3); }

        .btn-success { background: var(--success); color: white; }
        .btn-success:hover { background: #047857; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(5,150,105,0.3); }

        .btn-warning { background: var(--surface); color: var(--warning); border: 1.5px solid #fcd34d; }
        .btn-warning:hover { background: var(--warning-light); }
        .btn-warning.active { background: var(--warning-light); border-color: #f59e0b; color: #92400e; }

        .btn-ghost { background: var(--surface); border: 1.5px solid var(--border); color: var(--text-2); box-shadow: none; }
        .btn-ghost:hover { background: var(--surface-2); }

        .btn-icon {
            padding: 8px; border-radius: 9px;
            background: var(--surface); border: 1.5px solid var(--border);
            color: var(--text-3); cursor: pointer; transition: all 0.18s;
            display: flex; align-items: center; justify-content: center;
        }
        .btn-icon:hover { background: var(--surface-2); color: var(--text-1); }

        .btn-danger { background: var(--danger); color: white; }
        .btn-danger:hover { background: #be123c; }

        /* ─────────── TABLE CARD ─────────── */
        .main-card {
            background: var(--surface);
            border-radius: 14px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .table-responsive {
            width: 100%; overflow-x: auto;
        }

        table {
            width: 100%; border-collapse: collapse;
            font-size: 0.875rem;
        }

        table th {
            background: var(--surface-2);
            padding: 12px 16px;
            font-weight: 600; font-size: 0.78rem;
            color: var(--text-3); text-align: left;
            text-transform: uppercase; letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        table td {
            padding: 13px 16px;
            border-bottom: 1px solid #f1f5f9;
            color: var(--text-2);
            vertical-align: middle; white-space: nowrap;
        }

        table tbody tr:last-child td { border-bottom: none; }
        table tbody tr:hover { background: #fafbff; }

        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 4px 10px; border-radius: 6px;
            font-size: 0.78rem; font-weight: 600;
        }
        .badge-success { background: var(--success-light); color: #065f46; }
        .badge-danger { background: var(--danger-light); color: #9f1239; }

        .location-tag {
            color: #2563eb; font-weight: 600;
            background: #eff6ff;
            padding: 3px 8px; border-radius: 5px;
            border: 1px solid #bfdbfe; font-size: 0.8rem;
        }

        .action-cell { display: flex; gap: 6px; align-items: center; }

        .btn-row {
            padding: 5px 11px; border-radius: 6px;
            font-size: 0.78rem; font-weight: 600; cursor: pointer; border: 1px solid transparent;
            transition: all 0.15s; font-family: inherit;
        }
        .btn-row-edit { background: #eff6ff; color: #2563eb; border-color: #bfdbfe; }
        .btn-row-edit:hover { background: #dbeafe; }
        .btn-row-delete { background: var(--danger-light); color: var(--danger); border-color: #fecdd3; }
        .btn-row-delete:hover { background: #ffe4e6; }
        .btn-row-dispatch { background: #f0fdf4; color: #16a34a; border-color: #bbf7d0; }
        .btn-row-dispatch:hover { background: #dcfce7; }

        /* ─────────── EMPTY / LOADING STATES ─────────── */
        .state-container { text-align: center; padding: 48px 24px; }
        .state-container .state-icon { font-size: 2.5rem; margin-bottom: 12px; }
        .state-container p { color: var(--text-3); font-size: 0.9rem; }
        .loading-dots { display: inline-flex; gap: 5px; }
        .loading-dots span {
            width: 7px; height: 7px; background: var(--primary); border-radius: 50%;
            animation: bounce 1.2s infinite ease-in-out;
        }
        .loading-dots span:nth-child(2) { animation-delay: 0.2s; }
        .loading-dots span:nth-child(3) { animation-delay: 0.4s; }
        @keyframes bounce { 0%,60%,100%{transform:translateY(0)}30%{transform:translateY(-8px)} }

        /* ─────────── MODAL BASE ─────────── */
        .modal {
            display: none; position: fixed; z-index: 1000;
            inset: 0;
            background: rgba(15,23,42,0.45);
            backdrop-filter: blur(4px);
            align-items: center; justify-content: center;
        }
        .modal.open { display: flex; }

        .modal-content {
            background: white; border-radius: 16px;
            width: 92%; max-width: 820px;
            box-shadow: var(--shadow-lg);
            animation: modalIn 0.22s ease-out;
            overflow: hidden;
            max-height: 90vh;
            display: flex; flex-direction: column;
        }
        @keyframes modalIn { from { transform: translateY(-16px) scale(0.98); opacity: 0; } to { transform: none; opacity: 1; } }

        .modal-header {
            display: flex; justify-content: space-between; align-items: center;
            padding: 18px 24px; border-bottom: 1px solid var(--border);
            background: var(--surface-2); flex-shrink: 0;
        }
        .modal-header h3 { font-size: 1rem; font-weight: 700; color: var(--text-1); }
        .close-modal { background: none; border: none; font-size: 22px; cursor: pointer; color: var(--text-3); line-height: 1; transition: color 0.15s; }
        .close-modal:hover { color: var(--text-1); }

        .modal-body { padding: 22px 24px; overflow-y: auto; flex: 1; }
        .modal-footer { padding: 14px 24px; border-top: 1px solid var(--border); background: var(--surface-2); display: flex; justify-content: flex-end; gap: 10px; flex-shrink: 0; }

        .form-grid { display: grid; grid-template-columns: 1fr; gap: 20px; }
        @media (min-width: 600px) { .form-grid { grid-template-columns: 1fr 1fr; } }

        .form-section-title {
            font-size: 0.85rem; font-weight: 700; color: var(--primary);
            margin-bottom: 14px; border-bottom: 2px solid #e0e7ff;
            padding-bottom: 6px; display: flex; align-items: center; gap: 6px;
        }

        .form-group { margin-bottom: 13px; }
        .form-group label { display: block; margin-bottom: 5px; color: var(--text-2); font-weight: 600; font-size: 0.82rem; }
        .form-group input, .form-group select {
            width: 100%; padding: 9px 12px;
            border: 1.5px solid var(--border); border-radius: 8px;
            font-size: 0.875rem; color: var(--text-1);
            font-family: inherit; transition: all 0.2s; background: white; outline: none;
        }
        .form-group input:focus, .form-group select:focus {
            border-color: var(--primary); box-shadow: 0 0 0 3px rgba(79,70,229,0.12);
        }
        .form-group input[readonly] { background: var(--surface-2); color: var(--text-3); }

        .btn-cancel { background: white; border: 1.5px solid var(--border); color: var(--text-2); }
        .btn-cancel:hover { background: var(--surface-2); }

        /* ─────────── DISPATCH MODAL SPECIFIC ─────────── */
        .dispatch-info-grid {
            display: grid; grid-template-columns: repeat(2, 1fr);
            gap: 10px; margin-bottom: 20px;
        }
        @media (min-width: 500px) { .dispatch-info-grid { grid-template-columns: repeat(3, 1fr); } }

        .dispatch-info-item {
            background: var(--surface-2); border: 1px solid var(--border);
            border-radius: 9px; padding: 10px 14px;
        }
        .dispatch-info-item .di-label { font-size: 0.72rem; font-weight: 600; color: var(--text-3); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 3px; }
        .dispatch-info-item .di-value { font-size: 0.92rem; font-weight: 700; color: var(--text-1); }

        .dispatch-qty-section {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1.5px solid #bae6fd;
            border-radius: 12px; padding: 18px 20px;
        }

        .dispatch-qty-label { font-size: 0.85rem; font-weight: 700; color: #0369a1; margin-bottom: 12px; display: flex; align-items: center; gap: 6px; }

        .qty-input-row { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }

        .qty-input-field {
            flex: 1; padding: 10px 14px;
            border: 2px solid #bae6fd; border-radius: 9px;
            font-size: 1.1rem; font-weight: 700; color: var(--text-1);
            font-family: inherit; outline: none; background: white;
            transition: all 0.2s;
        }
        .qty-input-field:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(79,70,229,0.12); }

        .qty-btn {
            width: 38px; height: 38px; border-radius: 8px;
            background: white; border: 2px solid #bae6fd;
            font-size: 1.3rem; font-weight: 700; cursor: pointer;
            color: #0369a1; transition: all 0.15s;
            display: flex; align-items: center; justify-content: center;
        }
        .qty-btn:hover { background: #e0f2fe; border-color: #7dd3fc; }

        .qty-remaining-row {
            display: flex; align-items: center; justify-content: space-between;
            background: white; border-radius: 9px; padding: 10px 14px;
            border: 1px solid #bae6fd;
        }
        .qty-remaining-label { font-size: 0.82rem; font-weight: 600; color: var(--text-3); }
        .qty-remaining-value { font-size: 1.1rem; font-weight: 800; }
        .qty-remaining-ok { color: var(--success); }
        .qty-remaining-warn { color: var(--warning); }
        .qty-remaining-bad { color: var(--danger); }

        /* ─────────── TOAST ─────────── */
        .toast {
            position: fixed; top: 20px; right: 20px;
            padding: 13px 20px; border-radius: 10px;
            color: white; font-weight: 600; font-size: 0.875rem;
            box-shadow: var(--shadow-lg); z-index: 2000;
            opacity: 0; transform: translateY(-12px);
            transition: all 0.28s cubic-bezier(0.16,1,0.3,1);
            display: none; max-width: 340px;
        }
        .toast.show { display: block; opacity: 1; transform: translateY(0); }
        .toast-success { background: #10b981; }
        .toast-error { background: var(--danger); }
        .toast-warning { background: var(--warning); }

        /* ─────────── RESPONSIVE ─────────── */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.mobile-open { transform: translateX(0); }
            .main-wrapper { margin-left: 0 !important; }
            .mobile-overlay { display: block !important; }
            .topnav { padding: 0 16px; }
            .page-content { padding: 14px 16px; }
            .dispatch-info-grid { grid-template-columns: 1fr 1fr; }
        }

        .mobile-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(15,23,42,0.4); z-index: 190;
        }

        /* ─────────── TOOLTIP ─────────── */
        [data-tip] { position: relative; }
        [data-tip]:hover::after {
            content: attr(data-tip);
            position: absolute; left: 50%; transform: translateX(-50%);
            bottom: calc(100% + 6px);
            background: #1e293b; color: white;
            font-size: 0.72rem; white-space: nowrap;
            padding: 4px 9px; border-radius: 5px;
            pointer-events: none; z-index: 999;
        }

        .search-highlight {
            background: #fef08a; border-radius: 2px; font-weight: 700;
        }
    </style>
</head>
<body>

<!-- Mobile overlay -->
<div class="mobile-overlay" id="mobileOverlay" onclick="closeMobileSidebar()"></div>

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

        <a class="nav-item active" href="#" onclick="return false;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <span class="nav-item-text">คลังสินค้า</span>
            <span class="nav-badge" id="sidebarBadge">0</span>
        </a>

        <a class="nav-item" href="#" onclick="showComingSoon('Order History'); return false;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span class="nav-item-text">Order History</span>
        </a>

        <a class="nav-item" href="#" onclick="showComingSoon('Warehouse Layout'); return false;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
            <span class="nav-item-text">Warehouse Layout</span>
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

<!-- ═══════════ MAIN WRAPPER ═══════════ -->
<div class="main-wrapper" id="mainWrapper">

    <!-- TOP NAV -->
    <header class="topnav">
        <!-- Mobile hamburger -->
        <button class="btn-icon" id="mobileMenuBtn" onclick="openMobileSidebar()" style="display:none;">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>

        <span class="topnav-page-title">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline;vertical-align:-2px;margin-right:6px;color:var(--primary)"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            คลังสินค้า
        </span>

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
        <button class="btn btn-ghost" onclick="toggleFilterBar()" id="filterToggleBtn" style="padding:8px 12px;" data-tip="แสดง/ซ่อน Filter">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 4h18M7 12h10M11 20h2"/></svg>
            <span style="font-size:0.82rem;">Filter</span>
        </button>

        <button class="btn-icon" onclick="loadInventory()" data-tip="รีเฟรช">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H17"/></svg>
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
    </header>

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
            <option value="C">คลัง C</option>
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
                                <option value="C">คลังสินค้า C</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>แถวชั้นวาง (Row)</label>
                            <select id="rowLocation">
                                <option value="A">แถว A</option>
                                <option value="B">แถว B</option>
                                <option value="C">แถว C</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>ช่องแนวตั้ง (Column)</label>
                            <select id="columnLocation">
                                <option value="1">ช่องที่ 1</option>
                                <option value="2">ช่องที่ 2</option>
                                <option value="3">ช่องที่ 3</option>
                                <option value="4">ช่องที่ 4</option>
                                <option value="5">ช่องที่ 5</option>
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
            // warehouse (first char of location_id)
            if (warehouseF) {
                const wh = (item.location_id || '').charAt(0).toUpperCase();
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
            const canDispatch = item.status === 'Keep' && item.quantity > 0;

            html += `<tr>
                <td style="font-weight:700;color:var(--text-1);">${hl(item.product_id)}</td>
                <td>${hl(item.product_name)}</td>
                <td style="font-weight:600;">${item.quantity}</td>
                <td><span class="badge ${badgeClass}">${badgeText}</span></td>
                <td><span class="location-tag">${hl(locId)}</span></td>
                <td style="color:var(--text-3);font-size:0.82rem;">${dateFmt}</td>
                <td>
                    <button class="btn-row btn-row-dispatch"
                        ${!canDispatch ? 'disabled style="opacity:.4;cursor:not-allowed;"' : ''}
                        onclick="${canDispatch ? `openDispatchModal(${item.id})` : ''}">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline;vertical-align:-1px;"><path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        เบิก
                    </button>
                </td>
                ${isEditMode ? `<td>
                    <div class="action-cell">
                        <button class="btn-row btn-row-edit" onclick="editItem(${item.id})">แก้ไข</button>
                        <button class="btn-row btn-row-delete" onclick="openDeleteModal(${item.id}, '${escAttr(item.product_name)}')">ลบ</button>
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
        document.getElementById('sidebarBadge').textContent = total;
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
        const item = inventoryCachedData.find(i => i.id === id);
        if (!item) return;
        dispatchItem = item;

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