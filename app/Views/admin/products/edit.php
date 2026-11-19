<div class="container-fluid" style="max-width: 900px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Chỉnh Sửa Sản Phẩm Giày</h4>
            <p class="text-secondary small mb-0">Cập nhật thông tin chi tiết cho: <?= htmlspecialchars($product['name']) ?></p>
        </div>
        <a href="<?= route_url('admin-products') ?>" class="btn btn-outline-secondary rounded-pill">
            <i class="fa-solid fa-arrow-left me-1"></i> Quay Lại
        </a>
    </div>

    <div class="bg-white p-4 p-md-5 rounded-4 border shadow-sm">
        <form action="<?= route_url('admin-product-update') ?>" method="POST">
            <input type="hidden" name="id" value="<?= $product['id'] ?>">

            <div class="row g-3 mb-3">
                <div class="col-md-8">
                    <label class="form-label fw-bold">Tên Giày <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Thương Hiệu</label>
                    <select name="brand_id" class="form-select">
                        <option value="">-- Chọn Hãng --</option>
                        <?php foreach ($brands as $b): ?>
                            <option value="<?= $b['id'] ?>" <?= ($product['brand_id'] == $b['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($b['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Danh Mục</label>
                    <select name="category_id" class="form-select">
                        <option value="">-- Chọn Danh Mục --</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= (($product['category_id'] ?? 0) == $c['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Trạng Thái Bán</label>
                    <select name="status" class="form-select">
                        <option value="active" <?= ($product['status'] === 'active') ? 'selected' : '' ?>>Đang bán (Active)</option>
                        <option value="inactive" <?= ($product['status'] === 'inactive') ? 'selected' : '' ?>>Tạm ẩn (Inactive)</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Ảnh Chính (URL)</label>
                <input type="url" name="image_url" class="form-control" value="<?= htmlspecialchars($product['image_url'] ?? '') ?>" placeholder="https://images.unsplash.com/...">
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Các Hình Ảnh Chi Tiết (URL)</label>
                <?php
                $detailImageUrls = [];
                foreach ($product['images'] as $index => $image) {
                    if ((int)($image['is_primary'] ?? 0) !== 1) {
                        $detailImageUrls[] = $image['image_url'];
                    }
                }
                ?>
                <textarea name="image_urls" class="form-control" rows="4" placeholder="Mỗi link ảnh chi tiết đặt trên một dòng"><?= htmlspecialchars(implode("\n", $detailImageUrls)) ?></textarea>
                <div class="form-text">Để trống hoặc bỏ ảnh phụ thì ảnh đó sẽ bị xóa khỏi thư viện chi tiết.</div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-8">
                    <label class="form-label fw-bold">Màu Giày</label>
                    <?php
                    $productColors = array_values(array_unique(array_column($product['variants'], 'color')));
                    ?>
                    <input type="text" name="colors" class="form-control" value="<?= htmlspecialchars(implode(', ', $productColors)) ?>" placeholder="Trắng, Đen, Đỏ">
                    <div class="form-text">Nhập màu mới cùng các màu đang có, phân cách bằng dấu phẩy. Màu hiện có được giữ nguyên.</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Tồn Kho Mỗi Size Cho Màu Mới</label>
                    <input type="number" name="new_color_stock" class="form-control" value="15" min="0">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Mô Tả Sản Phẩm</label>
                <textarea name="description" class="form-control" rows="5"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold">
                <i class="fa-solid fa-floppy-disk me-2"></i> Lưu Thay Đổi
            </button>
        </form>
    </div>
</div>
