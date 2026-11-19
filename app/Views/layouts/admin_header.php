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
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
            min-height: 100vh;
        }
        .admin-sidebar {
            width: 260px;
            background-color: #0f172a;
            min-height: 100vh;
            color: #94a3b8;
            flex-shrink: 0;
        }
        .admin-sidebar .nav-link {
            color: #94a3b8;
            padding: 12px 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
            border-radius: 8px;
            margin: 4px 12px;
            transition: all 0.2s;
        }
        .admin-sidebar .nav-link:hover, .admin-sidebar .nav-link.active {
            background-color: #1e293b;
            color: #fff;
        }
        .admin-sidebar .nav-link.active {
            background-color: #ff4757;
            color: #fff;
        }
        .admin-main {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }
        .admin-topbar {
            background: #fff;
            padding: 14px 28px;
            border-bottom: 1px solid #e2e8f0;
        }
        .card-stat {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            transition: transform 0.2s;
        }
        .card-stat:hover {
            transform: translateY(-3px);
        }
    </style>
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <aside class="admin-sidebar py-3">
        <div class="px-4 mb-4">
            <a href="<?= route_url('admin') ?>" class="text-white text-decoration-none fs-5 fw-bold d-flex align-items-center gap-2">
                <i class="fa-solid fa-shield-halved text-danger"></i> SoleVibe Admin
            </a>
            <div class="small text-secondary mt-1">Hệ Thống Quản Lý Giày</div>
        </div>

        <nav class="nav flex-column">
            <a class="nav-link <?= (!isset($_GET['act']) || $_GET['act'] === 'admin') ? 'active' : '' ?>" href="<?= route_url('admin') ?>">
                <i class="fa-solid fa-chart-line"></i> Dashboard
            </a>
            <a class="nav-link <?= (isset($_GET['act']) && str_contains($_GET['act'], 'product')) ? 'active' : '' ?>" href="<?= route_url('admin-products') ?>">
                <i class="fa-solid fa-shoe-prints"></i> Quản Lý Giày
            </a>
            <a class="nav-link <?= (isset($_GET['act']) && str_contains($_GET['act'], 'categories')) ? 'active' : '' ?>" href="<?= route_url('admin-categories') ?>">
                <i class="fa-solid fa-tags"></i> Quản Lý Danh Mục
            </a>
            <a class="nav-link <?= (isset($_GET['act']) && str_contains($_GET['act'], 'order')) ? 'active' : '' ?>" href="<?= route_url('admin-orders') ?>">
                <i class="fa-solid fa-receipt"></i> Quản Lý Đơn Hàng
            </a>
            <a class="nav-link <?= (isset($_GET['act']) && str_contains($_GET['act'], 'voucher')) ? 'active' : '' ?>" href="<?= route_url('admin-vouchers') ?>">
                <i class="fa-solid fa-ticket"></i> Quản Lý Voucher
            </a>
            <a class="nav-link <?= (isset($_GET['act']) && str_contains($_GET['act'], 'report')) ? 'active' : '' ?>" href="<?= route_url('admin-reports') ?>">
                <i class="fa-solid fa-chart-pie"></i> Báo Cáo Thống Kê
            </a>
            
            <hr class="border-secondary mx-3 my-3">
            
            <a class="nav-link text-warning" href="<?= route_url() ?>" target="_blank">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Xem Cửa Hàng
            </a>
            <a class="nav-link text-danger" href="<?= route_url('logout') ?>">
                <i class="fa-solid fa-right-from-bracket"></i> Đăng Xuất
            </a>
        </nav>
    </aside>

    <!-- Main Content Area -->
    <div class="admin-main">
        <!-- Topbar -->
        <header class="admin-topbar d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark"><?= htmlspecialchars($pageTitle ?? 'Quản Trị') ?></h5>
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-danger px-3 py-2">Quản Trị Viên</span>
                <span class="fw-semibold text-dark"><?= htmlspecialchars(auth()['full_name'] ?? 'Admin') ?></span>
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
        <main class="p-4">
