<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Quản Lý Danh Mục Giày</h4>
            <p class="text-secondary small mb-0">Phân loại sản phẩm (Sneaker, Giày Chạy Bộ, Giày Bóng Rổ,...)</p>
        </div>
    </div>

    <div class="row g-4">
        <!-- Form Thêm Danh Mục -->
        <div class="col-lg-4">
            <div class="bg-white p-4 rounded-4 border shadow-sm">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Thêm Danh Mục Mới</h5>
                <form action="<?= route_url('admin-category-store') ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên Danh Mục <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Ví dụ: Giày Đi Bộ (Walking)" required>
                    </div>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold w-100">
                        <i class="fa-solid fa-plus me-1"></i> Tạo Danh Mục
                    </button>
                </form>
            </div>
        </div>

        <!-- Bảng Danh Sách Danh Mục -->
        <div class="col-lg-8">
            <div class="bg-white p-4 rounded-4 border shadow-sm">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Các Danh Mục Hiện Có</h5>
                <div class="table-responsive">
                    <table class="table align-middle table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#ID</th>
                                <th>Tên Danh Mục</th>
                                <th>Slug</th>
                                <th>Số Sản Phẩm</th>
                                <th class="text-end">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <tr>
                                        <td>#<?= $cat['id'] ?></td>
                                        <td class="fw-bold text-dark"><?= htmlspecialchars($cat['name']) ?></td>
                                        <td><code><?= htmlspecialchars($cat['slug']) ?></code></td>
                                        <td>
                                            <span class="badge bg-secondary"><?= $cat['total_products'] ?? 0 ?> giày</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="<?= route_url('admin-category-delete', ['id' => $cat['id']]) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center py-4 text-secondary">Chưa có danh mục nào.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
