<div class="container-fluid" style="max-width: 700px;">
    <div class="bg-white p-4 rounded-4 border shadow-sm">
        <h4 class="fw-bold mb-4">Chỉnh sửa danh mục</h4>
        <form action="<?= route_url('admin-category-update') ?>" method="POST">
            <input type="hidden" name="id" value="<?= (int)$category['id'] ?>">
            <div class="mb-3">
                <label class="form-label fw-bold" for="categoryName">Tên danh mục *</label>
                <input id="categoryName" name="name" class="form-control" value="<?= htmlspecialchars($category['name']) ?>" maxlength="120" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold" for="categoryParent">Danh mục cha</label>
                <select id="categoryParent" name="parent_id" class="form-select">
                    <option value="">-- Danh mục gốc --</option>
                    <?php foreach ($parentCategories as $parent): ?>
                        <option value="<?= (int)$parent['id'] ?>" <?= (int)$category['parent_id'] === (int)$parent['id'] ? 'selected' : '' ?>><?= str_repeat('&nbsp;&nbsp;', (int)$parent['depth']) ?><?= htmlspecialchars($parent['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold" for="categoryStatus">Trạng thái</label>
                <select id="categoryStatus" name="status" class="form-select">
                    <option value="active" <?= $category['status'] === 'active' ? 'selected' : '' ?>>Đang hiển thị</option>
                    <option value="inactive" <?= $category['status'] === 'inactive' ? 'selected' : '' ?>>Tạm ẩn</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
            <a href="<?= route_url('admin-categories') ?>" class="btn btn-outline-secondary">Quay lại</a>
        </form>
    </div>
</div>
