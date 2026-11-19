<div class="container-fluid" style="max-width: 760px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Chỉnh Sửa Mã Giảm Giá: <span class="text-danger"><?= htmlspecialchars($voucher['code']) ?></span></h4>
            <p class="text-secondary small mb-0">Cập nhật thông tin chiết khấu và điều kiện áp dụng</p>
        </div>
        <a href="<?= route_url('admin-vouchers') ?>" class="btn btn-outline-secondary rounded-pill">
            <i class="fa-solid fa-arrow-left me-1"></i> Quay Lại
        </a>
    </div>

    <div class="bg-white p-4 rounded-4 border shadow-sm">
        <form action="<?= route_url('admin-voucher-update') ?>" method="POST">
            <input type="hidden" name="id" value="<?= $voucher['id'] ?>">

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Mã Code Voucher <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control text-uppercase fw-bold" value="<?= htmlspecialchars($voucher['code']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Loại Giảm Giá <span class="text-danger">*</span></label>
                    <select name="discount_type" id="discountType" class="form-select" onchange="toggleDiscountFields()">
                        <option value="percent" <?= ($voucher['discount_type'] === 'percent') ? 'selected' : '' ?>>Giảm theo Phần trăm (%)</option>
                        <option value="fixed" <?= ($voucher['discount_type'] === 'fixed') ? 'selected' : '' ?>>Giảm số tiền cố định (₫)</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Mô Tả Chương Trình</label>
                <input type="text" name="description" class="form-control" value="<?= htmlspecialchars($voucher['description'] ?? '') ?>">
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Giá Trị Giảm <span class="text-danger">*</span></label>
                    <input type="number" step="any" name="discount_value" class="form-control" value="<?= (float)$voucher['discount_value'] ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Đơn Hàng Tối Thiểu (₫)</label>
                    <input type="number" name="min_order_value" class="form-control" value="<?= (float)$voucher['min_order_value'] ?>">
                </div>
                <div class="col-md-4" id="maxDiscountCol">
                    <label class="form-label fw-bold">Giảm Tối Đa (₫)</label>
                    <input type="number" name="max_discount" class="form-control" value="<?= !empty($voucher['max_discount']) ? (float)$voucher['max_discount'] : '' ?>" placeholder="Để trống nếu không giới hạn">
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Giới Hạn Lượt Dùng</label>
                    <input type="number" name="usage_limit" class="form-control" value="<?= $voucher['usage_limit'] ?? '' ?>" placeholder="Để trống nếu vô hạn">
                    <small class="text-muted">Đã dùng: <strong><?= $voucher['used_count'] ?></strong> lượt</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Ngày Bắt Đầu</label>
                    <input type="datetime-local" name="starts_at" class="form-control" value="<?= !empty($voucher['starts_at']) ? date('Y-m-d\TH:i', strtotime($voucher['starts_at'])) : '' ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Ngày Hết Hạn</label>
                    <input type="datetime-local" name="ends_at" class="form-control" value="<?= !empty($voucher['ends_at']) ? date('Y-m-d\TH:i', strtotime($voucher['ends_at'])) : '' ?>">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Trạng Thái</label>
                <select name="status" class="form-select">
                    <option value="active" <?= ($voucher['status'] === 'active') ? 'selected' : '' ?>>Kích hoạt (Khách hàng có thể dùng)</option>
                    <option value="inactive" <?= ($voucher['status'] === 'inactive') ? 'selected' : '' ?>>Tạm khóa (Tạm ẩn mã này)</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold w-100">
                <i class="fa-solid fa-check me-1"></i> Cập Nhật Mã Giảm Giá
            </button>
        </form>
    </div>
</div>

<script>
function toggleDiscountFields() {
    const type = document.getElementById('discountType').value;
    const maxCol = document.getElementById('maxDiscountCol');
    if (type === 'fixed') {
        maxCol.style.opacity = '0.4';
    } else {
        maxCol.style.opacity = '1';
    }
}
toggleDiscountFields();
</script>
