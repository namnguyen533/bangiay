<div class="container-fluid" style="max-width: 760px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Thêm Mã Giảm Giá Mới</h4>
            <p class="text-secondary small mb-0">Thiết lập chương trình khuyến mãi cho khách hàng</p>
        </div>
        <a href="<?= route_url('admin-vouchers') ?>" class="btn btn-outline-secondary rounded-pill">
            <i class="fa-solid fa-arrow-left me-1"></i> Quay Lại
        </a>
    </div>

    <div class="bg-white p-4 rounded-4 border shadow-sm">
        <form action="<?= route_url('admin-voucher-store') ?>" method="POST">
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Mã Code Voucher <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control text-uppercase fw-bold" placeholder="VÍ DỤ: SOLEVIBE20" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Loại Giảm Giá <span class="text-danger">*</span></label>
                    <select name="discount_type" id="discountType" class="form-select" onchange="toggleDiscountFields()">
                        <option value="percent">Giảm theo Phần trăm (%)</option>
                        <option value="fixed">Giảm số tiền cố định (₫)</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Mô Tả Chương Trình</label>
                <input type="text" name="description" class="form-control" placeholder="Ví dụ: Giảm 10% tối đa 100k cho đơn từ 500k">
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Giá Trị Giảm <span class="text-danger">*</span></label>
                    <input type="number" step="any" name="discount_value" class="form-control" placeholder="10 (nếu %) hoặc 50000 (nếu ₫)" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Đơn Hàng Tối Thiểu (₫)</label>
                    <input type="number" name="min_order_value" class="form-control" value="0">
                </div>
                <div class="col-md-4" id="maxDiscountCol">
                    <label class="form-label fw-bold">Giảm Tối Đa (₫)</label>
                    <input type="number" name="max_discount" class="form-control" placeholder="Để trống nếu không giới hạn">
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Giới Hạn Lượt Dùng</label>
                    <input type="number" name="usage_limit" class="form-control" placeholder="Để trống nếu vô hạn">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Ngày Bắt Đầu</label>
                    <input type="datetime-local" name="starts_at" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Ngày Hết Hạn</label>
                    <input type="datetime-local" name="ends_at" class="form-control">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Trạng Thái</label>
                <select name="status" class="form-select">
                    <option value="active" selected>Kích hoạt (Khách hàng có thể dùng)</option>
                    <option value="inactive">Tạm khóa (Tạm ẩn mã này)</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold w-100">
                <i class="fa-solid fa-save me-1"></i> Lưu Mã Giảm Giá
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
</script>
