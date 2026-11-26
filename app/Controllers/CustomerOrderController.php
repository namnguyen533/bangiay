<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Order;
use DomainException;

class CustomerOrderController extends BaseController
{
    private Order $orderModel;

    public function __construct()
    {
        $this->orderModel = new Order();
    }

    public function index(): void
    {
        $this->requireLogin();
        $userId = (int)(auth()['id'] ?? 0);

        $this->view('orders', [
            'pageTitle' => 'Đơn Hàng Của Tôi - SoleVibe',
            'orders' => $this->orderModel->getAllForUser($userId),
        ]);
    }

    public function cancel(): void
    {
        $this->requireLogin();
        $orderId = filter_var($_POST['order_id'] ?? '', FILTER_VALIDATE_INT);
        $reason = $_POST['reason'] ?? null;

        if ($orderId === false || $orderId <= 0 || !is_string($reason)) {
            set_flash('error', 'Thông tin hủy đơn không hợp lệ.', 'danger');
            $this->redirect(route_url('my-orders'));
        }

        $reason = trim($reason);
        if ($reason === '' || mb_strlen($reason) > 500) {
            set_flash('error', 'Vui lòng nhập lý do hủy từ 1 đến 500 ký tự.', 'danger');
            $this->redirect(route_url('my-orders'));
        }

        try {
            $this->orderModel->cancelForUser($orderId, (int)(auth()['id'] ?? 0), $reason);
            set_flash('success', 'Đơn hàng đã được hủy và lý do của bạn đã được lưu.', 'success');
        } catch (DomainException $exception) {
            set_flash('error', $exception->getMessage(), 'danger');
        }

        $this->redirect(route_url('my-orders'));
    }
}
