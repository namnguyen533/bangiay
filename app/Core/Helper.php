<?php

// Tự động xác định BASE_URL nếu chưa cấu hình
if (!function_exists('base_url')) {
    function base_url(string $path = ''): string
    {
        static $baseUrl = null;
        if ($baseUrl === null) {
            $config = require __DIR__ . '/../../config/app.php';
            if (!empty($config['base_url'])) {
                $baseUrl = rtrim($config['base_url'], '/');
            } elseif (PHP_SAPI === 'cli') {
                $baseUrl = 'http://localhost/www/ban%20giay2';
            } else {
                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
                $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                $scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
                $scriptDir = ($scriptName === '/' || $scriptName === '.') ? '' : $scriptName;
                $baseUrl = rtrim($protocol . $host . $scriptDir, '/');
            }
        }
        return $baseUrl . ($path ? '/' . ltrim($path, '/') : '');
    }
}

if (!function_exists('route_url')) {
    function route_url(string $act = '', array $params = []): string
    {
        $query = [];
        if ($act !== '') {
            $query['act'] = $act;
        }
        foreach ($params as $k => $v) {
            $query[$k] = $v;
        }
        $queryString = http_build_query($query);
        return base_url($queryString ? 'index.php?' . $queryString : '');
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return base_url('public/' . ltrim($path, '/'));
    }
}

if (!function_exists('image_src')) {
    function image_src(string $path): string
    {
        return preg_match('/\Ahttps?:\/\//i', $path) ? $path : base_url($path);
    }
}

if (!function_exists('format_price')) {
    function format_price($price): string
    {
        return number_format((float)$price, 0, ',', '.') . ' ₫';
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}

if (!function_exists('set_flash')) {
    function set_flash(string $key, string $message, string $type = 'success'): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['flash'][$key] = [
            'message' => $message,
            'type'    => $type
        ];
    }
}

if (!function_exists('get_flash')) {
    function get_flash(string $key): ?array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['flash'][$key])) {
            $flash = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $flash;
        }
        return null;
    }
}

if (!function_exists('auth')) {
    function auth(): ?array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['user'] ?? null;
    }
}

if (!function_exists('is_admin')) {
    function is_admin(): bool
    {
        $user = auth();
        return $user && ($user['role'] === 'admin' || (isset($user['roles']) && in_array('admin', $user['roles'])));
    }
}

if (!function_exists('order_status_label')) {
    function order_status_label(string $status): string
    {
        $labels = [
            'pending'   => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'processing'=> 'Đang đóng gói',
            'shipping'  => 'Đang giao hàng',
            'completed' => 'Đã giao thành công',
            'cancelled' => 'Đã hủy',
            'returned'  => 'Đã trả hàng',
            'return_requested' => 'Yêu cầu đổi/trả',
        ];
        return $labels[$status] ?? ucfirst($status);
    }
}

if (!function_exists('order_status_badge')) {
    function order_status_badge(string $status): string
    {
        $classes = [
            'pending'   => 'bg-warning text-dark',
            'confirmed' => 'bg-info text-dark',
            'processing'=> 'bg-secondary text-white',
            'shipping'  => 'bg-primary text-white',
            'completed' => 'bg-success text-white',
            'cancelled' => 'bg-danger text-white',
            'returned'  => 'bg-dark text-white',
            'return_requested' => 'bg-warning text-dark',
        ];
        return $classes[$status] ?? 'bg-secondary text-white';
    }
}

if (!function_exists('payment_method_label')) {
    function payment_method_label(string $method): string
    {
        $methods = [
            'cod'     => 'Thanh toán khi nhận hàng (COD)',
            'banking' => 'Chuyển khoản VietQR',
            'vnpay'   => 'Cổng thanh toán VNPay',
            'momo'    => 'Ví điện tử MoMo',
        ];
        return $methods[$method] ?? strtoupper($method);
    }
}

if (!function_exists('payment_status_label')) {
    function payment_status_label(?string $status): string
    {
        $statuses = [
            'unpaid'             => 'Chưa thanh toán',
            'pending'            => 'Đang chờ xử lý',
            'paid'               => 'Đã thanh toán',
            'failed'             => 'Thanh toán thất bại',
            'refunded'           => 'Đã hoàn tiền',
            'partially_refunded' => 'Hoàn tiền một phần',
        ];
        return $statuses[$status] ?? 'Không xác định';
    }
}

if (!function_exists('wishlist_count')) {
    function wishlist_count(): int
    {
        $user = auth();
        if (!$user) {
            return 0;
        }
        static $count = null;
        if ($count === null) {
            $model = new \App\Models\Wishlist();
            $count = $model->countByUserId((int)$user['id']);
        }
        return $count;
    }
}
