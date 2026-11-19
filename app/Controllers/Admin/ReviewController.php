<?php

namespace App\Controllers\Admin;

use App\Core\BaseController;
use App\Models\ProductReview;

class ReviewController extends BaseController
{
    private ProductReview $reviewModel;

    public function __construct()
    {
        $this->reviewModel = new ProductReview();
    }

    public function index(): void
    {
        $this->requireAdmin();
        $status = $_GET['status'] ?? '';
        $this->adminView('reviews/index', [
            'pageTitle' => 'Đánh Giá Sản Phẩm',
            'reviews' => $this->reviewModel->getAllForAdmin($status),
            'selectedStatus' => $status,
        ]);
    }

    public function update(): void
    {
        $this->requireAdmin();
        $id = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';
        $response = trim($_POST['admin_response'] ?? '');
        if ($id <= 0 || !in_array($status, ['pending', 'visible', 'hidden'], true) || mb_strlen($response) > 3000) {
            set_flash('error', 'Trạng thái hoặc phản hồi đánh giá không hợp lệ.', 'danger');
            $this->redirect(route_url('admin-reviews'));
        }

        $this->reviewModel->moderate($id, $status, $response, (int)auth()['id']);
        set_flash('success', 'Đã cập nhật trạng thái và phản hồi đánh giá.', 'success');
        $this->redirect(route_url('admin-reviews'));
    }
}
