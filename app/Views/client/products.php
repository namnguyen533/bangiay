<div class="container my-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= route_url() ?>" class="text-decoration-none text-secondary">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tất cả giày</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Sidebar Bộ Lọc Đa Điều Kiện -->
        <aside class="col-lg-3">
            <div class="bg-white p-4 rounded-4 border shadow-sm sticky-top" style="top: 90px; z-index: 100;">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-filter me-2 text-danger"></i> Bộ Lọc</h5>
                    <a href="<?= route_url('products') ?>" class="btn btn-sm btn-light border rounded-pill px-3 text-secondary text-decoration-none" title="Xóa toàn bộ bộ lọc">
                        <i class="fa-solid fa-rotate-left me-1"></i> Đặt lại
                    </a>
                </div>

                <form id="filterForm" action="<?= route_url('products') ?>" method="GET">
                    <input type="hidden" name="act" value="products">
                    <?php if (!empty($filters['keyword'])): ?>
                        <input type="hidden" name="q" value="<?= htmlspecialchars($filters['keyword']) ?>">
                    <?php endif; ?>

                    <!-- Danh Mục -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase text-secondary d-flex justify-content-between align-items-center">
                            <span>Danh Mục Giày</span>
                            <?php if (!empty($filters['category_id'])): ?>
                                <span class="badge bg-danger">Đang chọn</span>
                            <?php endif; ?>
                        </label>
                        <div class="d-flex flex-column gap-2" style="max-height: 200px; overflow-y: auto;">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="category_id" id="cat_all" value="" <?= empty($filters['category_id']) ? 'checked' : '' ?> onchange="this.form.submit()">
                                <label class="form-check-label" for="cat_all">Tất cả danh mục</label>
                            </div>
                            <?php foreach ($categories as $cat): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="category_id" id="cat_<?= $cat['id'] ?>" value="<?= $cat['id'] ?>" <?= ($filters['category_id'] == $cat['id']) ? 'checked' : '' ?> onchange="this.form.submit()">
                                    <label class="form-check-label" for="cat_<?= $cat['id'] ?>">
                                        <?= str_repeat('&nbsp;&nbsp;', (int)($cat['depth'] ?? 0)) ?><?= htmlspecialchars($cat['name']) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Thương Hiệu -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase text-secondary d-flex justify-content-between align-items-center">
                            <span>Thương Hiệu</span>
                            <?php if (!empty($filters['brand_id'])): ?>
                                <span class="badge bg-danger">Đang chọn</span>
                            <?php endif; ?>
                        </label>
                        <div class="d-flex flex-column gap-2" style="max-height: 200px; overflow-y: auto;">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="brand_id" id="brand_all" value="" <?= empty($filters['brand_id']) ? 'checked' : '' ?> onchange="this.form.submit()">
                                <label class="form-check-label" for="brand_all">Tất cả thương hiệu</label>
                            </div>
                            <?php foreach ($brands as $brand): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="brand_id" id="brand_<?= $brand['id'] ?>" value="<?= $brand['id'] ?>" <?= ($filters['brand_id'] == $brand['id']) ? 'checked' : '' ?> onchange="this.form.submit()">
                                    <label class="form-check-label" for="brand_<?= $brand['id'] ?>">
                                        <?= htmlspecialchars($brand['name']) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Khoảng Giá -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Mức Giá</label>
                        <?php
                        $priceRanges = [
                            '' => 'Tất cả mức giá',
                            '0-1000000' => 'Dưới 1.000.000 ₫',
                            '1000000-2000000' => '1.000.000 ₫ - 2.000.000 ₫',
                            '2000000-3000000' => '2.000.000 ₫ - 3.000.000 ₫',
                            '3000000-0' => 'Trên 3.000.000 ₫',
                        ];
                        $curRange = $filters['price_range'] ?? '';
                        ?>
                        <div class="d-flex flex-column gap-2">
                            <?php foreach ($priceRanges as $rangeVal => $rangeLabel): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="price_range" id="price_<?= md5($rangeVal) ?>" value="<?= $rangeVal ?>" <?= ($curRange === $rangeVal) ? 'checked' : '' ?> onchange="this.form.submit()">
                                    <label class="form-check-label small" for="price_<?= md5($rangeVal) ?>">
                                        <?= $rangeLabel ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Lọc Size Giày -->
                    <?php if (!empty($availableSizes)): ?>
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-secondary d-flex justify-content-between align-items-center">
                                <span>Kích Thước Size</span>
                                <?php if (!empty($filters['size'])): ?>
                                    <span class="badge bg-danger">Size <?= htmlspecialchars($filters['size']) ?></span>
                                <?php endif; ?>
                            </label>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="<?= route_url('products', array_merge($filters, ['size' => ''])) ?>" class="btn btn-sm <?= empty($filters['size']) ? 'btn-dark' : 'btn-outline-secondary' ?> rounded-pill px-3">
                                    Tất cả
                                </a>
                                <?php foreach ($availableSizes as $sz): ?>
                                    <a href="<?= route_url('products', array_merge($filters, ['size' => $sz])) ?>" class="btn btn-sm <?= ($filters['size'] === (string)$sz) ? 'btn-danger fw-bold' : 'btn-outline-secondary' ?> rounded-3" style="min-width: 44px;">
                                        <?= htmlspecialchars($sz) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Sắp Xếp -->
                    <div class="mb-2">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Sắp Xếp Theo</label>
                        <select name="sort" class="form-select" onchange="this.form.submit()">
                            <option value="newest" <?= ($filters['sort'] === 'newest') ? 'selected' : '' ?>>Mới nhất</option>
                            <option value="price_asc" <?= ($filters['sort'] === 'price_asc') ? 'selected' : '' ?>>Giá tăng dần</option>
                            <option value="price_desc" <?= ($filters['sort'] === 'price_desc') ? 'selected' : '' ?>>Giá giảm dần</option>
                            <option value="popular" <?= ($filters['sort'] === 'popular') ? 'selected' : '' ?>>Bán chạy / Nổi bật</option>
                            <option value="name_asc" <?= ($filters['sort'] === 'name_asc') ? 'selected' : '' ?>>Tên A - Z</option>
                        </select>
                    </div>
                </form>
            </div>
        </aside>

        <!-- Main Product Grid -->
        <main class="col-lg-9">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 bg-white p-3 rounded-4 border shadow-sm gap-2">
                <span class="text-secondary">
                    Tìm thấy <strong class="text-dark"><?= $totalProducts ?></strong> sản phẩm
                    <?php if (!empty($filters['keyword'])): ?>
                        cho từ khóa "<strong class="text-danger"><?= htmlspecialchars($filters['keyword']) ?></strong>"
                    <?php endif; ?>
                    <?php if (!empty($filters['size'])): ?>
                        | Size: <strong class="text-danger"><?= htmlspecialchars($filters['size']) ?></strong>
                    <?php endif; ?>
                    <?php if (!empty($curRange) && isset($priceRanges[$curRange])): ?>
                        | Mức giá: <strong class="text-danger"><?= $priceRanges[$curRange] ?></strong>
                    <?php endif; ?>
                </span>
                <?php if (!empty($filters['keyword']) || !empty($filters['category_id']) || !empty($filters['brand_id']) || !empty($filters['size']) || !empty($filters['price_range'])): ?>
                    <a href="<?= route_url('products') ?>" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                        <i class="fa-solid fa-xmark me-1"></i> Xóa tất cả bộ lọc
                    </a>
                <?php endif; ?>
            </div>

            <div class="row g-4">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $item): ?>
                        <div class="col-md-4 col-sm-6">
                            <div class="product-card h-100 d-flex flex-column">
                                <div class="product-img-box position-relative">
                                    <a href="<?= route_url('wishlist-toggle', ['product_id' => $item['id']]) ?>" class="btn btn-sm btn-light rounded-circle shadow-sm position-absolute" style="top: 10px; right: 10px; z-index: 5; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center;" title="Thêm vào yêu thích">
                                        <i class="fa-regular fa-heart text-danger"></i>
                                    </a>
                                    <a href="<?= route_url('product-detail', ['id' => $item['id']]) ?>">
                                        <img src="<?= htmlspecialchars(image_src($item['image_url'] ?: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600')) ?>" alt="<?= htmlspecialchars($item['name']) ?>" loading="lazy">
                                    </a>
                                    <?php if (!empty($item['brand_name'])): ?>
                                        <span class="badge-brand"><?= htmlspecialchars($item['brand_name']) ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($item['min_sale_price']) && $item['min_sale_price'] < $item['min_price']): ?>
                                        <span class="badge-discount">Sale</span>
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

                                        <a href="<?= route_url('product-detail', ['id' => $item['id']]) ?>" class="btn btn-outline-dark w-100 rounded-pill btn-sm fw-semibold">
                                            <i class="fa-solid fa-eye me-1"></i> Xem Chi Tiết
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5 bg-white rounded-4 border">
                        <i class="fa-solid fa-box-open fs-1 text-secondary mb-3"></i>
                        <h5 class="fw-bold">Không tìm thấy sản phẩm giày phù hợp!</h5>
                        <p class="text-secondary small">Vui lòng thử tìm kiếm với tên giày, mã SKU hoặc đặt lại bộ lọc.</p>
                        <a href="<?= route_url('products') ?>" class="btn btn-primary rounded-pill px-4">Xem Tất Cả Giày</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Phân Trang Đa Điều Kiện -->
            <?php if ($totalPages > 1): ?>
                <nav class="mt-5">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link rounded-pill me-1 px-3" href="<?= route_url('products', array_merge($filters, ['page' => $currentPage - 1])) ?>">
                                <i class="fa-solid fa-chevron-left me-1"></i> Trước
                            </a>
                        </li>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= ($currentPage == $i) ? 'active' : '' ?>">
                                <a class="page-link rounded-circle mx-1 text-center d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" href="<?= route_url('products', array_merge($filters, ['page' => $i])) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                            <a class="page-link rounded-pill ms-1 px-3" href="<?= route_url('products', array_merge($filters, ['page' => $currentPage + 1])) ?>">
                                Sau <i class="fa-solid fa-chevron-right ms-1"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </main>
    </div>
</div>
