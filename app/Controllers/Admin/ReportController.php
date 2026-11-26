<?php

namespace App\Controllers\Admin;

use App\Core\BaseController;
use App\Models\Order;

class ReportController extends BaseController
{
    private Order $orderModel;

    public function __construct()
    {
        $this->orderModel = new Order();
    }

    public function index(): void
    {
        $currentYear = (int)date('Y');
        $revenueDays = $this->orderModel->getRevenueByDays(15);
        $revenueMonths = $this->orderModel->getRevenueByMonth($currentYear);
        $statusDistribution = $this->orderModel->getOrderStatusDistribution();
        $topProducts = $this->orderModel->getTopSellingProducts(8);
        $categorySales = $this->orderModel->getSalesByCategory();

        $stats = $this->orderModel->getDashboardStats();

        $this->adminView('reports/index', [
            'pageTitle'          => 'Báo Cáo Thống Kê & Doanh Thu - SoleVibe Admin',
            'stats'              => $stats,
            'revenueDays'        => $revenueDays,
            'revenueMonths'      => $revenueMonths,
            'statusDistribution'=> $statusDistribution,
            'topProducts'        => $topProducts,
            'categorySales'      => $categorySales,
            'currentYear'        => $currentYear,
        ]);
    }

    /**
     * Xuất báo cáo doanh thu ra file CSV hỗ trợ tiếng Việt trên Excel
     */
    public function export(): void
    {
        $this->requireAdmin();

        $orders = $this->orderModel->getAllForAdmin();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=Bao_Cao_Doanh_Thu_SoleVibe_' . date('Ymd_His') . '.csv');

        $output = fopen('php://output', 'w');
        // Ghi BOM UTF-8 để mở trên Excel không bị lỗi font tiếng Việt
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($output, [
            'Mã đơn hàng',
            'Ngày đặt',
            'Tên khách hàng',
            'Số điện thoại',
            'Địa chỉ',
            'Tạm tính (₫)',
            'Giảm giá (₫)',
            'Phí ship (₫)',
            'Tổng thanh toán (₫)',
            'Phương thức',
            'Trạng thái đơn',
            'Trạng thái TT',
        ]);

        foreach ($orders as $ord) {
            fputcsv($output, [
                $ord['order_code'],
                $ord['created_at'],
                $ord['recipient_name'],
                $ord['recipient_phone'],
                $ord['shipping_address'],
                $ord['subtotal'],
                $ord['discount_amount'],
                $ord['shipping_fee'],
                $ord['total_amount'],
                strtoupper($ord['payment_method']),
                order_status_label($ord['order_status']),
                $ord['payment_status'] === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán',
            ]);
        }

        fclose($output);
        exit;
    }
}
