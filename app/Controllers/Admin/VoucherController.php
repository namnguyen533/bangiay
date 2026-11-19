<?php

namespace App\Controllers\Admin;

use App\Core\BaseController;
use App\Models\Voucher;

class VoucherController extends BaseController
{
    private Voucher $voucherModel;

    public function __construct()
    {
        $this->voucherModel = new Voucher();
    }

    public function index(): void
    {
        $vouchers = $this->voucherModel->getAll();

        $this->adminView('vouchers/index', [
            'pageTitle' => 'Quản Lý Mã Giảm Giá (Voucher)',
            'vouchers'  => $vouchers,
        ]);
    }

    public function create(): void
    {
        $this->adminView('vouchers/create', [
            'pageTitle' => 'Thêm Mã Giảm Giá Mới',
        ]);
    }

    public function store(): void
    {
        $code = strtoupper(trim($_POST['code'] ?? ''));
        $description = trim($_POST['description'] ?? '');
        $discountType = $_POST['discount_type'] ?? 'fixed';
        $discountValue = (float)($_POST['discount_value'] ?? 0);
        $minOrderValue = (float)($_POST['min_order_value'] ?? 0);
        $maxDiscount = !empty($_POST['max_discount']) ? (float)$_POST['max_discount'] : null;
        $usageLimit = !empty($_POST['usage_limit']) ? (int)$_POST['usage_limit'] : null;
        $startsAt = !empty($_POST['starts_at']) ? $_POST['starts_at'] : null;
        $endsAt = !empty($_POST['ends_at']) ? $_POST['ends_at'] : null;
        $status = $_POST['status'] ?? 'active';

        if (empty($code) || $discountValue <= 0) {
            set_flash('error', 'Vui lòng nhập mã và giá trị giảm giá hợp lệ!', 'danger');
            $this->redirect(route_url('admin-voucher-create'));
        }

        // Kiểm tra trùng mã
        if ($this->voucherModel->findByCode($code)) {
            set_flash('error', "Mã voucher '{$code}' đã tồn tại!", 'danger');
            $this->redirect(route_url('admin-voucher-create'));
        }

        $this->voucherModel->createVoucher([
            'code'            => $code,
            'description'     => $description,
            'discount_type'   => $discountType,
            'discount_value'  => $discountValue,
            'min_order_value' => $minOrderValue,
            'max_discount'    => $maxDiscount,
            'usage_limit'     => $usageLimit,
            'used_count'      => 0,
            'starts_at'       => $startsAt,
            'ends_at'         => $endsAt,
            'status'          => $status,
        ]);

        set_flash('success', "Đã tạo mã giảm giá {$code} thành công!", 'success');
        $this->redirect(route_url('admin-vouchers'));
    }

    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $voucher = $this->voucherModel->findById($id);

        if (!$voucher) {
            set_flash('error', 'Không tìm thấy voucher!', 'danger');
            $this->redirect(route_url('admin-vouchers'));
        }

        $this->adminView('vouchers/edit', [
            'pageTitle' => 'Chỉnh Sửa Mã ' . $voucher['code'],
            'voucher'   => $voucher,
        ]);
    }

    public function update(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        $code = strtoupper(trim($_POST['code'] ?? ''));
        $description = trim($_POST['description'] ?? '');
        $discountType = $_POST['discount_type'] ?? 'fixed';
        $discountValue = (float)($_POST['discount_value'] ?? 0);
        $minOrderValue = (float)($_POST['min_order_value'] ?? 0);
        $maxDiscount = !empty($_POST['max_discount']) ? (float)$_POST['max_discount'] : null;
        $usageLimit = !empty($_POST['usage_limit']) ? (int)$_POST['usage_limit'] : null;
        $startsAt = !empty($_POST['starts_at']) ? $_POST['starts_at'] : null;
        $endsAt = !empty($_POST['ends_at']) ? $_POST['ends_at'] : null;
        $status = $_POST['status'] ?? 'active';

        if ($id <= 0 || empty($code) || $discountValue <= 0) {
            set_flash('error', 'Dữ liệu không hợp lệ!', 'danger');
            $this->redirect(route_url('admin-vouchers'));
        }

        $existing = $this->voucherModel->findByCode($code);
        if ($existing && (int)$existing['id'] !== $id) {
            set_flash('error', "Mã voucher '{$code}' đã trùng với voucher khác!", 'danger');
            $this->redirect(route_url('admin-voucher-edit', ['id' => $id]));
        }

        $this->voucherModel->updateVoucher($id, [
            'code'            => $code,
            'description'     => $description,
            'discount_type'   => $discountType,
            'discount_value'  => $discountValue,
            'min_order_value' => $minOrderValue,
            'max_discount'    => $maxDiscount,
            'usage_limit'     => $usageLimit,
            'starts_at'       => $startsAt,
            'ends_at'         => $endsAt,
            'status'          => $status,
        ]);

        set_flash('success', 'Đã cập nhật mã giảm giá thành công!', 'success');
        $this->redirect(route_url('admin-vouchers'));
    }

    public function delete(): void
    {
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id > 0) {
            $this->voucherModel->deleteVoucher($id);
            set_flash('success', 'Đã xóa mã giảm giá!', 'info');
        }
        $this->redirect(route_url('admin-vouchers'));
    }
}
