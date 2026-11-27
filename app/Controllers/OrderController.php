<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Order;

class OrderController extends BaseController
{
    private Order $orderModel;

    public function __construct()
    {
        $this->orderModel = new Order();
    }

    /**
     * Lịch sử mua hàng của khách
     */
    public function myOrders(): void
    {
        $this->requireLogin();
        $user = auth();

        $orders = $this->orderModel->getUserOrders((int)$user['id']);

        $this->view('my_orders', [
            'pageTitle' => 'Lịch Sử Mua Hàng - Apple Meo Meo',
            'orders'    => $orders,
        ]);
    }

    /**
     * Chi tiết đơn hàng và tiến trình theo dõi (Tracking Timeline)
     */
    public function detail(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $user = auth();

        if ($id <= 0) {
            $this->redirect(route_url('my-orders'));
        }

        $order = null;
        if ($user) {
            $order = $this->orderModel->getOrderDetailForUser($id, (int)$user['id']);
        } else {
            // Khách vãng lai: yêu cầu số điện thoại xác thực
            $phone = trim($_GET['phone'] ?? '');
            $rawOrder = $this->orderModel->getDetail($id);
            if ($rawOrder && $rawOrder['recipient_phone'] === $phone) {
                $order = $rawOrder;
            }
        }

        if (!$order) {
            set_flash('error', 'Không tìm thấy thông tin đơn hàng hoặc bạn không có quyền xem!', 'danger');
            $this->redirect(route_url());
        }

        $this->view('order_detail', [
            'pageTitle' => 'Chi Tiết Đơn Hàng #' . $order['order_code'] . ' - Apple Meo Meo',
            'order'     => $order,
        ]);
    }

    /**
     * Tra cứu đơn hàng nhanh theo Mã đơn + SĐT
     */
    public function track(): void
    {
        $orderCode = trim($_GET['order_code'] ?? $_POST['order_code'] ?? '');
        $phone = trim($_GET['phone'] ?? $_POST['phone'] ?? '');
        $order = null;
        $searched = false;

        if (!empty($orderCode)) {
            $searched = true;
            $order = $this->orderModel->getOrderByCode($orderCode, $phone ?: null);
            if (!$order) {
                set_flash('error', 'Không tìm thấy đơn hàng phù hợp với thông tin đã nhập!', 'danger');
            }
        }

        $this->view('track_order', [
            'pageTitle' => 'Theo Dõi & Tra Cứu Đơn Hàng - Apple Meo Meo',
            'order'     => $order,
            'orderCode' => $orderCode,
            'phone'     => $phone,
            'searched'  => $searched,
        ]);
    }

    /**
     * Hủy đơn hàng (khi đơn chưa được giao) và hoàn kho
     */
    public function cancel(): void
    {
        $this->requireLogin();
        $user = auth();

        $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
        $reason = $_POST['reason'] ?? null;

        if ($id === false || $id <= 0) {
            set_flash('error', 'Thông tin đơn hàng không hợp lệ.', 'danger');
            $this->redirect(route_url('my-orders'));
        }

        if (!is_string($reason) || trim($reason) === '' || mb_strlen(trim($reason)) > 500) {
            set_flash('error', 'Vui lòng nhập lý do hủy từ 1 đến 500 ký tự.', 'danger');
            $this->redirect(route_url('order-detail', ['id' => $id]));
        }

        $result = $this->orderModel->cancelOrder($id, (int)$user['id'], trim($reason));

        if ($result['success']) {
            set_flash('success', $result['message'], 'success');
        } else {
            set_flash('error', $result['message'], 'danger');
        }

        $this->redirect(route_url('order-detail', ['id' => $id]));
    }
}
