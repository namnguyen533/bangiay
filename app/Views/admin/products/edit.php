<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Chỉnh sửa sản phẩm</h4>
            <p class="text-secondary small mb-0"><?= htmlspecialchars($product['name']) ?></p>
        </div>
        <a href="<?= route_url('admin-products') ?>" class="btn btn-outline-secondary">Quay lại</a>
    </div>
    <form action="<?= route_url('admin-product-update') ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= (int)$product['id'] ?>">
        <div class="row g-4">
            <div class="col-lg-7">
                <section class="bg-white p-4 rounded-4 border shadow-sm mb-4">
                    <h5 class="fw-bold mb-3">Thông tin chung</h5>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên sản phẩm *</label>
                        <input name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" maxlength="200" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Thương hiệu</label>
                            <select name="brand_id" class="form-select">
                                <option value="">-- Chưa chọn --</option>
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?= (int)$brand['id'] ?>" <?= (int)$product['brand_id'] === (int)$brand['id'] ? 'selected' : '' ?>><?= htmlspecialchars($brand['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Danh mục</label>
                            <select name="category_id" class="form-select">
                                <option value="">-- Chưa chọn --</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= (int)$category['id'] ?>" <?= (int)($product['category_id'] ?? 0) === (int)$category['id'] ? 'selected' : '' ?>><?= str_repeat('&nbsp;&nbsp;', (int)$category['depth']) ?><?= htmlspecialchars($category['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Trạng thái</label>
                            <select name="status" class="form-select">
                                <option value="active" <?= $product['status'] === 'active' ? 'selected' : '' ?>>Đang bán</option>
                                <option value="inactive" <?= $product['status'] === 'inactive' ? 'selected' : '' ?>>Tạm ẩn</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">URL ảnh (tùy chọn)</label>
                            <input type="url" name="image_url" class="form-control">
                        </div>
                    </div>
                    <?php $descriptionEditorContent = $product['description'] ?? ''; include __DIR__ . '/_description_editor.php'; ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Thêm ảnh (tối đa 8 ảnh, 5 MB/ảnh)</label>
                        <input type="file" name="images[]" class="form-control" accept="image/jpeg,image/png,image/webp" multiple>
                    </div>
                    <div class="row g-3">
                        <?php foreach ($product['images'] as $image): ?>
                            <div class="col-6 col-md-4">
                                <div class="border rounded-3 p-2 h-100">
                                    <img src="<?= htmlspecialchars(image_src($image['image_url'])) ?>" alt="" class="w-100 rounded-2 mb-2" style="height:120px;object-fit:cover">
                                    <label class="form-check small">
                                        <input class="form-check-input" type="checkbox" name="remove_image_ids[]" value="<?= (int)$image['id'] ?>">
                                        <span class="form-check-label">Gỡ ảnh</span>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-3">
                        <label class="form-label fw-bold">Thêm ảnh bổ sung bằng URL</label>
                        <textarea name="image_urls" class="form-control" rows="3" placeholder="Mỗi URL một dòng"></textarea>
                    </div>
                </section>
            </div>
            <div class="col-lg-5">
                <section class="bg-white p-4 rounded-4 border shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div><h5 class="fw-bold mb-1">Biến thể</h5><small class="text-secondary">Tồn kho chỉ thay đổi qua phiếu nhập.</small></div>
                        <button class="btn btn-sm btn-outline-primary" type="button" id="addVariant">Thêm dòng</button>
                    </div>
                    <div id="variants" class="d-grid gap-3">
                        <?php foreach ($variants as $index => $variant): ?>
                            <div class="variant-row border rounded-3 p-3">
                                <input type="hidden" name="variants[<?= $index ?>][id]" value="<?= (int)$variant['id'] ?>">
                                <div class="small text-secondary mb-2">Tồn hiện tại: <strong><?= (int)$variant['stock_quantity'] ?></strong></div>
                                <div class="row g-2">
                                    <div class="col-4"><label class="form-label small">Size *</label><input name="variants[<?= $index ?>][size]" class="form-control" value="<?= htmlspecialchars($variant['size']) ?>" required maxlength="20"></div>
                                    <div class="col-8"><label class="form-label small">Màu *</label><input name="variants[<?= $index ?>][color]" class="form-control" value="<?= htmlspecialchars($variant['color']) ?>" required maxlength="50"></div>
                                    <div class="col-6"><label class="form-label small">Giá *</label><input name="variants[<?= $index ?>][price]" class="form-control" type="number" min="1" step="1000" value="<?= htmlspecialchars($variant['price']) ?>" required></div>
                                    <div class="col-6"><label class="form-label small">Giá khuyến mãi</label><input name="variants[<?= $index ?>][sale_price]" class="form-control" type="number" min="1" step="1000" value="<?= htmlspecialchars((string)($variant['sale_price'] ?? '')) ?>"></div>
                                    <div class="col-7"><label class="form-label small">SKU</label><input name="variants[<?= $index ?>][sku]" class="form-control" value="<?= htmlspecialchars($variant['sku']) ?>" maxlength="80"></div>
                                    <div class="col-5"><label class="form-label small">Trạng thái</label><select name="variants[<?= $index ?>][status]" class="form-select"><option value="active" <?= $variant['status'] === 'active' ? 'selected' : '' ?>>Bán</option><option value="inactive" <?= $variant['status'] === 'inactive' ? 'selected' : '' ?>>Ẩn</option></select></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="btn btn-primary w-100 mt-4" type="submit">Lưu thay đổi</button>
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
            <div class="col-7"><label class="form-label small">SKU</label><input name="variants[__INDEX__][sku]" class="form-control" maxlength="80" placeholder="Tự động nếu để trống"></div>
            <div class="col-5"><label class="form-label small">Trạng thái</label><select name="variants[__INDEX__][status]" class="form-select"><option value="active">Bán</option><option value="inactive">Ẩn</option></select></div>
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
})();
</script>
