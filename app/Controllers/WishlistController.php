<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Wishlist;

class WishlistController extends BaseController
{
    private Wishlist $wishlistModel;

    public function __construct()
    {
        $this->wishlistModel = new Wishlist();
    }

    public function index(): void
    {
        $this->requireLogin();
        $user = auth();

        $items = $this->wishlistModel->getByUserId((int)$user['id']);

        $this->view('wishlist', [
            'pageTitle' => 'Sản Phẩm Yêu Thích - SoleVibe',
            'items'     => $items,
        ]);
    }

    public function toggle(): void
    {
        $user = auth();
        $productId = (int)($_POST['product_id'] ?? $_GET['product_id'] ?? 0);
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        if (!$user) {
            if ($isAjax) {
                $this->json([
                    'success'  => false,
                    'require_login' => true,
                    'message'  => 'Vui lòng đăng nhập để lưu sản phẩm yêu thích!',
                    'redirect' => route_url('login'),
                ]);
            }
            set_flash('error', 'Vui lòng đăng nhập để lưu sản phẩm yêu thích!', 'warning');
            $this->redirect(route_url('login'));
        }

        if ($productId <= 0) {
            if ($isAjax) {
                $this->json(['success' => false, 'message' => 'Sản phẩm không hợp lệ!']);
            }
            $this->redirect($_SERVER['HTTP_REFERER'] ?? route_url('products'));
        }

        $res = $this->wishlistModel->toggle((int)$user['id'], $productId);

        if ($isAjax) {
            $msg = ($res['action'] === 'added') 
                ? 'Đã thêm vào danh sách yêu thích!' 
                : 'Đã xóa khỏi danh sách yêu thích!';
            $this->json([
                'success' => true,
                'action'  => $res['action'],
                'count'   => $res['count'],
                'message' => $msg,
            ]);
        }

        if ($res['action'] === 'added') {
            set_flash('success', 'Đã thêm sản phẩm vào danh sách yêu thích!', 'success');
        } else {
            set_flash('info', 'Đã xóa sản phẩm khỏi danh sách yêu thích!', 'info');
        }

        $this->redirect($_SERVER['HTTP_REFERER'] ?? route_url('wishlist'));
    }

    public function remove(): void
    {
        $this->requireLogin();
        $user = auth();
        $productId = (int)($_GET['product_id'] ?? 0);

        if ($productId > 0) {
            $this->wishlistModel->remove((int)$user['id'], $productId);
            set_flash('success', 'Đã xóa sản phẩm khỏi danh sách yêu thích!', 'info');
        }

        $this->redirect(route_url('wishlist'));
    }
}
