<?php

namespace App\Controllers\Admin;

use App\Core\BaseController;
use App\Models\Order;

class OrderController extends BaseController
{
    private Order $orderModel;

    public function __construct()
    {
        $this->orderModel = new Order();
    }

    public function index(): void
    {
        $orders = $this->orderModel->getAllForAdmin();

        $this->adminView('orders/index', [
            'pageTitle' => 'Quản Lý Đơn Hàng',
            'orders'    => $orders,
        ]);
    }

    public function detail(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $order = $this->orderModel->getDetail($id);

        if (!$order) {
            set_flash('error', 'Không tìm thấy đơn hàng!', 'danger');
            $this->redirect(route_url('admin-orders'));
        }

        $this->adminView('orders/detail', [
            'pageTitle' => 'Chi Tiết Đơn Hàng #' . $order['order_code'],
            'order'     => $order,
        ]);
    }

    public function updateStatus(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        $status = $_POST['order_status'] ?? 'pending';

        if ($id > 0) {
            $this->orderModel->updateStatus($id, $status);
            set_flash('success', 'Đã cập nhật trạng thái đơn hàng thành công!', 'success');
        }

        $this->redirect(route_url('admin-order-detail', ['id' => $id]));
    }
}
