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
                <img id="mainProductImg" src="<?= htmlspecialchars($product['image_url'] ?: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600') ?>" alt="<?= htmlspecialchars($product['image_alt_text'] ?: $product['name']) ?>" class="img-fluid rounded-3" style="max-height: 450px; object-fit: contain; width: 100%;">
                <?php if (strpos($product['image_alt_text'] ?? '', 'Stock reference:') === 0): ?>
                    <small class="d-block text-secondary mt-2">Ảnh minh họa, có thể khác mẫu giày thực tế.</small>
                <?php endif; ?>
            </div>

            <?php
            $galleryImages = array_values(array_filter(
                $product['images'] ?? [],
                static fn($img) => (int)($img['is_primary'] ?? 0) !== 1
            ));
            ?>
            <?php if (!empty($galleryImages)): ?>
                <div class="d-flex gap-2 overflow-auto py-2">
                    <?php foreach ($galleryImages as $img): ?>
                        <button type="button" class="btn p-0 border rounded-3 thumbnail-button" aria-label="Xem <?= htmlspecialchars($img['alt_text'] ?: $product['name']) ?>" data-image="<?= htmlspecialchars($img['image_url']) ?>" data-alt="<?= htmlspecialchars($img['alt_text'] ?: $product['name']) ?>">
                            <img src="<?= htmlspecialchars($img['image_url']) ?>" alt="" class="rounded-3" style="width: 80px; height: 80px; object-fit: cover;">
                        </button>
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
                $firstVariant = null;
                foreach ($product['variants'] as $variant) {
                    if ((int)$variant['stock_quantity'] > 0) {
                        $firstVariant = $variant;
                        break;
                    }
                }
                $firstVariant = $firstVariant ?? ($product['variants'][0] ?? null);
                $defaultPrice = $firstVariant['price'] ?? 0;
                $defaultSale = $firstVariant['sale_price'] ?? null;
                $productColors = array_values(array_unique(array_column($product['variants'], 'color')));
                $selectedColor = $firstVariant['color'] ?? '';
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
                            <div class="mb-3">
                                <label class="form-label fw-bold d-block">Chọn Màu Giày</label>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach ($productColors as $colorIndex => $color): ?>
                                        <?php
                                        $colorInStock = false;
                                        foreach ($product['variants'] as $variant) {
                                            if ($variant['color'] === $color && (int)$variant['stock_quantity'] > 0) {
                                                $colorInStock = true;
                                                break;
                                            }
                                        }
                                        ?>
                                        <div>
                                            <input type="radio" class="btn-check color-radio" name="product_color" id="color_<?= $colorIndex ?>" value="<?= htmlspecialchars($color) ?>" <?= ($color === $selectedColor) ? 'checked' : '' ?> <?= !$colorInStock ? 'disabled' : '' ?>>
                                            <label class="btn btn-outline-secondary px-3 py-2 fw-semibold" for="color_<?= $colorIndex ?>">
                                                <?= htmlspecialchars($color) ?><?= !$colorInStock ? ' (Hết)' : '' ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($product['variants'] as $idx => $v): ?>
                                    <div class="variant-option <?= ($v['color'] !== $selectedColor) ? 'd-none' : '' ?>" data-color="<?= htmlspecialchars($v['color']) ?>">
                                        <input type="radio" class="btn-check variant-radio" name="variant_id" id="variant_<?= $v['id'] ?>" value="<?= $v['id'] ?>" 
                                            data-price="<?= $v['price'] ?>" 
                                            data-sale="<?= $v['sale_price'] ?? '' ?>" 
                                            data-stock="<?= $v['stock_quantity'] ?>"
                                            data-color="<?= htmlspecialchars($v['color']) ?>"
                                            <?= ((int)$v['id'] === (int)($firstVariant['id'] ?? 0)) ? 'checked' : '' ?>
                                            <?= ($v['stock_quantity'] <= 0 || $v['color'] !== $selectedColor) ? 'disabled' : '' ?>>
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
                                Tồn kho: <strong id="variantStock" class="text-success"><?= $firstVariant['stock_quantity'] ?? 0 ?> đôi</strong>
                            </div>
                            <p id="colorStockMessage" class="small text-danger mt-2 mb-0 d-none">Màu này hiện đã hết hàng.</p>
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

                        <div class="col-sm-8 col-7 pt-4">
                            <button type="submit" id="addToCartButton" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold" <?= empty($product['variants']) || (int)($firstVariant['stock_quantity'] ?? 0) <= 0 ? 'disabled' : '' ?>>
                                <i class="fa-solid fa-cart-plus me-2"></i> Thêm Vào Giỏ Hàng
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Cam Kết Khách Hàng -->
                <div class="border-top pt-4">
                    <div class="row g-2 text-secondary small">
                        <div class="col-6"><i class="fa-solid fa-truck text-danger me-2"></i> Phí ship 30.000đ, miễn phí từ 2.000.000đ</div>
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
                                    <img src="<?= htmlspecialchars($rel['image_url'] ?: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600') ?>" alt="<?= htmlspecialchars($rel['name']) ?>" loading="lazy">
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
document.querySelectorAll('.thumbnail-button').forEach(button => {
    button.addEventListener('click', function() {
        const mainImage = document.getElementById('mainProductImg');
        mainImage.src = this.dataset.image;
        mainImage.alt = this.dataset.alt;
    });
});

document.querySelectorAll('.variant-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        const price = parseFloat(this.getAttribute('data-price')) || 0;
        const sale = parseFloat(this.getAttribute('data-sale')) || null;
        const stock = this.getAttribute('data-stock');
        const color = this.getAttribute('data-color');

        document.getElementById('variantStock').innerText = stock > 0 ? stock + ' đôi' : 'Hết hàng';
        document.getElementById('colorStockMessage').classList.add('d-none');
        document.getElementById('addToCartButton').disabled = stock <= 0;

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

document.querySelectorAll('.color-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        let firstAvailableVariant = null;

        document.querySelectorAll('.variant-option').forEach(option => {
            const variant = option.querySelector('.variant-radio');
            const isSelectedColor = option.dataset.color === this.value;
            option.classList.toggle('d-none', !isSelectedColor);
            variant.disabled = !isSelectedColor || parseInt(variant.dataset.stock, 10) <= 0;

            if (isSelectedColor && !variant.disabled && !firstAvailableVariant) {
                firstAvailableVariant = variant;
            }
        });

        if (firstAvailableVariant) {
            firstAvailableVariant.checked = true;
            firstAvailableVariant.dispatchEvent(new Event('change'));
        } else {
            document.getElementById('variantStock').innerText = 'Hết hàng';
            document.getElementById('colorStockMessage').classList.remove('d-none');
            document.getElementById('addToCartButton').disabled = true;
        }
    });
});
</script>
