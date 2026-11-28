<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Cart;
use App\Models\Order;
use App\Models\UserAddress;
use App\Models\Voucher;
use App\Core\OrderMailer;
use Exception;

class CheckoutController extends BaseController
{
    private Cart $cart;
    private Order $orderModel;
    private UserAddress $addressModel;
    private Voucher $voucherModel;

    public function __construct()
    {
        $this->cart = new Cart();
        $this->orderModel = new Order();
        $this->addressModel = new UserAddress();
        $this->voucherModel = new Voucher();
    }

    public function index(): void
    {
        if (!auth()) {
            $_SESSION['login_return_url'] = route_url('checkout');
            set_flash('warning', 'Vui lòng đăng nhập để tiến hành đặt hàng!', 'warning');
            $this->redirect(route_url('login'));
        }

        $items = $this->cart->getItems();
        if (empty($items)) {
            set_flash('error', 'Giỏ hàng của bạn đang trống!', 'warning');
            $this->redirect(route_url('products'));
        }

        // Kiểm tra tồn kho trước khi vào trang thanh toán
        $stockErrors = $this->cart->validateInventory();
        if (!empty($stockErrors)) {
            set_flash('error', implode('<br>', $stockErrors), 'danger');
            $this->redirect(route_url('cart'));
        }

        $subtotal = $this->cart->getTotalAmount();
        $shippingFee = $this->cart->getShippingFee();
        $appliedVoucher = $this->cart->getVoucher();
        $discountAmount = $this->cart->getDiscountAmount();
        $finalTotal = $this->cart->getFinalTotal();

        $user = auth();
        $savedAddresses = [];
        $defaultAddress = null;

        if ($user) {
            $savedAddresses = $this->addressModel->getByUserId((int)$user['id']);
            $defaultAddress = $this->addressModel->getDefault((int)$user['id']);
        }

        $availableVouchers = $this->voucherModel->getActiveVouchers();

        $this->view('checkout', [
            'pageTitle'         => 'Thanh Toán Đơn Hàng - Apple Meo Meo',
            'items'             => $items,
            'subtotal'          => $subtotal,
            'shippingFee'       => $shippingFee,
            'appliedVoucher'    => $appliedVoucher,
            'discountAmount'    => $discountAmount,
            'finalTotal'        => $finalTotal,
            'user'              => $user,
            'savedAddresses'    => $savedAddresses,
            'defaultAddress'    => $defaultAddress,
            'availableVouchers' => $availableVouchers,
        ]);
    }

    public function process(): void
    {
        if (!auth()) {
            $_SESSION['login_return_url'] = route_url('checkout');
            set_flash('warning', 'Vui lòng đăng nhập để đặt hàng!', 'warning');
            $this->redirect(route_url('login'));
        }

        $items = $this->cart->getItems();
        if (empty($items)) {
            $this->redirect(route_url('products'));
        }

        // 1. Kiểm tra tồn kho lần cuối
        $stockErrors = $this->cart->validateInventory();
        if (!empty($stockErrors)) {
            set_flash('error', implode('<br>', $stockErrors), 'danger');
            $this->redirect(route_url('cart'));
        }

        $user = auth();
        $userId = $user['id'] ?? null;

        $recipientName = '';
        $recipientPhone = '';
        $shippingAddress = '';

        $addressChoice = $_POST['address_choice'] ?? 'new';
        $selectedAddressId = (int)($_POST['selected_address_id'] ?? 0);

        if ($user && $addressChoice === 'saved' && $selectedAddressId > 0) {
            $addr = $this->addressModel->findById($selectedAddressId, (int)$userId);
            if ($addr) {
                $recipientName = $addr['recipient_name'];
                $recipientPhone = $addr['phone'];
                $shippingAddress = "{$addr['address_line']}, {$addr['ward']}, {$addr['district']}, {$addr['province']}";
            }
        }

        if (empty($recipientName)) {
            $recipientName = trim($_POST['recipient_name'] ?? '');
            $recipientPhone = trim($_POST['recipient_phone'] ?? '');
            
            $province = trim($_POST['province'] ?? '');
            $district = trim($_POST['district'] ?? '');
            $ward = trim($_POST['ward'] ?? '');
            $addressLine = trim($_POST['address_line'] ?? '');

            if (!empty($province) && !empty($district) && !empty($addressLine)) {
                $shippingAddress = "{$addressLine}, {$ward}, {$district}, {$province}";
            } else {
                $shippingAddress = trim($_POST['shipping_address'] ?? '');
            }

            // Lưu địa chỉ mới nếu người dùng tick chọn
            if ($user && !empty($_POST['save_address']) && !empty($province) && !empty($district) && !empty($addressLine)) {
                $this->addressModel->createAddress([
                    'user_id'        => $userId,
                    'recipient_name' => $recipientName,
                    'phone'          => $recipientPhone,
                    'province'       => $province,
                    'district'       => $district,
                    'ward'           => $ward,
                    'address_line'   => $addressLine,
                    'is_default'     => !empty($_POST['is_default_address']) ? 1 : 0,
                ]);
            }
        }

        if (empty($recipientName) || empty($recipientPhone) || empty($shippingAddress)) {
            set_flash('error', 'Vui lòng cung cấp đầy đủ họ tên, số điện thoại và địa chỉ giao hàng!', 'danger');
            $this->redirect(route_url('checkout'));
        }

        $paymentMethod = $_POST['payment_method'] ?? 'cod';
        $note = trim($_POST['note'] ?? '');

        // Tính tổng tiền & giảm giá
        $subtotal = $this->cart->getTotalAmount();
        $shippingFee = $this->cart->getShippingFee();
        $appliedVoucher = $this->cart->getVoucher();
        $discountAmount = $this->cart->getDiscountAmount();
        $totalAmount = $this->cart->getFinalTotal();

        $orderCode = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        $orderData = [
            'order_code'       => $orderCode,
            'user_id'          => $userId,
            'voucher_id'       => $appliedVoucher['id'] ?? null,
            'recipient_name'   => $recipientName,
            'recipient_phone'  => $recipientPhone,
            'shipping_address' => $shippingAddress,
            'subtotal'         => $subtotal,
            'discount_amount'  => $discountAmount,
            'shipping_fee'     => $shippingFee,
            'total_amount'     => $totalAmount,
            'payment_method'   => $paymentMethod,
            'payment_status'   => ($paymentMethod === 'cod') ? 'unpaid' : 'pending',
            'order_status'     => 'pending',
            'note'             => $note,
        ];

        try {
            $orderId = $this->orderModel->createOrder($orderData, $items);
            $this->cart->clear();

            $createdOrder = $this->orderModel->getDetail($orderId);
            if ($createdOrder) {
                OrderMailer::sendToAdmins(
                    $createdOrder,
                    'Đơn hàng mới #' . $createdOrder['order_code'],
                    'Có đơn hàng mới vừa được khách hàng đặt.'
                );
                OrderMailer::sendToUser(
                    $createdOrder,
                    'Đặt hàng thành công #' . $createdOrder['order_code'],
                    'Cảm ơn bạn đã đặt hàng. Đơn hàng của bạn đang chờ quản trị viên xác nhận.'
                );
            }

            // Nếu thanh toán online (VNPay / MoMo / Banking QR) -> chuyển sang trang cổng thanh toán
            if (in_array($paymentMethod, ['vnpay', 'momo', 'banking'])) {
                $this->redirect(route_url('payment-gateway', ['id' => $orderId]));
            }

            set_flash('order_success', $orderCode);
            $this->redirect(route_url('order-success', ['id' => $orderId]));
        } catch (Exception $e) {
            set_flash('error', 'Lỗi khi tạo đơn hàng: ' . $e->getMessage(), 'danger');
            $this->redirect(route_url('checkout'));
        }
    }

    /**
     * Màn hình cổng thanh toán Online (VNPay / MoMo / VietQR)
     */
    public function paymentGateway(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $order = $this->orderModel->getDetail($id);

        if (!$order) {
            set_flash('error', 'Không tìm thấy đơn hàng!', 'danger');
            $this->redirect(route_url());
        }

        $this->view('payment_gateway', [
            'pageTitle' => 'Thanh Toán Đơn Hàng #' . $order['order_code'],
            'order'     => $order,
        ]);
    }

    /**
     * Xác nhận thanh toán thành công (Mô phỏng IPN / Callback từ Cổng thanh toán)
     */
    public function confirmPayment(): void
    {
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        $order = $this->orderModel->getDetail($id);

        if ($order) {
            $this->orderModel->updatePaymentStatus($id, 'paid');
            set_flash('order_success', $order['order_code']);
            set_flash('success', 'Thanh toán online thành công! Đơn hàng của bạn đã được xác nhận.', 'success');
            $this->redirect(route_url('order-success', ['id' => $id]));
        }

        $this->redirect(route_url());
    }

    public function success(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $order = $this->orderModel->getDetail($id);

        if (!$order) {
            $this->redirect(route_url());
        }

        $this->view('success', [
            'pageTitle' => 'Đặt Hàng Thành Công - Apple Meo Meo',
            'order'     => $order,
        ]);
    }
}
