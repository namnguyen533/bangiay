<?php

namespace App\Core;

use App\Controllers\HomeController;
use App\Controllers\ProductController;
use App\Controllers\CartController;
use App\Controllers\CheckoutController;
use App\Controllers\AuthController;
use App\Controllers\ReviewController;
use App\Controllers\Admin\BrandController as AdminBrandController;
use App\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Controllers\Admin\ProductController as AdminProductController;
use App\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Controllers\Admin\OrderController as AdminOrderController;
use App\Controllers\Admin\ReviewController as AdminReviewController;
use App\Controllers\Admin\StockReceiptController as AdminStockReceiptController;

class Router
{
    public function dispatch(): void
    {
        $act = $_GET['act'] ?? $_GET['action'] ?? '';
        $act = trim($act, '/');

        // Phân phối route
        match ($act) {
            '', 'home' => (new HomeController())->index(),
            'products' => (new ProductController())->index(),
            'product-detail', 'detail' => (new ProductController())->detail(),
            'review-submit' => (new ReviewController())->submit(),
            
            // Giỏ hàng
            'cart'        => (new CartController())->index(),
            'cart-add'    => (new CartController())->add(),
            'cart-update' => (new CartController())->update(),
            'cart-remove' => (new CartController())->remove(),

            // Thanh toán
            'checkout'         => (new CheckoutController())->index(),
            'checkout-process' => (new CheckoutController())->process(),
            'order-success'    => (new CheckoutController())->success(),

            // Tài khoản
            'login'         => (new AuthController())->login(),
            'post-login'    => (new AuthController())->postLogin(),
            'register'      => (new AuthController())->register(),
            'post-register' => (new AuthController())->postRegister(),
            'logout'        => (new AuthController())->logout(),

            // Quản trị Admin
            'admin', 'admin-dashboard' => (new AdminDashboardController())->index(),
            
            'admin-products'        => (new AdminProductController())->index(),
            'admin-product-create'  => (new AdminProductController())->create(),
            'admin-product-store'   => (new AdminProductController())->store(),
            'admin-product-edit'    => (new AdminProductController())->edit(),
            'admin-product-update'  => (new AdminProductController())->update(),
            'admin-product-delete'  => (new AdminProductController())->delete(),

            'admin-categories'      => (new AdminCategoryController())->index(),
            'admin-category-store'  => (new AdminCategoryController())->store(),
            'admin-category-edit'   => (new AdminCategoryController())->edit(),
            'admin-category-update' => (new AdminCategoryController())->update(),
            'admin-category-delete' => (new AdminCategoryController())->delete(),

            'admin-brands'       => (new AdminBrandController())->index(),
            'admin-brand-store'  => (new AdminBrandController())->store(),
            'admin-brand-edit'   => (new AdminBrandController())->edit(),
            'admin-brand-update' => (new AdminBrandController())->update(),
            'admin-brand-delete' => (new AdminBrandController())->delete(),

            'admin-inventory'            => (new AdminStockReceiptController())->index(),
            'admin-stock-receipt-store'  => (new AdminStockReceiptController())->store(),

            'admin-reviews'       => (new AdminReviewController())->index(),
            'admin-review-update' => (new AdminReviewController())->update(),

            'admin-orders'              => (new AdminOrderController())->index(),
            'admin-order-detail'        => (new AdminOrderController())->detail(),
            'admin-order-update-status' => (new AdminOrderController())->updateStatus(),

            default => (new HomeController())->index(),
        };
    }
}
