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
        $status = $_GET['status'] ?? 'all';
        $keyword = trim($_GET['q'] ?? '');

        $orders = $this->orderModel->getAllForAdmin($status, $keyword);

        $this->adminView('orders/index', [
            'pageTitle'      => 'Quản Lý Đơn Hàng',
            'orders'         => $orders,
            'currentStatus'  => $status,
            'keyword'        => $keyword,
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
        $paymentStatus = $_POST['payment_status'] ?? null;

        if ($id > 0) {
            if (!$this->orderModel->updateStatus($id, $status)) {
                set_flash('error', 'Không thể cập nhật đơn hàng không tồn tại hoặc đã hủy.', 'danger');
            } else {
                $paymentWasSetPaidOnCompletion = $status === 'completed' && $paymentStatus === 'unpaid';
                if ($paymentStatus && !$paymentWasSetPaidOnCompletion) {
                    if (!$this->orderModel->updatePaymentStatus($id, $paymentStatus)) {
                        set_flash('error', 'Đơn hàng đã cập nhật nhưng trạng thái thanh toán chưa được cập nhật.', 'danger');
                        $this->redirect(route_url('admin-order-detail', ['id' => $id]));
                        return;
                    }
                }
                set_flash('success', 'Đã cập nhật trạng thái đơn hàng thành công!', 'success');
            }
        }

        $this->redirect(route_url('admin-order-detail', ['id' => $id]));
    }

    /**
     * Mẫu in hóa đơn chuyên nghiệp
     */
    public function print(): void
    {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        $order = $this->orderModel->getDetail($id);

        if (!$order) {
            die('Không tìm thấy đơn hàng để in hóa đơn!');
        }

        extract(['order' => $order]);
        require_once __DIR__ . '/../../Views/admin/orders/print.php';
        exit;
    }
}
