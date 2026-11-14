<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Cart;
use App\Models\Order;
use Exception;

class CheckoutController extends BaseController
{
    private Cart $cart;
    private Order $orderModel;

    public function __construct()
    {
        $this->cart = new Cart();
        $this->orderModel = new Order();
    }

    public function index(): void
    {
        $items = $this->cart->getItems();
        if (empty($items)) {
            set_flash('error', 'Giỏ hàng của bạn đang trống!', 'warning');
            $this->redirect(route_url('products'));
        }

        $subtotal = $this->cart->getTotalAmount();
        $shippingFee = ($subtotal >= 2000000 || $subtotal == 0) ? 0 : 30000;
        $totalAmount = $subtotal + $shippingFee;

        $user = auth();

        $this->view('checkout', [
            'pageTitle'   => 'Thanh Toán Đơn Hàng - SoleVibe',
            'items'       => $items,
            'subtotal'    => $subtotal,
            'shippingFee' => $shippingFee,
            'totalAmount' => $totalAmount,
            'user'        => $user,
        ]);
    }

    public function process(): void
    {
        $items = $this->cart->getItems();
        if (empty($items)) {
            $this->redirect(route_url('products'));
        }

        $recipientName = trim($_POST['recipient_name'] ?? '');
        $recipientPhone = trim($_POST['recipient_phone'] ?? '');
        $shippingAddress = trim($_POST['shipping_address'] ?? '');
        $paymentMethod = $_POST['payment_method'] ?? 'cod';
        $note = trim($_POST['note'] ?? '');

        if (empty($recipientName) || empty($recipientPhone) || empty($shippingAddress)) {
            set_flash('error', 'Vui lòng điền đầy đủ họ tên, số điện thoại và địa chỉ giao hàng!', 'danger');
            $this->redirect(route_url('checkout'));
        }

        $subtotal = $this->cart->getTotalAmount();
        $shippingFee = ($subtotal >= 2000000) ? 0 : 30000;
        $totalAmount = $subtotal + $shippingFee;

        $user = auth();
        $userId = $user['id'] ?? null;

        $orderCode = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        $orderData = [
            'order_code'       => $orderCode,
            'user_id'          => $userId,
            'recipient_name'   => $recipientName,
            'recipient_phone'  => $recipientPhone,
            'shipping_address' => $shippingAddress,
            'subtotal'         => $subtotal,
            'discount_amount'  => 0,
            'shipping_fee'     => $shippingFee,
            'total_amount'     => $totalAmount,
            'payment_method'   => $paymentMethod,
            'payment_status'   => ($paymentMethod === 'banking') ? 'pending' : 'unpaid',
            'order_status'     => 'pending',
            'note'             => $note,
        ];

        try {
            $orderId = $this->orderModel->createOrder($orderData, $items);
            $this->cart->clear();

            set_flash('order_success', $orderCode);
            $this->redirect(route_url('order-success', ['id' => $orderId]));
        } catch (Exception $e) {
            set_flash('error', 'Lỗi khi tạo đơn hàng: ' . $e->getMessage(), 'danger');
            $this->redirect(route_url('checkout'));
        }
    }

    public function success(): void
    {
        $orderId = (int)($_GET['id'] ?? 0);
        $order = $this->orderModel->getDetail($orderId);

        if (!$order) {
            $this->redirect(route_url());
        }

        $this->view('success', [
            'pageTitle' => 'Đặt Hàng Thành Công - SoleVibe',
            'order'     => $order,
        ]);
    }
}
