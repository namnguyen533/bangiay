<?php

declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('ROOT_PATH', __DIR__);

// Đăng ký Autoloader cho namespace App\
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = ROOT_PATH . '/app/' . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// Nạp các hàm trợ giúp (Helper)
require_once ROOT_PATH . '/app/Core/Helper.php';

// Khởi chạy Router điều hướng ứng dụng
$router = new App\Core\Router();
$router->dispatch();
