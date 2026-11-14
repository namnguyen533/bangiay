<?php

namespace App\Core;

abstract class BaseController
{
    /**
     * Render giao diện Client
     */
    protected function view(string $viewPath, array $data = []): void
    {
        extract($data);

        $viewFile = __DIR__ . '/../Views/client/' . $viewPath . '.php';
        $header = __DIR__ . '/../Views/layouts/header.php';
        $footer = __DIR__ . '/../Views/layouts/footer.php';

        if (!file_exists($viewFile)) {
            die("Không tìm thấy file view: {$viewPath}");
        }

        require_once $header;
        require_once $viewFile;
        require_once $footer;
    }

    /**
     * Render giao diện Admin
     */
    protected function adminView(string $viewPath, array $data = []): void
    {
        $this->requireAdmin();
        extract($data);

        $viewFile = __DIR__ . '/../Views/admin/' . $viewPath . '.php';
        $header = __DIR__ . '/../Views/layouts/admin_header.php';
        $footer = __DIR__ . '/../Views/layouts/admin_footer.php';

        if (!file_exists($viewFile)) {
            die("Không tìm thấy file view Admin: {$viewPath}");
        }

        require_once $header;
        require_once $viewFile;
        require_once $footer;
    }

    protected function json(mixed $data): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function redirect(string $url): void
    {
        redirect($url);
    }

    protected function requireLogin(): void
    {
        if (!auth()) {
            set_flash('error', 'Vui lòng đăng nhập để tiếp tục!', 'danger');
            $this->redirect(route_url('login'));
        }
    }

    protected function requireAdmin(): void
    {
        $user = auth();
        if (!$user) {
            set_flash('error', 'Vui lòng đăng nhập tài khoản Quản trị viên!', 'danger');
            $this->redirect(route_url('login'));
        }

        if (!is_admin()) {
            set_flash('error', 'Bạn không có quyền truy cập trang quản trị!', 'danger');
            $this->redirect(route_url());
        }
    }
}
