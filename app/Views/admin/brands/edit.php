<div class="container-fluid" style="max-width: 700px;">
    <div class="bg-white p-4 rounded-4 border shadow-sm">
        <h4 class="fw-bold mb-4">Chỉnh sửa thương hiệu</h4>
        <form action="<?= route_url('admin-brand-update') ?>" method="POST">
            <input type="hidden" name="id" value="<?= (int)$brand['id'] ?>">
            <div class="mb-3">
                <label class="form-label fw-bold" for="brandName">Tên thương hiệu</label>
                <input id="brandName" name="name" class="form-control" value="<?= htmlspecialchars($brand['name']) ?>" required maxlength="120">
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold" for="brandStatus">Trạng thái</label>
                <select id="brandStatus" name="status" class="form-select">
                    <option value="active" <?= $brand['status'] === 'active' ? 'selected' : '' ?>>Đang hiển thị</option>
                    <option value="inactive" <?= $brand['status'] === 'inactive' ? 'selected' : '' ?>>Tạm ẩn</option>
                </select>
            </div>
            <button class="btn btn-primary" type="submit">Lưu thay đổi</button>
            <a class="btn btn-outline-secondary" href="<?= route_url('admin-brands') ?>">Quay lại</a>
        </form>
    </div>
</div>
