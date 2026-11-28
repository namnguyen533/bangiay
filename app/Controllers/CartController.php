<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Cart;
use App\Models\Voucher;

class CartController extends BaseController
{
    private Cart $cart;
    private Voucher $voucherModel;

    public function __construct()
    {
        $this->cart = new Cart();
        $this->voucherModel = new Voucher();
    }

    public function index(): void
    {
        $items = $this->cart->getItemsWithSiblings();
        $subtotal = $this->cart->getTotalAmount();
        $totalQuantity = $this->cart->getTotalQuantity();
        $shippingFee = $this->cart->getShippingFee();
        $appliedVoucher = $this->cart->getVoucher();
        $discountAmount = $this->cart->getDiscountAmount();
        $finalTotal = $this->cart->getFinalTotal();

        // Danh sách voucher đang khả dụng để khách tiện tham khảo
        $availableVouchers = $this->voucherModel->getActiveVouchers();

        $this->view('cart', [
            'pageTitle'         => 'Giỏ Hàng Của Bạn - Apple Meo Meo',
            'items'             => $items,
            'subtotal'          => $subtotal,
            'totalQuantity'     => $totalQuantity,
            'shippingFee'       => $shippingFee,
            'appliedVoucher'    => $appliedVoucher,
            'discountAmount'    => $discountAmount,
            'finalTotal'        => $finalTotal,
            'availableVouchers' => $availableVouchers,
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

        $result = $this->cart->add($variantId, $quantity);

        if ($result['success']) {
            set_flash('success', $result['message'], 'success');
        } else {
            set_flash('error', $result['message'], 'danger');
        }

        $this->redirect(route_url('cart'));
    }

    public function update(): void
    {
        $variantId = (int)($_POST['variant_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);

        if ($variantId > 0) {
            $result = $this->cart->update($variantId, $quantity);
            if ($result['success']) {
                set_flash('success', $result['message'], 'info');
            } else {
                set_flash('error', $result['message'], 'warning');
            }
        }

        $this->redirect(route_url('cart'));
    }

    /**
     * Đổi size / màu sắc trực tiếp trong giỏ hàng
     */
    public function changeVariant(): void
    {
        $oldVariantId = (int)($_POST['old_variant_id'] ?? 0);
        $newVariantId = (int)($_POST['new_variant_id'] ?? 0);

        if ($oldVariantId > 0 && $newVariantId > 0) {
            $res = $this->cart->changeVariant($oldVariantId, $newVariantId);
            if ($res['success']) {
                set_flash('success', $res['message'], 'success');
            } else {
                set_flash('error', $res['message'], 'danger');
            }
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

    public function applyVoucher(): void
    {
        $code = trim($_POST['voucher_code'] ?? '');
        $redirectUrl = $_POST['redirect_to'] ?? route_url('cart');

        if (empty($code)) {
            set_flash('error', 'Vui lòng nhập mã giảm giá!', 'warning');
            $this->redirect($redirectUrl);
        }

        $user = auth();
        $userId = $user['id'] ?? null;
        $res = $this->cart->applyVoucher($code, $userId);

        if ($res['success']) {
            set_flash('success', $res['message'], 'success');
        } else {
            set_flash('error', $res['message'], 'danger');
        }

        $this->redirect($redirectUrl);
    }

    public function removeVoucher(): void
    {
        $this->cart->removeVoucher();
        set_flash('info', 'Đã gỡ bỏ mã giảm giá.', 'info');
        $redirectUrl = $_GET['redirect_to'] ?? route_url('cart');
        $this->redirect($redirectUrl);
    }
}
