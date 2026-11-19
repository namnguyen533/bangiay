<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Thêm sản phẩm giày</h4>
            <p class="text-secondary small mb-0">Tồn kho ban đầu bằng 0; ghi nhận số lượng qua phiếu nhập kho.</p>
        </div>
        <a href="<?= route_url('admin-products') ?>" class="btn btn-outline-secondary rounded-pill">Quay lại</a>
    </div>

    <form action="<?= route_url('admin-product-store') ?>" method="POST" enctype="multipart/form-data">
        <div class="row g-4">
            <div class="col-lg-7">
                <section class="bg-white p-4 rounded-4 border shadow-sm mb-4">
                    <h5 class="fw-bold mb-3">Thông tin chung</h5>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên sản phẩm *</label>
                        <input name="name" class="form-control" maxlength="200" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Thương hiệu</label>
                            <select name="brand_id" class="form-select">
                                <option value="">-- Chưa chọn --</option>
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?= (int)$brand['id'] ?>"><?= htmlspecialchars($brand['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Danh mục</label>
                            <select name="category_id" class="form-select">
                                <option value="">-- Chưa chọn --</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= (int)$category['id'] ?>"><?= str_repeat('&nbsp;&nbsp;', (int)$category['depth']) ?><?= htmlspecialchars($category['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mô tả</label>
                        <textarea name="description" class="form-control" rows="5"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tải nhiều ảnh (JPG, PNG, WebP; tối đa 8 ảnh, 5 MB/ảnh)</label>
                        <input type="file" name="images[]" class="form-control" accept="image/jpeg,image/png,image/webp" multiple>
                    </div>
                    <div>
                        <label class="form-label fw-bold">Hoặc thêm ảnh bằng URL</label>
                        <input type="url" name="image_url" class="form-control" placeholder="https://...">
                    </div>
                </section>
            </div>
            <div class="col-lg-5">
                <section class="bg-white p-4 rounded-4 border shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">Biến thể theo size / màu</h5>
                            <small class="text-secondary">Mã SKU để trống sẽ được tạo tự động.</small>
                        </div>
                        <button class="btn btn-sm btn-outline-primary" type="button" id="addVariant">Thêm dòng</button>
                    </div>
                    <div id="variants" class="d-grid gap-3">
                        <?php foreach (['39', '40', '41', '42'] as $index => $size): ?>
                            <div class="variant-row border rounded-3 p-3">
                                <div class="row g-2">
                                    <div class="col-4"><label class="form-label small">Size *</label><input name="variants[<?= $index ?>][size]" class="form-control" value="<?= $size ?>" required maxlength="20"></div>
                                    <div class="col-8"><label class="form-label small">Màu *</label><input name="variants[<?= $index ?>][color]" class="form-control" value="Trắng" required maxlength="50"></div>
                                    <div class="col-6"><label class="form-label small">Giá *</label><input name="variants[<?= $index ?>][price]" class="form-control" type="number" min="1" step="1000" required></div>
                                    <div class="col-6"><label class="form-label small">Giá khuyến mãi</label><input name="variants[<?= $index ?>][sale_price]" class="form-control" type="number" min="1" step="1000"></div>
                                    <div class="col-10"><label class="form-label small">SKU</label><input name="variants[<?= $index ?>][sku]" class="form-control" maxlength="80" placeholder="Tự động nếu để trống"></div>
                                    <div class="col-2 d-flex align-items-end"><button type="button" class="btn btn-outline-danger w-100 remove-variant" aria-label="Xóa biến thể">&times;</button></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="btn btn-primary w-100 mt-4" type="submit">Lưu sản phẩm</button>
                </section>
            </div>
        </div>
    </form>
</div>

<template id="variantTemplate">
    <div class="variant-row border rounded-3 p-3">
        <div class="row g-2">
            <div class="col-4"><label class="form-label small">Size *</label><input name="variants[__INDEX__][size]" class="form-control" required maxlength="20"></div>
            <div class="col-8"><label class="form-label small">Màu *</label><input name="variants[__INDEX__][color]" class="form-control" required maxlength="50"></div>
            <div class="col-6"><label class="form-label small">Giá *</label><input name="variants[__INDEX__][price]" class="form-control" type="number" min="1" step="1000" required></div>
            <div class="col-6"><label class="form-label small">Giá khuyến mãi</label><input name="variants[__INDEX__][sale_price]" class="form-control" type="number" min="1" step="1000"></div>
            <div class="col-10"><label class="form-label small">SKU</label><input name="variants[__INDEX__][sku]" class="form-control" maxlength="80" placeholder="Tự động nếu để trống"></div>
            <div class="col-2 d-flex align-items-end"><button type="button" class="btn btn-outline-danger w-100 remove-variant" aria-label="Xóa biến thể">&times;</button></div>
        </div>
    </div>
</template>
<script>
(() => {
    let nextIndex = document.querySelectorAll('#variants .variant-row').length;
    document.getElementById('addVariant').addEventListener('click', () => {
        const html = document.getElementById('variantTemplate').innerHTML.replaceAll('__INDEX__', String(nextIndex++));
        document.getElementById('variants').insertAdjacentHTML('beforeend', html);
    });
    document.getElementById('variants').addEventListener('click', event => {
        if (event.target.closest('.remove-variant') && document.querySelectorAll('#variants .variant-row').length > 1) {
            event.target.closest('.variant-row').remove();
        }
    });
})();
</script>
