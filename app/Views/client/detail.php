<div class="container my-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= route_url() ?>" class="text-decoration-none text-secondary">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= route_url('products') ?>" class="text-decoration-none text-secondary">Sản phẩm</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['name']) ?></li>
        </ol>
    </nav>

    <div class="row g-5">
        <!-- Cột Ảnh Sản Phẩm -->
        <div class="col-lg-6">
            <div class="bg-white p-3 rounded-4 border shadow-sm mb-3 text-center">
                <img id="mainProductImg" src="<?= htmlspecialchars(image_src($product['image_url'] ?: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600')) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="img-fluid rounded-3" style="max-height: 450px; object-fit: contain; width: 100%;">
            </div>

            <?php if (!empty($product['images']) && count($product['images']) > 1): ?>
                <div class="d-flex gap-2 overflow-auto py-2">
                    <?php foreach ($product['images'] as $img): ?>
                        <img src="<?= htmlspecialchars(image_src($img['image_url'])) ?>" alt="Thumbnail" class="rounded-3 border cursor-pointer thumbnail-img" style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;" onclick="document.getElementById('mainProductImg').src = this.src">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Cột Thông Tin & Chọn Biến Thể -->
        <div class="col-lg-6">
            <div class="bg-white p-4 rounded-4 border shadow-sm">
                <!-- Hãng & Danh mục -->
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-dark px-3 py-2 text-uppercase"><?= htmlspecialchars($product['brand_name'] ?? 'Sneaker') ?></span>
                    <span class="text-secondary small"><?= htmlspecialchars($product['category_name'] ?? 'Giày thời trang') ?></span>
                </div>

                <!-- Tên Giày -->
                <h2 class="fw-bold text-dark mb-3"><?= htmlspecialchars($product['name']) ?></h2>

                <!-- Giá -->
                <?php
                $firstVariant = $product['variants'][0] ?? null;
                $defaultPrice = $firstVariant['price'] ?? 0;
                $defaultSale = $firstVariant['sale_price'] ?? null;
                ?>
                <div class="p-3 bg-light rounded-3 mb-4 d-flex align-items-baseline gap-3">
                    <?php if ($defaultSale && $defaultSale < $defaultPrice): ?>
                        <span id="displayPrice" class="fs-2 fw-black text-danger"><?= format_price($defaultSale) ?></span>
                        <span id="displayOldPrice" class="fs-5 text-decoration-line-through text-secondary"><?= format_price($defaultPrice) ?></span>
                        <span class="badge bg-danger">Tiết kiệm <?= format_price($defaultPrice - $defaultSale) ?></span>
                    <?php else: ?>
                        <span id="displayPrice" class="fs-2 fw-black text-danger"><?= format_price($defaultPrice) ?></span>
                        <span id="displayOldPrice" class="fs-5 text-decoration-line-through text-secondary d-none"></span>
                    <?php endif; ?>
                </div>

                <!-- Form Thêm Giỏ Hàng -->
                <form action="<?= route_url('cart-add') ?>" method="POST">
                    <!-- Chọn Size & Biến thể -->
                    <div class="mb-4">
                        <label class="form-label fw-bold d-flex justify-content-between align-items-center">
                            <span>Chọn Kích Thước (Size)</span>
                            <a href="#sizeGuideModal" data-bs-toggle="modal" class="small text-danger text-decoration-none">
                                <i class="fa-solid fa-ruler me-1"></i> Bảng đo size
                            </a>
                        </label>

                        <?php if (!empty($product['variants'])): ?>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($product['variants'] as $idx => $v): ?>
                                    <div>
                                        <input type="radio" class="btn-check variant-radio" name="variant_id" id="variant_<?= $v['id'] ?>" value="<?= $v['id'] ?>" 
                                            data-price="<?= $v['price'] ?>" 
                                            data-sale="<?= $v['sale_price'] ?? '' ?>" 
                                            data-stock="<?= $v['stock_quantity'] ?>"
                                            data-color="<?= htmlspecialchars($v['color']) ?>"
                                            <?= ($idx === 0) ? 'checked' : '' ?>
                                            <?= ($v['stock_quantity'] <= 0) ? 'disabled' : '' ?>>
                                        <label class="btn btn-outline-secondary px-3 py-2 fw-bold" for="variant_<?= $v['id'] ?>">
                                            Size <?= htmlspecialchars($v['size']) ?>
                                            <?php if ($v['stock_quantity'] <= 0): ?>
                                                <small class="d-block text-danger">(Hết)</small>
                                            <?php endif; ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="mt-2 small text-secondary">
                                Phối màu: <strong id="variantColor" class="text-dark"><?= htmlspecialchars($firstVariant['color'] ?? 'Tiêu chuẩn') ?></strong>
                                | Tồn kho: <strong id="variantStock" class="text-success"><?= $firstVariant['stock_quantity'] ?? 0 ?> đôi</strong>
                            </div>
                        <?php else: ?>
                            <p class="text-danger">Sản phẩm hiện đang tạm hết hàng.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Số lượng & Nút mua -->
                    <div class="row g-3 align-items-center mb-4">
                        <div class="col-sm-4 col-5">
                            <label class="form-label fw-bold small">Số Lượng</label>
                            <div class="input-group">
                                <button type="button" class="btn btn-outline-secondary" onclick="let q=document.getElementById('quantityInput'); if(q.value>1) q.value--;">-</button>
                                <input type="number" id="quantityInput" name="quantity" class="form-control text-center" value="1" min="1" max="10">
                                <button type="button" class="btn btn-outline-secondary" onclick="let q=document.getElementById('quantityInput'); q.value++;">+</button>
                            </div>
                        </div>

                        <div class="col-sm-8 col-7 pt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg flex-grow-1 rounded-pill fw-bold" <?= empty($product['variants']) ? 'disabled' : '' ?>>
                                <i class="fa-solid fa-cart-plus me-2"></i> Thêm Vào Giỏ Hàng
                            </button>
                            <a href="<?= route_url('wishlist-toggle', ['product_id' => $product['id']]) ?>" class="btn btn-outline-danger btn-lg rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; flex-shrink: 0;" title="Thêm vào danh sách yêu thích">
                                <i class="fa-regular fa-heart"></i>
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Cam Kết Khách Hàng -->
                <div class="border-top pt-4">
                    <div class="row g-2 text-secondary small">
                        <div class="col-6"><i class="fa-solid fa-truck text-danger me-2"></i> Miễn phí giao hàng từ 2.000.000đ</div>
                        <div class="col-6"><i class="fa-solid fa-shield text-danger me-2"></i> Bảo hành chính hãng 12 tháng</div>
                        <div class="col-6"><i class="fa-solid fa-rotate-left text-danger me-2"></i> Đổi size miễn phí trong 30 ngày</div>
                        <div class="col-6"><i class="fa-solid fa-box-check text-danger me-2"></i> Kiểm tra hàng trước khi thanh toán</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mô Tả Chi Tiết Sản Phẩm -->
    <div class="my-5 bg-white p-4 p-md-5 rounded-4 border shadow-sm">
        <h4 class="fw-bold mb-4 border-bottom pb-3"><i class="fa-solid fa-file-lines me-2 text-danger"></i> Mô Tả Chi Tiết</h4>
        <div class="lead fs-6 text-secondary" style="line-height: 1.8;">
            <?= nl2br(htmlspecialchars($product['description'] ?? 'Mẫu giày sneaker cao cấp được thiết kế tinh xảo, êm ái cho đôi chân, phong cách trẻ trung và năng động.')) ?>
        </div>
    </div>

    <section class="my-5 bg-white p-4 p-md-5 rounded-4 border shadow-sm">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 border-bottom pb-3 mb-4">
            <h4 class="fw-bold mb-0"><i class="fa-solid fa-star text-warning me-2"></i>Đánh Giá Sản Phẩm</h4>
            <span class="text-secondary"><?= count($reviews) ?> đánh giá đã duyệt</span>
        </div>
        <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $review): ?>
                <article class="border-bottom pb-3 mb-3">
                    <div class="d-flex justify-content-between gap-2">
                        <strong><?= htmlspecialchars($review['full_name']) ?></strong>
                        <time class="small text-secondary"><?= htmlspecialchars($review['created_at']) ?></time>
                    </div>
                    <div class="text-warning mb-2"><?= str_repeat('★', (int)$review['rating']) ?><?= str_repeat('☆', 5 - (int)$review['rating']) ?></div>
                    <p class="mb-2"><?= nl2br(htmlspecialchars($review['content'])) ?></p>
                    <?php if (!empty($review['admin_response'])): ?>
                        <div class="bg-light rounded-3 p-3"><strong>Phản hồi từ cửa hàng</strong><p class="mb-0 mt-1"><?= nl2br(htmlspecialchars($review['admin_response'])) ?></p></div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-secondary">Sản phẩm chưa có đánh giá được duyệt.</p>
        <?php endif; ?>

        <?php if (!auth()): ?>
            <p class="mb-0"><a href="<?= route_url('login') ?>">Đăng nhập</a> để gửi đánh giá.</p>
        <?php elseif ($userReview): ?>
            <div class="alert alert-info mb-0">
                <?= $userReview['status'] === 'pending' ? 'Đánh giá của bạn đang chờ cửa hàng duyệt.' : 'Bạn đã gửi đánh giá cho sản phẩm này.' ?>
            </div>
        <?php else: ?>
            <form action="<?= route_url('review-submit') ?>" method="POST" class="mt-4">
                <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                <div class="mb-3">
                    <label class="form-label fw-bold" for="reviewRating">Số sao</label>
                    <select class="form-select" id="reviewRating" name="rating" required style="max-width:180px">
                        <option value="5">5 sao</option><option value="4">4 sao</option><option value="3">3 sao</option><option value="2">2 sao</option><option value="1">1 sao</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold" for="reviewContent">Đánh giá của bạn</label>
                    <textarea class="form-control" id="reviewContent" name="content" rows="4" maxlength="3000" required></textarea>
                </div>
                <button class="btn btn-primary" type="submit">Gửi đánh giá</button>
                <small class="text-secondary ms-2">Đánh giá sẽ hiển thị sau khi được duyệt.</small>
            </form>
        <?php endif; ?>
    </section>

    <!-- Sản Phẩm Liên Quan -->
    <?php if (!empty($relatedProducts)): ?>
        <div class="my-5">
            <h4 class="fw-bold mb-4">Sản Phẩm Cùng Phân Khúc</h4>
            <div class="row g-4">
                <?php foreach ($relatedProducts as $rel): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="product-card h-100 d-flex flex-column">
                            <div class="product-img-box">
                                <a href="<?= route_url('product-detail', ['id' => $rel['id']]) ?>">
                                    <img src="<?= htmlspecialchars(image_src($rel['image_url'] ?: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600')) ?>" alt="<?= htmlspecialchars($rel['name']) ?>" loading="lazy">
                                </a>
                                <?php if (!empty($rel['brand_name'])): ?>
                                    <span class="badge-brand"><?= htmlspecialchars($rel['brand_name']) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="p-3 d-flex flex-column flex-grow-1">
                                <h6 class="fw-bold mb-2 flex-grow-1">
                                    <a href="<?= route_url('product-detail', ['id' => $rel['id']]) ?>" class="text-dark text-decoration-none">
                                        <?= htmlspecialchars($rel['name']) ?>
                                    </a>
                                </h6>
                                <div class="mt-auto">
                                    <div class="fw-bold text-danger fs-5 mb-2"><?= format_price($rel['min_sale_price'] ?: $rel['min_price']) ?></div>
                                    <a href="<?= route_url('product-detail', ['id' => $rel['id']]) ?>" class="btn btn-outline-dark w-100 rounded-pill btn-sm">Xem Ngay</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Bảng Đo Size Giày -->
<div class="modal fade" id="sizeGuideModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-ruler me-2 text-danger"></i> Bảng Quy Đổi Size Giày Chuẩn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Size (VN/EU)</th>
                            <th>Chiều dài chân (cm)</th>
                            <th>Size US</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>38</td><td>23.5 cm</td><td>6.0</td></tr>
                        <tr><td>39</td><td>24.5 cm</td><td>6.5</td></tr>
                        <tr><td>40</td><td>25.0 cm</td><td>7.5</td></tr>
                        <tr><td>41</td><td>26.0 cm</td><td>8.5</td></tr>
                        <tr><td>42</td><td>26.5 cm</td><td>9.0</td></tr>
                        <tr><td>43</td><td>27.5 cm</td><td>10.0</td></tr>
                        <tr><td>44</td><td>28.0 cm</td><td>10.5</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.variant-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        const price = parseFloat(this.getAttribute('data-price')) || 0;
        const sale = parseFloat(this.getAttribute('data-sale')) || null;
        const stock = this.getAttribute('data-stock');
        const color = this.getAttribute('data-color');

        document.getElementById('variantStock').innerText = stock + ' đôi';
        document.getElementById('variantColor').innerText = color;

        if (sale && sale < price) {
            document.getElementById('displayPrice').innerText = new Intl.NumberFormat('vi-VN').format(sale) + ' ₫';
            const oldEl = document.getElementById('displayOldPrice');
            oldEl.innerText = new Intl.NumberFormat('vi-VN').format(price) + ' ₫';
            oldEl.classList.remove('d-none');
        } else {
            document.getElementById('displayPrice').innerText = new Intl.NumberFormat('vi-VN').format(price) + ' ₫';
            document.getElementById('displayOldPrice').classList.add('d-none');
        }
    });
});
</script>
