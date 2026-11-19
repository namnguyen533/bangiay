<div class="container-fluid" style="max-width: 900px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Thêm Sản Phẩm Giày Mới</h4>
            <p class="text-secondary small mb-0">Điền đầy đủ thông tin để đăng bán giày mới lên website</p>
        </div>
        <a href="<?= route_url('admin-products') ?>" class="btn btn-outline-secondary rounded-pill">
            <i class="fa-solid fa-arrow-left me-1"></i> Quay Lại
        </a>
    </div>

    <div class="bg-white p-4 p-md-5 rounded-4 border shadow-sm">
        <form action="<?= route_url('admin-product-store') ?>" method="POST">
            <div class="row g-3 mb-3">
                <div class="col-md-8">
                    <label class="form-label fw-bold">Tên Giày <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Ví dụ: Nike Air Force 1 '07 All White" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Thương Hiệu</label>
                    <select name="brand_id" class="form-select">
                        <option value="">-- Chọn Hãng --</option>
                        <?php foreach ($brands as $b): ?>
                            <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?></option>
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
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Ảnh Chính (URL)</label>
                    <input type="url" name="image_url" id="primaryImageUrl" class="form-control" placeholder="https://images.unsplash.com/...">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Các Hình Ảnh Chi Tiết (URL)</label>
                <textarea name="image_urls" class="form-control" rows="4" placeholder="Mỗi link ảnh chi tiết đặt trên một dòng&#10;https://example.com/goc-ngang.jpg&#10;https://example.com/de-giay.jpg"></textarea>
                <div class="form-text">Thêm nhiều link để khách hàng xem từng góc của đôi giày. Ảnh chính ở trên sẽ hiển thị trước.</div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Giá Gốc (VNĐ) <span class="text-danger">*</span></label>
                    <input type="number" name="price" class="form-control" placeholder="2500000" min="0" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Giá Khuyến Mãi (Nếu có)</label>
                    <input type="number" name="sale_price" class="form-control" placeholder="2190000" min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Tồn Kho Mỗi Size</label>
                    <input type="number" name="stock_quantity" class="form-control" value="15" min="0">
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Phối Màu</label>
                    <input type="text" name="color" class="form-control" value="Trắng / Đen" placeholder="Ví dụ: Trắng tinh khiết">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Các Kích Thước Tạo Mặc Định</label>
                    <div class="d-flex flex-wrap gap-3 pt-2">
                        <?php foreach (['38', '39', '40', '41', '42', '43', '44'] as $sz): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="sizes[]" value="<?= $sz ?>" id="sz_<?= $sz ?>" <?= in_array($sz, ['39', '40', '41', '42']) ? 'checked' : '' ?>>
                                <label class="form-check-label fw-semibold" for="sz_<?= $sz ?>"><?= $sz ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Mô Tả Sản Phẩm</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Mô tả chất liệu đế, đệm lót, hướng dẫn phối đồ..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold">
                <i class="fa-solid fa-check me-2"></i> Lưu Sản Phẩm Giày
            </button>
        </form>
    </div>
</div>
