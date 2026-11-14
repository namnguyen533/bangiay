<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Cart;

class CartController extends BaseController
{
    private Cart $cart;

    public function __construct()
    {
        $this->cart = new Cart();
    }

    public function index(): void
    {
        $items = $this->cart->getItems();
        $totalAmount = $this->cart->getTotalAmount();
        $totalQuantity = $this->cart->getTotalQuantity();

        $this->view('cart', [
            'pageTitle'     => 'Giỏ Hàng Của Bạn - SoleVibe',
            'items'         => $items,
            'totalAmount'   => $totalAmount,
            'totalQuantity' => $totalQuantity,
        ]);
    }

    public function add(): void
    {
        $variantId = (int)($_POST['variant_id'] ?? 0);
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));

        if ($variantId <= 0) {
            set_flash('error', 'Vui lòng chọn size và màu sắc giày!', 'warning');
            $this->redirect($_SERVER['HTTP_REFERER'] ?? route_url('products'));
        }

        $success = $this->cart->add($variantId, $quantity);

        if ($success) {
            set_flash('success', 'Đã thêm giày vào giỏ hàng thành công!', 'success');
        } else {
            set_flash('error', 'Không tìm thấy thông tin phiên bản giày!', 'danger');
        }

        $this->redirect(route_url('cart'));
    }

    public function update(): void
    {
        $variantId = (int)($_POST['variant_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);

        if ($variantId > 0) {
            $this->cart->update($variantId, $quantity);
            set_flash('success', 'Đã cập nhật số lượng!', 'info');
        }

        $this->redirect(route_url('cart'));
    }

    public function remove(): void
    {
        $variantId = (int)($_GET['variant_id'] ?? 0);
        if ($variantId > 0) {
            $this->cart->remove($variantId);
            set_flash('success', 'Đã xóa sản phẩm khỏi giỏ hàng!', 'info');
        }

        $this->redirect(route_url('cart'));
    }
}
