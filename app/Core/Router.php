<?php

namespace App\Core;

use App\Controllers\HomeController;
use App\Controllers\ProductController;
use App\Controllers\CartController;
use App\Controllers\CheckoutController;
use App\Controllers\AuthController;
use App\Controllers\WishlistController;
use App\Controllers\OrderController;
use App\Controllers\ReviewController;
use App\Controllers\Admin\BrandController as AdminBrandController;
use App\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Controllers\Admin\ProductController as AdminProductController;
use App\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Controllers\Admin\OrderController as AdminOrderController;
use App\Controllers\Admin\VoucherController as AdminVoucherController;
use App\Controllers\Admin\ReportController as AdminReportController;
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
            'search-suggest' => (new ProductController())->searchSuggest(),
            'review-submit' => (new ReviewController())->submit(),
            
            // Sản phẩm yêu thích (Wishlist)
            'wishlist'        => (new WishlistController())->index(),
            'wishlist-toggle' => (new WishlistController())->toggle(),
            'wishlist-remove' => (new WishlistController())->remove(),

            // Giỏ hàng & Voucher
            'cart'                => (new CartController())->index(),
            'cart-add'            => (new CartController())->add(),
            'cart-update'         => (new CartController())->update(),
            'cart-change-variant' => (new CartController())->changeVariant(),
            'cart-remove'         => (new CartController())->remove(),
            'cart-apply-voucher'  => (new CartController())->applyVoucher(),
            'cart-remove-voucher' => (new CartController())->removeVoucher(),

            // Đặt hàng & Thanh toán
            'checkout'         => (new CheckoutController())->index(),
            'checkout-process' => (new CheckoutController())->process(),
            'order-success'    => (new CheckoutController())->success(),
            'payment-gateway'  => (new CheckoutController())->paymentGateway(),
            'payment-confirm'  => (new CheckoutController())->confirmPayment(),

            // Đơn hàng phía Client (Theo dõi & Lịch sử & Hủy đơn)
            'my-orders'     => (new OrderController())->myOrders(),
            'order-detail'  => (new OrderController())->detail(),
            'track-order'   => (new OrderController())->track(),
            'order-cancel'  => (new OrderController())->cancel(),

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
            'admin-order-print'         => (new AdminOrderController())->print(),

            'admin-vouchers'        => (new AdminVoucherController())->index(),
            'admin-voucher-create'  => (new AdminVoucherController())->create(),
            'admin-voucher-store'   => (new AdminVoucherController())->store(),
            'admin-voucher-edit'    => (new AdminVoucherController())->edit(),
            'admin-voucher-update'  => (new AdminVoucherController())->update(),
            'admin-voucher-delete'  => (new AdminVoucherController())->delete(),

            'admin-reports'         => (new AdminReportController())->index(),
            'admin-report-export'   => (new AdminReportController())->export(),

            default => (new HomeController())->index(),
        };
    }
}
