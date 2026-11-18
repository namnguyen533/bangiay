<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Product;
use App\Models\ProductReview;
use PDOException;

class ReviewController extends BaseController
{
    public function submit(): void
    {
        $this->requireLogin();
        $productId = (int)($_POST['product_id'] ?? 0);
        $product = (new Product())->getDetail($productId);
        if (!$product || $product['status'] !== 'active') {
            set_flash('error', 'Không tìm thấy sản phẩm đang bán để đánh giá.', 'danger');
            $this->redirect(route_url('products'));
        }

        $rating = filter_var($_POST['rating'] ?? null, FILTER_VALIDATE_INT);
        $content = trim($_POST['content'] ?? '');
        if ($rating === false || $rating < 1 || $rating > 5 || $content === '' || mb_strlen($content) > 3000) {
            set_flash('error', 'Đánh giá cần có số sao từ 1 đến 5 và nội dung tối đa 3.000 ký tự.', 'danger');
            $this->redirect(route_url('product-detail', ['id' => $productId]));
        }

        try {
            (new ProductReview())->create($productId, (int)auth()['id'], $rating, $content);
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                set_flash('error', 'Bạn đã gửi đánh giá cho sản phẩm này rồi.', 'warning');
                $this->redirect(route_url('product-detail', ['id' => $productId]));
            }
            error_log('Product review submission failed: ' . $exception->getMessage());
            set_flash('error', 'Không thể lưu đánh giá lúc này. Vui lòng thử lại.', 'danger');
            $this->redirect(route_url('product-detail', ['id' => $productId]));
        }

        set_flash('success', 'Đánh giá đã được gửi và đang chờ cửa hàng duyệt.', 'success');
        $this->redirect(route_url('product-detail', ['id' => $productId]));
    }
}
