<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Quản Trị SoleVibe') ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --admin-primary: #ff4757;
            --admin-ink: #172033;
            --admin-muted: #718096;
            --admin-border: #e8edf4;
            --admin-canvas: #f5f7fb;
        }
        body {
            min-height: 100vh;
            background: var(--admin-canvas);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .admin-sidebar {
            position: sticky;
            top: 0;
            width: 260px;
            height: 100vh;
            min-height: 100vh;
            overflow-y: auto;
            flex-shrink: 0;
            color: #94a3b8;
            background: linear-gradient(180deg, #111b2e, #0d1525);
        }
        .admin-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #fff;
            font-size: 1.05rem;
            font-weight: 800;
            text-decoration: none;
        }
        .admin-brand-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 13px;
            color: #fff;
            background: var(--admin-primary);
            box-shadow: 0 8px 18px rgba(255, 71, 87, 0.24);
        }
        .admin-sidebar-caption {
            padding: 0 30px;
            color: #64748b;
            font-size: 0.68rem;
            font-weight: 800;
            letter-spacing: 1.2px;
            text-transform: uppercase;
        }
        .admin-sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: 13px;
            margin: 4px 16px;
            padding: 11px 14px;
            border-radius: 11px;
            color: #94a3b8;
            font-size: 0.9rem;
            font-weight: 600;
            transition: color 0.2s, background-color 0.2s;
        }
        .admin-sidebar .nav-link i {
            width: 18px;
            text-align: center;
        }
        .admin-sidebar .nav-link:hover {
            color: #fff;
            background: #1e293b;
        }
        .admin-sidebar .nav-link.active {
            color: #fff;
            background: var(--admin-primary);
            box-shadow: 0 8px 18px rgba(255, 71, 87, 0.18);
        }
        .admin-main {
            display: flex;
            min-width: 0;
            flex-grow: 1;
            flex-direction: column;
            overflow-x: hidden;
        }
        .admin-topbar {
            position: sticky;
            z-index: 20;
            top: 0;
            min-height: 76px;
            padding: 12px 30px;
            border-bottom: 1px solid var(--admin-border);
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(12px);
        }
        .admin-topbar-title {
            color: var(--admin-ink);
            font-size: 1.05rem;
            font-weight: 800;
        }
        .admin-avatar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            color: #b42332;
            background: #fff0f1;
        }
        .admin-content {
            width: 100%;
            max-width: 1600px;
            margin: 0 auto;
            padding: 30px;
        }
        .admin-sidebar-backdrop,
        .admin-sidebar-toggle {
            display: none;
        }
        .admin-page-heading {
            margin-bottom: 24px;
        }
        .admin-page-heading h1 {
            margin-bottom: 5px;
            color: var(--admin-ink);
            font-size: clamp(1.45rem, 2vw, 1.8rem);
            font-weight: 800;
            letter-spacing: -0.7px;
        }
        .admin-page-heading p {
            margin: 0;
            color: var(--admin-muted);
            font-size: 0.9rem;
        }
        .card-stat {
            height: 100%;
            padding: 22px;
            border: 1px solid var(--admin-border);
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 6px 22px rgba(15, 23, 42, 0.035);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card-stat:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
        }
        .admin-stat-label {
            color: var(--admin-muted);
            font-size: 0.76rem;
            font-weight: 800;
            letter-spacing: 0.55px;
            text-transform: uppercase;
        }
        .admin-stat-value {
            margin: 9px 0 5px;
            color: var(--admin-ink);
            font-size: clamp(1.35rem, 2vw, 1.8rem);
            font-weight: 800;
            letter-spacing: -0.7px;
        }
        .admin-stat-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 15px;
            font-size: 1.15rem;
        }
        .admin-stat-note {
            color: #94a3b8;
            font-size: 0.78rem;
        }
        .admin-panel {
            border: 1px solid var(--admin-border);
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 6px 22px rgba(15, 23, 42, 0.035);
        }
        .admin-panel-heading {
            padding: 20px 22px;
            border-bottom: 1px solid var(--admin-border);
        }
        .admin-panel-heading h2 {
            margin: 0;
            color: var(--admin-ink);
            font-size: 1rem;
            font-weight: 800;
        }
        .admin-quick-link {
            display: flex;
            min-height: 64px;
            align-items: center;
            gap: 12px;
            padding: 13px 15px;
            border: 1px solid var(--admin-border);
            border-radius: 14px;
            color: var(--admin-ink);
            font-size: 0.86rem;
            font-weight: 700;
            text-decoration: none;
            transition: border-color 0.2s, transform 0.2s, box-shadow 0.2s;
        }
        .admin-quick-link:hover {
            transform: translateY(-2px);
            border-color: #ffc2c8;
            color: var(--admin-primary);
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
        }
        .admin-quick-link i:first-child {
            display: inline-flex;
            width: 36px;
            height: 36px;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border-radius: 11px;
            color: var(--admin-primary);
            background: #fff0f1;
        }
        .admin-orders-table {
            margin: 0;
            white-space: nowrap;
        }
        .admin-orders-table thead th {
            padding: 12px 18px;
            border-bottom: 1px solid var(--admin-border);
            background: #f8fafc;
            color: #718096;
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 0.45px;
            text-transform: uppercase;
        }
        .admin-orders-table tbody td {
            padding: 14px 18px;
            border-color: #f1f4f8;
            color: #475569;
            font-size: 0.83rem;
        }
        .admin-order-code {
            color: #d93848;
            font-weight: 800;
        }
        .admin-order-status {
            display: inline-flex;
            align-items: center;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
        }
        @media (max-width: 991.98px) {
            .admin-sidebar {
                position: fixed;
                z-index: 1041;
                top: 0;
                bottom: 0;
                left: 0;
                height: 100dvh;
                min-height: 0;
                transform: translateX(-100%);
                transition: transform 0.25s ease;
            }
            body.admin-sidebar-open {
                overflow: hidden;
            }
            body.admin-sidebar-open .admin-sidebar {
                transform: translateX(0);
            }
            .admin-sidebar-backdrop {
                position: fixed;
                z-index: 1040;
                inset: 0;
                display: block;
                border: 0;
                background: rgba(15, 23, 42, 0.5);
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.25s ease;
            }
            body.admin-sidebar-open .admin-sidebar-backdrop {
                opacity: 1;
                pointer-events: auto;
            }
            .admin-sidebar-toggle {
                display: inline-flex;
                width: 40px;
                height: 40px;
                align-items: center;
                justify-content: center;
                border: 1px solid var(--admin-border);
                border-radius: 11px;
                color: var(--admin-ink);
                background: #fff;
            }
        }
        @media (max-width: 575.98px) {
            .admin-topbar {
                min-height: 64px;
                padding: 10px 15px;
            }
            .admin-content {
                padding: 20px 15px;
            }
            .admin-topbar .badge {
                display: none;
            }
            .admin-topbar .small {
                display: none;
            }
            .admin-page-heading {
                align-items: flex-start !important;
                flex-direction: column;
                gap: 14px;
            }
            .admin-stat-icon {
                width: 42px;
                height: 42px;
            }
            .card-stat {
                padding: 17px;
            }
        }
    </style>
</head>
<body>

<div class="admin-shell d-flex min-vh-100">
    <!-- Sidebar -->
    <aside class="admin-sidebar py-3" id="adminSidebar">
        <div class="px-4 mb-4">
            <a href="<?= route_url('admin') ?>" class="admin-brand">
                <span class="admin-brand-icon"><i class="fa-solid fa-shield-halved"></i></span>
                <span>SoleVibe<small class="d-block text-secondary fw-normal mt-1">TRANG QUẢN TRỊ</small></span>
            </a>
        </div>

        <nav class="nav flex-column">
            <div class="admin-sidebar-caption mb-2">Tổng quan</div>
            <a class="nav-link <?= (!isset($_GET['act']) || in_array($_GET['act'], ['admin', 'admin-dashboard'], true)) ? 'active' : '' ?>" href="<?= route_url('admin') ?>">
                <i class="fa-solid fa-chart-pie"></i> Tổng quan
            </a>

            <div class="admin-sidebar-caption mt-4 mb-2">Quản lý cửa hàng</div>
            <a class="nav-link <?= (isset($_GET['act']) && str_contains($_GET['act'], 'product')) ? 'active' : '' ?>" href="<?= route_url('admin-products') ?>">
                <i class="fa-solid fa-shoe-prints"></i> Sản phẩm
            </a>
            <a class="nav-link <?= (isset($_GET['act']) && str_contains($_GET['act'], 'categor')) ? 'active' : '' ?>" href="<?= route_url('admin-categories') ?>">
                <i class="fa-solid fa-tags"></i> Danh mục
            </a>
            <a class="nav-link <?= (isset($_GET['act']) && str_contains($_GET['act'], 'order')) ? 'active' : '' ?>" href="<?= route_url('admin-orders') ?>">
                <i class="fa-solid fa-receipt"></i> Đơn hàng
            </a>
            
            <hr class="border-secondary mx-3 my-3">
            
            <a class="nav-link text-warning" href="<?= route_url() ?>" target="_blank">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Xem cửa hàng
            </a>
            <a class="nav-link text-danger" href="<?= route_url('logout') ?>">
                <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
            </a>
        </nav>
    </aside>

    <button class="admin-sidebar-backdrop" type="button" aria-label="Đóng menu quản trị"></button>

    <!-- Main Content Area -->
    <div class="admin-main">
        <!-- Topbar -->
        <header class="admin-topbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <button class="admin-sidebar-toggle" type="button" aria-label="Mở menu quản trị" aria-controls="adminSidebar" aria-expanded="false">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <span class="admin-topbar-title"><?= htmlspecialchars($pageTitle ?? 'Quản Trị') ?></span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="badge rounded-pill bg-danger-subtle text-danger px-3 py-2">Quản trị viên</span>
                <span class="admin-avatar"><i class="fa-solid fa-user"></i></span>
                <span class="fw-semibold text-dark small"><?= htmlspecialchars(auth()['full_name'] ?? 'Admin') ?></span>
            </div>
        </header>

        <!-- Flash alerts in Admin -->
        <div class="p-4 pb-0">
            <?php foreach (['success', 'error', 'info', 'warning'] as $flashType): ?>
                <?php if ($flash = get_flash($flashType)): ?>
                    <div class="alert alert-<?= $flash['type'] ?? 'info' ?> alert-dismissible fade show" role="alert">
                        <i class="fa-solid fa-circle-info me-2"></i>
                        <?= htmlspecialchars($flash['message']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <!-- Dynamic Admin Page Body -->
        <main class="admin-content">
