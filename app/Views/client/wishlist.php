<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="fa-solid fa-heart text-danger me-2"></i> Sản Phẩm Yêu Thích</h3>
            <p class="text-secondary small mb-0">Danh sách những đôi giày sneaker bạn đã lưu lại để mua sau</p>
        </div>
        <a href="<?= route_url('products') ?>" class="btn btn-outline-dark rounded-pill">
            <i class="fa-solid fa-shoe-prints me-1"></i> Khám Phá Thêm Giày
        </a>
    </div>

    <?php if (!empty($items)): ?>
        <div class="row g-4">
            <?php foreach ($items as $item): ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="product-card h-100 d-flex flex-column position-relative shadow-sm">
                        <!-- Nút Xóa Wishlist -->
                        <a href="<?= route_url('wishlist-remove', ['product_id' => $item['id']]) ?>" class="btn btn-sm btn-light rounded-circle shadow-sm position-absolute" style="top: 10px; right: 10px; z-index: 10; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center;" title="Bỏ yêu thích" onclick="return confirm('Bỏ sản phẩm này khỏi danh sách yêu thích?');">
                            <i class="fa-solid fa-xmark text-danger"></i>
                        </a>

                        <div class="product-img-box">
                            <a href="<?= route_url('product-detail', ['id' => $item['id']]) ?>">
                                <img src="<?= htmlspecialchars($item['image_url'] ?: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600') ?>" alt="<?= htmlspecialchars($item['name']) ?>" loading="lazy">
                            </a>
                            <?php if (!empty($item['brand_name'])): ?>
                                <span class="badge-brand"><?= htmlspecialchars($item['brand_name']) ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="p-3 d-flex flex-column flex-grow-1">
                            <div class="small text-secondary mb-1"><?= htmlspecialchars($item['category_name'] ?? 'Giày Sneaker') ?></div>
                            <h6 class="fw-bold mb-2 flex-grow-1">
                                <a href="<?= route_url('product-detail', ['id' => $item['id']]) ?>" class="text-dark text-decoration-none">
                                    <?= htmlspecialchars($item['name']) ?>
                                </a>
                            </h6>

                            <div class="mt-auto">
                                <div class="d-flex align-items-baseline gap-2 mb-3">
                                    <?php if (!empty($item['min_sale_price']) && $item['min_sale_price'] < $item['min_price']): ?>
                                        <span class="fw-bold text-danger fs-5"><?= format_price($item['min_sale_price']) ?></span>
                                        <span class="text-decoration-line-through text-secondary small"><?= format_price($item['min_price']) ?></span>
                                    <?php else: ?>
                                        <span class="fw-bold text-dark fs-5"><?= format_price($item['min_price'] ?? 1500000) ?></span>
                                    <?php endif; ?>
                                </div>

                                <div class="d-flex gap-2">
                                    <a href="<?= route_url('product-detail', ['id' => $item['id']]) ?>" class="btn btn-primary w-100 rounded-pill btn-sm fw-semibold">
                                        <i class="fa-solid fa-cart-shopping me-1"></i> Chọn Size & Mua
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5 bg-white rounded-4 border shadow-sm">
            <div class="mb-3 text-secondary" style="font-size: 4rem;">
                <i class="fa-regular fa-heart"></i>
            </div>
            <h4 class="fw-bold">Danh sách yêu thích của bạn đang trống!</h4>
            <p class="text-secondary small mb-4">Hãy nhấn biểu tượng trái tim ở các mẫu giày bạn ưng ý để lưu lại xem sau.</p>
            <a href="<?= route_url('products') ?>" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold">
                Khám Phá Bộ Sưu Tập Giày
            </a>
        </div>
    <?php endif; ?>
</div>
