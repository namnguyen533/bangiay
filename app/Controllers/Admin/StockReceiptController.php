<?php

namespace App\Controllers\Admin;

use App\Core\BaseController;
use App\Models\ProductVariant;
use App\Models\StockReceipt;
use InvalidArgumentException;
use PDOException;
use RuntimeException;

class StockReceiptController extends BaseController
{
    private StockReceipt $receiptModel;
    private ProductVariant $variantModel;

    public function __construct()
    {
        $this->receiptModel = new StockReceipt();
        $this->variantModel = new ProductVariant();
    }

    public function index(): void
    {
        $this->requireAdmin();
        $this->adminView('inventory/index', [
            'pageTitle' => 'Tồn Kho & Phiếu Nhập',
            'variants' => $this->variantModel->getAllForInventory(),
            'lowStock' => $this->variantModel->getLowStock(5),
            'receipts' => $this->receiptModel->getRecentWithItems(),
            'threshold' => 5,
        ]);
    }

    public function store(): void
    {
        $this->requireAdmin();
        $variantIds = $_POST['variant_id'] ?? [];
        $quantities = $_POST['quantity'] ?? [];
        $unitCosts = $_POST['unit_cost'] ?? [];
        if (!is_array($variantIds) || !is_array($quantities) || !is_array($unitCosts)) {
            set_flash('error', 'Dữ liệu phiếu nhập không hợp lệ.', 'danger');
            $this->redirect(route_url('admin-inventory'));
        }

        $items = [];
        foreach ($variantIds as $index => $variantId) {
            $items[] = [
                'variant_id' => $variantId,
                'quantity' => $quantities[$index] ?? null,
                'unit_cost' => $unitCosts[$index] ?? null,
            ];
        }

        try {
            $code = $this->receiptModel->receive(
                (int)auth()['id'],
                trim($_POST['supplier'] ?? '') ?: null,
                trim($_POST['note'] ?? '') ?: null,
                $items
            );
        } catch (InvalidArgumentException | RuntimeException $exception) {
            set_flash('error', $exception->getMessage(), 'danger');
            $this->redirect(route_url('admin-inventory'));
        } catch (PDOException $exception) {
            error_log('Stock receipt creation failed: ' . $exception->getMessage());
            set_flash('error', 'Không thể lưu phiếu nhập. Vui lòng kiểm tra cấu trúc database.', 'danger');
            $this->redirect(route_url('admin-inventory'));
        }

        set_flash('success', 'Đã lưu phiếu nhập ' . $code . ' và cộng tồn kho cho các biến thể.', 'success');
        $this->redirect(route_url('admin-inventory'));
    }
}
