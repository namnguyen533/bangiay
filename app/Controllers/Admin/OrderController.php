<?php

namespace App\Controllers\Admin;

use App\Core\BaseController;
use App\Core\OrderMailer;
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
            if ($status === 'cancelled') {
                $reason = trim((string)($_POST['cancellation_reason'] ?? ''));
                $result = $this->orderModel->cancelOrderByAdmin($id, $reason);
                if (!$result['success']) {
                    set_flash('error', $result['message'], 'danger');
                    $this->redirect(route_url('admin-order-detail', ['id' => $id]));
                }
                $order = $this->orderModel->getDetail($id);
                if ($order) OrderMailer::sendToUser($order, 'Đơn hàng đã bị hủy #' . $order['order_code'], 'Đơn hàng của bạn đã bị quản trị viên hủy.', $reason);
                set_flash('success', $result['message'], 'success');
                $this->redirect(route_url('admin-order-detail', ['id' => $id]));
            }

            $before = $this->orderModel->getDetail($id);
            if (!$this->orderModel->updateStatus($id, $status)) {
                set_flash('error', 'Chỉ có thể chuyển trạng thái đơn hàng theo từng bước.', 'danger');
            } else {
                if ($paymentStatus) {
                    $this->orderModel->updatePaymentStatus($id, $paymentStatus);
                }
                $after = $this->orderModel->getDetail($id);
                if ($after) OrderMailer::sendToUser(
                    $after,
                    'Cập nhật đơn hàng #' . $after['order_code'],
                    'Trạng thái đơn hàng của bạn đã được thay đổi từ "' . order_status_label((string)($before['order_status'] ?? 'pending')) . '" sang "' . order_status_label($status) . '".'
                );
                set_flash('success', 'Đã cập nhật trạng thái đơn hàng thành công!', 'success');
            }
        }

        $this->redirect(route_url('admin-order-detail', ['id' => $id]));
    }

    public function approveCancellation(): void
    {
        $this->requireAdmin();
        $id = (int)($_POST['id'] ?? 0);
        $result = $this->orderModel->approveCancellation($id);
        $order = $this->orderModel->getDetail($id);
        if ($order) {
            OrderMailer::sendToUser($order, 'Yêu cầu hủy đơn được chấp nhận #' . $order['order_code'], 'Quản trị viên đã đồng ý yêu cầu hủy đơn hàng của bạn.');
        }
        set_flash($result['success'] ? 'success' : 'error', $result['message'], $result['success'] ? 'success' : 'danger');
        $this->redirect(route_url('admin-order-detail', ['id' => $id]));
    }

    public function rejectCancellation(): void
    {
        $this->requireAdmin();
        $id = (int)($_POST['id'] ?? 0);
        $reason = trim((string)($_POST['reason'] ?? ''));
        $result = $this->orderModel->rejectCancellation($id, $reason);
        $order = $this->orderModel->getDetail($id);
        if ($order) {
            OrderMailer::sendToUser($order, 'Yêu cầu hủy đơn bị từ chối #' . $order['order_code'], 'Quản trị viên đã từ chối yêu cầu hủy đơn hàng của bạn.', $reason);
        }
        set_flash($result['success'] ? 'success' : 'error', $result['message'], $result['success'] ? 'success' : 'danger');
        $this->redirect(route_url('admin-order-detail', ['id' => $id]));
    }

    public function approveReturn(): void
    {
        $this->requireAdmin();
        $id = (int)($_POST['id'] ?? 0);
        $result = $this->orderModel->approveReturn($id);
        $order = $this->orderModel->getDetail($id);
        if ($order) OrderMailer::sendToUser($order, 'Yêu cầu đổi/trả được chấp nhận #' . $order['order_code'], 'Quản trị viên đã đồng ý yêu cầu đổi/trả của bạn.');
        set_flash($result['success'] ? 'success' : 'error', $result['message'], $result['success'] ? 'success' : 'danger');
        $this->redirect(route_url('admin-order-detail', ['id' => $id]));
    }

    public function rejectReturn(): void
    {
        $this->requireAdmin();
        $id = (int)($_POST['id'] ?? 0);
        $reason = trim((string)($_POST['reason'] ?? ''));
        $result = $this->orderModel->rejectReturn($id, $reason);
        $order = $this->orderModel->getDetail($id);
        if ($order) OrderMailer::sendToUser($order, 'Yêu cầu đổi/trả bị từ chối #' . $order['order_code'], 'Quản trị viên đã từ chối yêu cầu đổi/trả của bạn.', $reason);
        set_flash($result['success'] ? 'success' : 'error', $result['message'], $result['success'] ? 'success' : 'danger');
        $this->redirect(route_url('admin-order-detail', ['id' => $id]));
    }

    public function confirmReturnReceived(): void
    {
        $this->requireAdmin();
        $id = (int)($_POST['id'] ?? 0);
        $result = $this->orderModel->confirmReturnReceived($id);
        $order = $this->orderModel->getDetail($id);
        if ($order) OrderMailer::sendToUser($order, 'Đã nhận hàng trả lại #' . $order['order_code'], 'Quản trị viên đã xác nhận nhận lại hàng trả. Tiền sẽ được hoàn theo chính sách.');
        set_flash($result['success'] ? 'success' : 'error', $result['message'], $result['success'] ? 'success' : 'danger');
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
