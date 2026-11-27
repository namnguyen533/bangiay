<!-- Hero Banner Section -->
<section class="py-5 bg-dark text-white rounded-4 my-3 mx-lg-4 shadow" style="position: relative; overflow: hidden; min-height: 520px; background: #0f172a;">
    <div id="featuredSneakerCarousel" class="carousel slide carousel-fade position-absolute top-0 start-0 w-100 h-100" style="z-index: 0;" aria-label="Bộ sưu tập giày nổi bật">
        <div class="carousel-indicators">
            <button type="button" data-carousel-slide="0" class="active" aria-current="true" aria-label="Ảnh 1"></button>
            <button type="button" data-carousel-slide="1" aria-label="Ảnh 2"></button>
            <button type="button" data-carousel-slide="2" aria-label="Ảnh 3"></button>
            <button type="button" data-carousel-slide="3" aria-label="Ảnh 4"></button>
            <button type="button" data-carousel-slide="4" aria-label="Ảnh 5"></button>
        </div>
        <div class="carousel-inner h-100">
            <div class="carousel-item active h-100">
                <img src="https://images.unsplash.com/photo-1552346154-21d32810aba3?w=1920&auto=format&fit=crop&q=85" alt="Giày sneaker nổi bật 1" class="d-block w-100" style="height: 100%; min-height: 520px; object-fit: cover;">
            </div>
            <div class="carousel-item h-100">
                <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=1920&auto=format&fit=crop&q=85" alt="Giày sneaker nổi bật 2" class="d-block w-100" style="height: 100%; min-height: 520px; object-fit: cover;">
            </div>
            <div class="carousel-item h-100">
                <img src="https://images.unsplash.com/photo-1549298916-b41d501d3772?w=1920&auto=format&fit=crop&q=85" alt="Giày sneaker nổi bật 3" class="d-block w-100" style="height: 100%; min-height: 520px; object-fit: cover;">
            </div>
            <div class="carousel-item h-100">
                <img src="https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?w=1920&auto=format&fit=crop&q=85" alt="Giày sneaker nổi bật 4" class="d-block w-100" style="height: 100%; min-height: 520px; object-fit: cover;">
            </div>
            <div class="carousel-item h-100">
                <img src="https://images.unsplash.com/photo-1495555961986-6d4c1ecb7be3?w=1920&auto=format&fit=crop&q=85" alt="Giày sneaker nổi bật 5" class="d-block w-100" style="height: 100%; min-height: 520px; object-fit: cover;">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-carousel-action="prev" aria-label="Ảnh trước">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-carousel-action="next" aria-label="Ảnh tiếp theo">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </button>
    </div>
    <div aria-hidden="true" style="position: absolute; inset: 0; z-index: 1; pointer-events: none; background: linear-gradient(90deg, rgba(15, 23, 42, .94) 0%, rgba(15, 23, 42, .78) 44%, rgba(15, 23, 42, .2) 100%);"></div>
    <div class="container py-4 position-relative" style="z-index: 3;">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="badge bg-danger mb-3 px-3 py-2 text-uppercase fw-bold">BST Sneaker Mùa Hè 2026</span>
                <h1 class="display-4 fw-black mb-3">Nâng Tầm Phong Cách Với Từng Bước Chân</h1>
                <p class="lead text-light mb-4">
                    Khám phá bộ sưu tập giày Sneaker chính hãng từ Nike, Adidas, Puma, Converse. Đa dạng size, phối màu cực chất và bảo hành chính hãng trọn đời.
                </p>
                <div class="d-flex gap-3">
                    <a href="<?= route_url('products') ?>" class="btn btn-primary btn-lg rounded-pill px-4">
                        <i class="fa-solid fa-fire me-2"></i> Mua Ngay Bây Giờ
                    </a>
                    <?php if (!empty($brands)): ?>
                        <a href="<?= route_url('products', ['brand_id' => $brands[0]['id']]) ?>" class="btn btn-outline-light btn-lg rounded-pill px-4">
                            Giày <?= htmlspecialchars($brands[0]['name']) ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script>
        (() => {
            const carousel = document.getElementById('featuredSneakerCarousel');
            const slides = Array.from(carousel.querySelectorAll('.carousel-item'));
            const indicators = Array.from(carousel.querySelectorAll('[data-carousel-slide]'));
            let activeIndex = Math.max(0, slides.findIndex(slide => slide.classList.contains('active')));

            const showSlide = index => {
                slides[activeIndex].classList.remove('active');
                indicators[activeIndex].classList.remove('active');
                indicators[activeIndex].removeAttribute('aria-current');
                activeIndex = (index + slides.length) % slides.length;
                slides[activeIndex].classList.add('active');
                indicators[activeIndex].classList.add('active');
                indicators[activeIndex].setAttribute('aria-current', 'true');
            };

            window.setInterval(() => showSlide(activeIndex + 1), 3500);
            carousel.querySelector('[data-carousel-action="prev"]').addEventListener('click', () => showSlide(activeIndex - 1));
            carousel.querySelector('[data-carousel-action="next"]').addEventListener('click', () => showSlide(activeIndex + 1));
            indicators.forEach((indicator, index) => {
                indicator.addEventListener('click', () => showSlide(index));
            });
        })();
    </script>
</section>

<!-- Value Props Highlights -->
<section class="container my-5">
    <div class="row g-4 text-center">
        <div class="col-md-3 col-6">
            <div class="p-4 bg-white rounded-4 border h-100 shadow-sm">
                <i class="fa-solid fa-certificate fs-1 text-danger mb-3"></i>
                <h6 class="fw-bold mb-1">100% Chính Hãng</h6>
                <p class="small text-secondary mb-0">Cam kết đền 10 lần nếu phát hiện fake</p>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="p-4 bg-white rounded-4 border h-100 shadow-sm">
                <i class="fa-solid fa-truck-fast fs-1 text-danger mb-3"></i>
                <h6 class="fw-bold mb-1">Phí ship 30.000đ</h6>
                <p class="mb-0 small text-secondary">Miễn phí vận chuyển từ 2.000.000đ</p>
                <p class="small text-secondary mb-0">Giao hàng hỏa tốc trong 2 giờ</p>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="p-4 bg-white rounded-4 border h-100 shadow-sm">
                <i class="fa-solid fa-arrow-rotate-left fs-1 text-danger mb-3"></i>
                <h6 class="fw-bold mb-1">Đổi Trả 30 Ngày</h6>
                <p class="small text-secondary mb-0">Hỗ trợ đổi size miễn phí tận nhà</p>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="p-4 bg-white rounded-4 border h-100 shadow-sm">
                <i class="fa-solid fa-headset fs-1 text-danger mb-3"></i>
                <h6 class="fw-bold mb-1">Hỗ Trợ 24/7</h6>
                <p class="small text-secondary mb-0">Tư vấn chọn size giày chuẩn xác</p>
            </div>
        </div>
    </div>
</section>

<!-- Categories List -->
<section class="container my-5">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <span class="text-danger fw-bold text-uppercase small">Bộ sưu tập</span>
            <h3 class="fw-bold mb-0">Danh Mục Giày Nổi Bật</h3>
        </div>
    </div>
    <div class="row g-3">
        <?php foreach ($categories as $cat): ?>
            <div class="col-md col-6">
                <a href="<?= route_url('products', ['category_id' => $cat['id']]) ?>" class="text-decoration-none">
                    <div class="card border-0 bg-white shadow-sm rounded-4 p-3 text-center h-100 hover-card">
                        <i class="fa-solid fa-shoe-prints fs-3 text-danger mb-2"></i>
                        <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($cat['name']) ?></h6>
                        <span class="small text-secondary"><?= $cat['total_products'] ?? 0 ?> sản phẩm</span>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Featured Products Section -->
<section id="featured-products" class="container my-5">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <span class="text-danger fw-bold text-uppercase small">Được yêu thích nhất</span>
            <h3 class="fw-bold mb-0">Sản Phẩm Giày Nổi Bật</h3>
        </div>
        <a href="<?= route_url('products') ?>" class="btn btn-outline-dark rounded-pill btn-sm px-3">
            Xem Tất Cả <i class="fa-solid fa-arrow-right ms-1"></i>
        </a>
    </div>

    <div class="row g-4">
        <?php if (!empty($featuredProducts)): ?>
            <?php foreach ($featuredProducts as $item): ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
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
                                <a href="<?= route_url('product-detail', ['id' => $item['id']]) ?>" class="text-dark text-decoration-none text-truncate-2">
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
            <div class="col-12 text-center py-5">
                <p class="text-muted">Chưa có sản phẩm nào trong hệ thống.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Phân trang Trang Chủ -->
    <?php if (!empty($totalPages) && $totalPages > 1): ?>
        <nav aria-label="Page navigation" class="mt-5">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link rounded-pill me-1 px-3" href="<?= route_url('', ['page' => $currentPage - 1]) ?>#featured-products">
                        <i class="fa-solid fa-chevron-left me-1"></i> Trước
                    </a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($i === $currentPage) ? 'active' : '' ?>">
                        <a class="page-link rounded-circle mx-1 text-center d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" href="<?= route_url('', ['page' => $i]) ?>#featured-products">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                    <a class="page-link rounded-pill ms-1 px-3" href="<?= route_url('', ['page' => $currentPage + 1]) ?>#featured-products">
                        Sau <i class="fa-solid fa-chevron-right ms-1"></i>
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</section>

<!-- Brand Showcase -->
<section class="container my-5 py-4 bg-white rounded-4 border shadow-sm">
    <div class="text-center mb-4">
        <h5 class="fw-bold text-uppercase tracking-wider text-secondary">Thương Hiệu Sneaker Đồng Hành</h5>
    </div>
    <div class="d-flex justify-content-around align-items-center flex-wrap gap-4 py-2">
        <?php foreach ($brands as $brand): ?>
            <a href="<?= route_url('products', ['brand_id' => $brand['id']]) ?>" class="text-decoration-none text-dark fw-bold fs-4 opacity-75 hover-opacity-100">
                <?= htmlspecialchars($brand['name']) ?>
            </a>
        <?php endforeach; ?>
    </div>
</section>
