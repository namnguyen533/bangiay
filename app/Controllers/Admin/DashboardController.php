<?php

namespace App\Controllers\Admin;

use App\Core\BaseController;
use App\Models\Order;

class DashboardController extends BaseController
{
    public function index(): void
    {
        $orderModel = new Order();
        $stats = $orderModel->getDashboardStats();

        $this->adminView('dashboard', [
            'pageTitle' => 'Bảng Quản Trị - Thống Kê Tổng Quan',
            'stats'     => $stats,
        ]);
    }
}
