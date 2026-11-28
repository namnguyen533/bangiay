<?php
use App\Models\Cart;
$cartModel = new Cart();
$cartCount = $cartModel->getTotalQuantity();
$currentUser = auth();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Apple Meo Meo Shoes Store') ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap&amp;subset=vietnamese" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary: #ff4757;
            --primary-hover: #ee3545;
            --dark: #1e293b;
            --secondary: #64748b;
            --light-bg: #f8fafc;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--light-bg);
            color: #334155;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .site-header {
            z-index: 1030;
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
        }
        .utility-bar {
            min-height: 34px;
            background: #f5f5f5;
            color: #111827;
            font-size: 0.72rem;
        }
        .utility-bar a {
            color: inherit;
            text-decoration: none;
        }
        .utility-bar a:hover {
            text-decoration: underline;
        }
        .utility-divider {
            margin: 0 0.75rem;
            color: #9ca3af;
        }
        .navbar {
            min-height: 64px;
        }
        .navbar-brand {
            display: flex;
            align-items: center;
            flex-shrink: 0;
            margin-right: 2rem;
            font-weight: 800;
            font-size: 1.5rem;
            color: #0f172a !important;
            letter-spacing: -0.5px;
        }
        .navbar-brand span {
            color: var(--primary);
        }
        .main-nav-links {
            gap: 0.25rem;
        }
        .main-nav-links .nav-link {
            padding: 1.25rem 0.8rem;
            color: #111827 !important;
            font-size: 0.9rem;
            font-weight: 700;
            white-space: nowrap;
        }
        .main-nav-links .nav-link:hover,
        .main-nav-links .nav-link.active {
            color: var(--primary) !important;
        }
        .header-actions {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            flex-shrink: 0;
        }
        .navbar-search {
            position: relative;
            width: 170px;
            z-index: 1;
        }
        .navbar-search .input-group {
            overflow: hidden;
            border-radius: 999px;
            background: #f3f4f6;
        }
        .navbar-search .form-control {
            min-width: 0;
            padding: 0.55rem 0.85rem;
            background: transparent !important;
            font-size: 0.88rem;
        }
        .navbar-search .form-control:focus {
            box-shadow: none;
        }
        .navbar-search .btn {
            padding: 0 0.85rem;
            color: #111827;
            background: transparent;
            border: 0;
        }
        .header-icon-link {
            position: relative;
            display: inline-flex;
            width: 40px;
            height: 40px;
            align-items: center;
            justify-content: center;
            color: #111827;
            border-radius: 50%;
            font-size: 1.15rem;
            text-decoration: none;
            transition: background-color 0.2s, color 0.2s;
        }
        .header-icon-link:hover {
            color: var(--primary);
            background: #f3f4f6;
        }
        .header-icon-link .badge {
            top: 0;
            right: 0;
            font-size: 0.62rem;
        }
        @media (max-width: 1199.98px) {
            .navbar > .container {
                flex-wrap: wrap;
            }
            .navbar-brand {
                margin-right: auto;
            }
            .navbar-toggler {
                order: 1;
                margin-left: 0.5rem;
            }
            .header-actions {
                order: 2;
                flex: 0 0 100%;
                padding: 0.2rem 0 0.75rem;
            }
            .navbar-search {
                flex: 1 1 auto;
                width: auto;
                max-width: none;
            }
            #navContent {
                order: 3;
                flex-basis: 100%;
            }
            .main-nav-links .nav-link {
                padding: 0.75rem 0;
            }
        }
        @media (min-width: 1200px) {
            #navContent {
                justify-content: center;
            }
        }
        .nav-link {
            font-weight: 600;
            color: #475569 !important;
            transition: color 0.2s;
        }
        .nav-link:hover, .nav-link.active {
            color: var(--primary) !important;
        }
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            font-weight: 600;
        }
        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }
        .product-card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #f1f5f9;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.07);
        }
        .product-img-box {
            position: relative;
            background-color: #f1f5f9;
            padding-top: 75%;
            overflow: hidden;
        }
        .product-img-box img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.35s ease;
        }
        .product-card:hover .product-img-box img {
            transform: scale(1.06);
        }
        .badge-brand {
            position: absolute;
            top: 12px;
            left: 12px;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(4px);
            color: #fff;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .badge-discount {
            position: absolute;
            top: 12px;
            right: 12px;
            background: var(--primary);
            color: #fff;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .cart-badge {
            font-size: 0.7rem;
            padding: 3px 6px;
            border-radius: 50%;
        }
        .auth-page {
            padding: 2rem 0 4rem;
        }
        .auth-card {
            display: grid;
            grid-template-columns: minmax(0, 0.9fr) minmax(0, 1.1fr);
            max-width: 1040px;
            margin: 0 auto;
            overflow: hidden;
            background: #fff;
            border: 1px solid #e8edf4;
            border-radius: 28px;
            box-shadow: 0 24px 70px rgba(15, 23, 42, 0.1);
        }
        .auth-showcase {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 620px;
            padding: 3rem;
            overflow: hidden;
            color: #fff;
            background: radial-gradient(circle at 15% 85%, rgba(255, 71, 87, 0.35), transparent 34%), linear-gradient(145deg, #0f172a, #263449 72%, #192334);
        }
        .auth-showcase::before,
        .auth-showcase::after {
            position: absolute;
            content: "";
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            pointer-events: none;
        }
        .auth-showcase::before {
            right: -125px;
            bottom: 60px;
            width: 340px;
            height: 340px;
        }
        .auth-showcase::after {
            right: -75px;
            bottom: 110px;
            width: 240px;
            height: 240px;
        }
        .auth-showcase > * {
            position: relative;
            z-index: 1;
        }
        .auth-brand-mark {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 64px;
            height: 64px;
            margin-bottom: 2rem;
            color: #fff;
            background: #000;
            border-radius: 12px;
            overflow: hidden;
            font-size: 1.2rem;
        }
        .auth-brand-mark img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .auth-showcase h1 {
            max-width: 390px;
            margin-bottom: 1rem;
            font-size: clamp(2rem, 3vw, 2.7rem);
            font-weight: 800;
            letter-spacing: -1.5px;
            line-height: 1.15;
        }
        .auth-showcase-copy {
            max-width: 370px;
            color: #cbd5e1;
            line-height: 1.8;
        }
        .auth-benefits {
            display: grid;
            gap: 1rem;
            margin: 2rem 0 0;
            padding: 0;
            list-style: none;
        }
        .auth-benefits li {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #e2e8f0;
            font-size: 0.92rem;
        }
        .auth-benefits i {
            color: #ff7b86;
        }
        .auth-showcase-note {
            margin: 2.5rem 0 0;
            color: #94a3b8;
            font-size: 0.8rem;
        }
        .auth-form-panel {
            display: flex;
            align-items: center;
            padding: clamp(2rem, 5vw, 4rem);
        }
        .auth-form-content {
            width: 100%;
            max-width: 430px;
            margin: 0 auto;
        }
        .auth-eyebrow {
            margin-bottom: 0.6rem;
            color: var(--primary);
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 1.4px;
            text-transform: uppercase;
        }
        .auth-form-title {
            margin-bottom: 0.5rem;
            color: #0f172a;
            font-size: 1.9rem;
            font-weight: 800;
            letter-spacing: -0.8px;
        }
        .auth-form-subtitle {
            margin-bottom: 2rem;
            color: #64748b;
            font-size: 0.92rem;
        }
        .auth-form .form-label {
            margin-bottom: 0.5rem;
            color: #334155;
            font-size: 0.87rem;
            font-weight: 700;
        }
        .auth-form .form-control {
            min-height: 50px;
            padding: 0.75rem 0.95rem;
            border-color: #e2e8f0;
            border-radius: 12px;
            font-size: 0.92rem;
        }
        .auth-form .form-control::placeholder {
            color: #a0aec0;
        }
        .auth-form .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(255, 71, 87, 0.12);
        }
        .auth-submit {
            min-height: 52px;
            border-radius: 13px;
            box-shadow: 0 8px 18px rgba(255, 71, 87, 0.2);
        }
        .auth-divider {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 1.25rem 0;
            color: #94a3b8;
            font-size: 0.75rem;
        }
        .auth-divider::before,
        .auth-divider::after {
            height: 1px;
            flex: 1;
            content: "";
            background: #e8edf4;
        }
        .auth-demo {
            padding: 1rem;
            border: 1px solid #e8edf4;
            border-radius: 14px;
            background: #f8fafc;
        }
        .auth-demo code {
            color: #475569;
        }
        .auth-switch {
            margin: 1.5rem 0 0;
            color: #64748b;
            font-size: 0.88rem;
            text-align: center;
        }
        .auth-switch a {
            color: var(--primary);
            font-weight: 800;
            text-decoration: none;
        }
        .auth-switch a:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }
        .auth-admin-entry {
            margin-top: 1.25rem;
            padding-top: 1.25rem;
            border-top: 1px solid #e8edf4;
            text-align: center;
        }
        .auth-admin-entry-label {
            margin-bottom: 0.65rem;
            color: #64748b;
            font-size: 0.78rem;
        }
        .auth-admin-entry .btn {
            min-height: 46px;
            border-color: #d7dee8;
            border-radius: 12px;
            color: #334155;
            font-size: 0.86rem;
        }
        .auth-admin-entry .btn:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: #fff7f8;
        }
        footer {
            margin-top: auto;
            background-color: #0f172a;
            color: #94a3b8;
        }
        @media (max-width: 767.98px) {
            .auth-page {
                padding: 1.25rem 0 3rem;
            }
            .auth-card {
                grid-template-columns: 1fr;
                border-radius: 22px;
            }
            .auth-showcase {
                min-height: 0;
                padding: 2rem;
            }
            .auth-brand-mark {
                margin-bottom: 1.25rem;
            }
            .auth-showcase h1 {
                font-size: 2rem;
            }
            .auth-benefits {
                grid-template-columns: 1fr 1fr;
                gap: 0.75rem;
                margin-top: 1.25rem;
            }
            .auth-benefits li {
                align-items: flex-start;
                font-size: 0.8rem;
            }
            .auth-showcase-note {
                margin-top: 1.5rem;
            }
            .auth-form-panel {
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>
<body>

    <!-- Header Navigation -->
    <header class="site-header sticky-top">
        <div class="utility-bar">
            <div class="container d-flex justify-content-end align-items-center">
                <a href="<?= route_url('track-order') ?>">Tra cứu đơn hàng</a>
                <span class="utility-divider">|</span>
                <?php if ($currentUser): ?>
                    <div class="dropdown">
                        <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?= htmlspecialchars($currentUser['full_name']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <?php if (!is_admin()): ?>
                                <li>
                                    <a class="dropdown-item py-2" href="<?= route_url('my-orders') ?>">
                                        <i class="fa-solid fa-box-open me-2 text-secondary"></i> Đơn Hàng Của Tôi
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <?php if (is_admin()): ?>
                                <li>
                                    <a class="dropdown-item py-2 text-danger fw-bold" href="<?= route_url('admin') ?>">
                                        <i class="fa-solid fa-gauge-high me-2"></i> Trang Quản Trị
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <li>
                                <a class="dropdown-item py-2" href="<?= route_url('wishlist') ?>">
                                    <i class="fa-solid fa-heart me-2 text-danger"></i> Sản Phẩm Yêu Thích
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item py-2" href="<?= route_url('logout') ?>">
                                    <i class="fa-solid fa-right-from-bracket me-2 text-secondary"></i> Đăng Xuất
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="<?= route_url('login') ?>">Đăng nhập</a>
                <?php endif; ?>
            </div>
        </div>
        <nav class="navbar navbar-expand-xl bg-white py-0">
            <div class="container">
                <a class="navbar-brand" href="<?= route_url() ?>">
                    <img src="<?= htmlspecialchars(asset('images/apple-meo-meo-logo.png')) ?>" alt="Apple Meo Meo Shoes Store" style="width: 48px; height: 48px; object-fit: cover; border-radius: 8px; margin-right: 10px;">
                    <span style="color: #0f172a;">Apple</span><span> Meo Meo</span>
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent" aria-controls="navContent" aria-expanded="false" aria-label="Mở menu">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navContent">
                    <ul class="navbar-nav main-nav-links">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= route_url() ?>">Trang Chủ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= route_url('products') ?>">Tất Cả Giày</a>
                        </li>
                    </ul>
                </div>

                <div class="header-actions">
                    <form class="navbar-search" action="<?= route_url('products') ?>" method="GET">
                        <input type="hidden" name="act" value="products">
                        <div class="input-group">
                            <input id="searchInput" class="form-control border-0" type="search" name="q" placeholder="Tìm kiếm" aria-label="Tìm kiếm sản phẩm" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" autocomplete="off">
                            <button class="btn" type="submit" aria-label="Tìm kiếm">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                        </div>
                        <div id="searchSuggestBox" class="position-absolute start-0 end-0 bg-white rounded-3 shadow-lg border mt-1 p-2 d-none" style="top: 100%; z-index: 1060; max-height: 420px; overflow-y: auto;"></div>
                    </form>

                    <?php $wishCount = wishlist_count(); ?>
                    <a href="<?= route_url('wishlist') ?>" class="header-icon-link" title="Sản phẩm yêu thích" aria-label="Sản phẩm yêu thích">
                        <i class="fa-regular fa-heart"></i>
                        <?php if ($wishCount > 0): ?>
                            <span class="position-absolute translate-middle badge bg-danger rounded-pill"><?= $wishCount ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="<?= route_url('cart') ?>" class="header-icon-link" title="Giỏ hàng" aria-label="Giỏ hàng">
                        <i class="fa-solid fa-bag-shopping"></i>
                        <?php if ($cartCount > 0): ?>
                            <span class="position-absolute translate-middle badge bg-danger rounded-pill"><?= $cartCount ?></span>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Flash Messages Container -->
    <div class="container mt-3">
        <?php foreach (['success', 'error', 'info', 'warning'] as $flashType): ?>
            <?php if ($flash = get_flash($flashType)): ?>
                <div class="alert alert-<?= $flash['type'] ?? 'info' ?> alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fa-solid fa-circle-info me-2"></i>
                    <?= htmlspecialchars($flash['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
