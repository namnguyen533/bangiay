<div class="container my-4">
    <?php
    $hasProductImage = !empty($product['image_url']);
    $mainImageUrl = $hasProductImage ? $product['image_url'] : 'uploads/products/no-product-image.svg';
    $mainImageAlt = $product['image_alt_text'] ?: ($hasProductImage ? $product['name'] : 'Ảnh sản phẩm chưa được cập nhật');
    ?>
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= route_url() ?>" class="text-decoration-none text-secondary">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= route_url('products') ?>" class="text-decoration-none text-secondary">Sản phẩm</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['name']) ?></li>
        </ol>
    </nav>

    <div class="row g-4 g-lg-5">
        <!-- Cột Gallery Ảnh Sản Phẩm -->
        <div class="col-lg-6">
            <div id="product3dStage" class="product-3d-stage bg-white p-3 rounded-4 border shadow-sm mb-2 text-center position-relative overflow-hidden">
                <img id="mainProductImg"
                     src="<?= htmlspecialchars(image_src($mainImageUrl)) ?>"
                     alt="<?= htmlspecialchars($mainImageAlt) ?>"
                     class="img-fluid rounded-3 main-image-zoom"
                     draggable="false">
                <?php if (!empty($product['brand_name'])): ?>
                    <span class="badge-brand"><?= htmlspecialchars($product['brand_name']) ?></span>
                <?php endif; ?>
            </div>
            <div class="product-3d-controls bg-white border rounded-4 p-3 mb-2">
                <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                    <span class="small fw-semibold text-dark"><i class="fa-solid fa-cube me-1 text-danger"></i> Xoay xem sản phẩm</span>
                    <div class="d-flex gap-2">
                        <button id="product3dSpin" type="button" class="btn btn-sm btn-outline-dark rounded-pill">
                            <i class="fa-solid fa-rotate me-1"></i> Tự xoay
                        </button>
                        <button id="product3dReset" type="button" class="btn btn-sm btn-light rounded-pill" aria-label="Đặt lại góc xoay">
                            Đặt lại
                        </button>
                    </div>
                </div>
                <input id="product3dAngle" class="form-range mb-1" type="range" min="0" max="360" value="0" aria-label="Góc xoay ảnh sản phẩm">
                <div class="d-flex justify-content-between small text-secondary">
                    <span>Kéo trực tiếp ảnh hoặc thanh trượt để xoay</span>
                    <span id="product3dAngleLabel" aria-live="polite">0°</span>
                </div>
                <p class="small text-secondary mb-0 mt-2">Hiệu ứng 3D mô phỏng từ ảnh hiện có; muốn xem chính xác mặt sau cần ảnh nhiều góc hoặc mô hình 3D.</p>
            </div>
            <p id="galleryImageCaption" class="small text-secondary mb-2">
                <?= htmlspecialchars($mainImageAlt) ?>
            </p>

            <!-- Gallery ảnh thu nhỏ (Thumbnails) -->
            <?php
            $galleryImages = $product['images'] ?? [];
            if (empty($galleryImages) && !empty($product['image_url'])) {
                $galleryImages[] = [
                    'image_url' => $product['image_url'],
                    'alt_text' => $product['image_alt_text'] ?: $product['name'],
                ];
            }
            ?>
            <?php if (!empty($galleryImages)): ?>
                <div class="d-flex gap-2 overflow-auto py-2 gallery-thumbnails" role="group" aria-label="Ảnh sản phẩm">
                    <?php foreach ($galleryImages as $idx => $img): ?>
                        <button type="button"
                                class="thumbnail-wrapper rounded-3 border p-1 bg-white <?= $idx === 0 ? 'border-danger border-2' : '' ?>"
                                style="width: 82px; height: 82px; flex-shrink: 0; cursor: pointer;"
                                data-image-url="<?= htmlspecialchars(image_src($img['image_url'])) ?>"
                                data-image-alt="<?= htmlspecialchars($img['alt_text'] ?: $product['name']) ?>"
                                aria-label="<?= htmlspecialchars($img['alt_text'] ?: 'Ảnh sản phẩm ' . ($idx + 1)) ?>"
                                aria-pressed="<?= $idx === 0 ? 'true' : 'false' ?>">
                            <img src="<?= htmlspecialchars(image_src($img['image_url'])) ?>"
                                 alt=""
                                 class="w-100 h-100 rounded-2"
                                 style="object-fit: cover;">
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Cột Thông Tin & Chọn Biến Thể -->
        <div class="col-lg-6">
            <div class="bg-white p-4 rounded-4 border shadow-sm">
                <!-- Hãng & Danh mục & Đánh giá nhanh -->
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-dark px-3 py-2 text-uppercase fw-bold"><?= htmlspecialchars($product['brand_name'] ?? 'Sneaker') ?></span>
                        <span class="text-secondary small"><?= htmlspecialchars($product['category_name'] ?? 'Giày thời trang') ?></span>
                    </div>
                    <?php if (!empty($ratingStats) && $ratingStats['total_reviews'] > 0): ?>
                        <div class="d-flex align-items-center gap-1 text-warning small fw-bold">
                            <span>★</span>
                            <span class="text-dark"><?= $ratingStats['avg_rating'] ?></span>
                            <span class="text-secondary">(<?= $ratingStats['total_reviews'] ?> đánh giá)</span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Tên Giày -->
                <h2 class="fw-bold text-dark mb-3"><?= htmlspecialchars($product['name']) ?></h2>

                <!-- Mã SKU Biến Thể -->
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
                $defaultStock = (int)($firstVariant['stock_quantity'] ?? 0);
                $defaultSku = $firstVariant['sku'] ?? 'SKU-Chưa có';
                ?>
                <div class="mb-3 d-flex align-items-center gap-3">
                    <span class="small text-secondary">Mã SKU: <strong id="variantSku" class="text-dark font-monospace"><?= htmlspecialchars($defaultSku) ?></strong></span>
                    <span class="text-muted">|</span>
                    <span class="small text-secondary">Tình trạng: <span id="variantStockBadge">
                        <?php if ($defaultStock > 5): ?>
                            <strong class="text-success fw-bold"><i class="fa-solid fa-circle-check me-1"></i>Còn <?= $defaultStock ?> đôi trong kho</strong>
                        <?php elseif ($defaultStock > 0): ?>
                            <strong class="text-warning fw-bold"><i class="fa-solid fa-triangle-exclamation me-1"></i>Chỉ còn <?= $defaultStock ?> đôi (Sắp hết!)</strong>
                        <?php else: ?>
                            <strong class="text-danger fw-bold"><i class="fa-solid fa-circle-xmark me-1"></i>Tạm hết hàng</strong>
                        <?php endif; ?>
                    </span></span>
                </div>

                <!-- Hiển thị Giá Động -->
                <div class="p-3 bg-light rounded-3 mb-4 d-flex flex-wrap align-items-baseline gap-3">
                    <?php if ($defaultSale && $defaultSale < $defaultPrice): ?>
                        <span id="displayPrice" class="fs-2 fw-black text-danger"><?= format_price($defaultSale) ?></span>
                        <span id="displayOldPrice" class="fs-5 text-decoration-line-through text-secondary"><?= format_price($defaultPrice) ?></span>
                        <span id="discountTag" class="badge bg-danger">Tiết kiệm <?= format_price($defaultPrice - $defaultSale) ?></span>
                    <?php else: ?>
                        <span id="displayPrice" class="fs-2 fw-black text-danger"><?= format_price($defaultPrice) ?></span>
                        <span id="displayOldPrice" class="fs-5 text-decoration-line-through text-secondary d-none"></span>
                        <span id="discountTag" class="badge bg-danger d-none"></span>
                    <?php endif; ?>
                </div>

                <!-- Form Thêm Giỏ Hàng & Chọn Biến Thể -->
                <form action="<?= route_url('cart-add') ?>" method="POST" id="addToCartForm">
                    <!-- Chọn Size & Biến thể -->
                    <div class="mb-4">
                        <label class="form-label fw-bold d-flex justify-content-between align-items-center">
                            <span>1. Chọn Kích Thước (Size)</span>
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
                                            data-sku="<?= htmlspecialchars($v['sku'] ?? 'SKU-' . $v['id']) ?>"
                                            data-size="<?= htmlspecialchars($v['size']) ?>"
                                            data-color="<?= htmlspecialchars($v['color']) ?>"
                                            <?= ((int)$v['id'] === (int)($firstVariant['id'] ?? 0)) ? 'checked' : '' ?>
                                            <?= ($v['stock_quantity'] <= 0 || $v['color'] !== $selectedColor) ? 'disabled' : '' ?>>
                                        <label class="btn btn-outline-secondary px-3 py-2 fw-bold variant-label" for="variant_<?= $v['id'] ?>" style="min-width: 72px;">
                                            Size <?= htmlspecialchars($v['size']) ?>
                                            <?php if ($v['stock_quantity'] <= 0): ?>
                                                <small class="d-block text-danger" style="font-size: 0.7rem;">Hết hàng</small>
                                            <?php endif; ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="mt-3 p-2 bg-light rounded-2 small text-secondary d-flex flex-wrap justify-content-between">
                                <span>Phối màu: <strong id="variantColor" class="text-dark"><?= htmlspecialchars($firstVariant['color'] ?? 'Tiêu chuẩn') ?></strong></span>
                                <span>Size đang chọn: <strong id="variantSizeDisplay" class="text-danger"><?= htmlspecialchars($firstVariant['size'] ?? '38') ?></strong></span>
                            </div>
                            <p id="colorStockMessage" class="small text-danger mt-2 mb-0 d-none">Màu này hiện đã hết hàng.</p>
                        <?php else: ?>
                            <div class="alert alert-warning py-2 small mb-0">Sản phẩm hiện đang tạm hết toàn bộ các size.</div>
                        <?php endif; ?>
                    </div>

                    <!-- Số lượng & Nút mua -->
                    <div class="row g-3 align-items-center mb-4">
                        <div class="col-sm-4 col-5">
                            <label class="form-label fw-bold small">2. Số Lượng</label>
                            <div class="input-group">
                                <button type="button" class="btn btn-outline-secondary" onclick="decreaseQuantity()">-</button>
                                <input type="number" id="quantityInput" name="quantity" class="form-control text-center" value="1" min="1" max="<?= max(1, $defaultStock) ?>">
                                <button type="button" class="btn btn-outline-secondary" onclick="increaseQuantity()">+</button>
                            </div>
                        </div>

                        <div class="col-sm-8 col-7 pt-4 d-flex gap-2">
                            <button type="submit" id="btnAddToCart" class="btn btn-primary btn-lg flex-grow-1 rounded-pill fw-bold shadow-sm" <?= empty($product['variants']) || $defaultStock <= 0 ? 'disabled' : '' ?>>
                                <i class="fa-solid fa-cart-plus me-2"></i> <span id="addToCartText"><?= ($defaultStock <= 0) ? 'Tạm Hết Hàng' : 'Thêm Vào Giỏ Hàng' ?></span>
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
                        <div class="col-6"><i class="fa-solid fa-truck text-danger me-2"></i> Phí ship 30.000đ, miễn phí từ 2.000.000đ</div>
                        <div class="col-6"><i class="fa-solid fa-shield text-danger me-2"></i> Bảo hành chính hãng 12 tháng</div>
                        <div class="col-6"><i class="fa-solid fa-rotate-left text-danger me-2"></i> Đổi size miễn phí trong 30 ngày</div>
                        <div class="col-6"><i class="fa-solid fa-box-check text-danger me-2"></i> Kiểm tra hàng trước khi thanh toán</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Khối Mô Tả Chi Tiết & Thông Số Kỹ Thuật -->
    <div class="my-5 bg-white p-4 p-md-5 rounded-4 border shadow-sm">
        <h4 class="fw-bold mb-4 border-bottom pb-3"><i class="fa-solid fa-file-lines me-2 text-danger"></i> Mô Tả Chi Tiết & Thông Số Kỹ Thuật</h4>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="lead fs-6 text-secondary" style="line-height: 1.8;">
                    <?= \App\Core\DescriptionSanitizer::sanitize((string)($product['description'] ?? 'Mẫu giày sneaker cao cấp được thiết kế tinh xảo, êm ái cho đôi chân, phong cách trẻ trung và năng động.')) ?>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="bg-light p-3 rounded-3 border">
                    <h6 class="fw-bold mb-3 text-dark">Thông tin cơ bản</h6>
                    <ul class="list-unstyled small text-secondary mb-0">
                        <li class="py-1 border-bottom d-flex justify-content-between"><span>Thương hiệu:</span> <strong class="text-dark"><?= htmlspecialchars($product['brand_name'] ?? 'Chính hãng') ?></strong></li>
                        <li class="py-1 border-bottom d-flex justify-content-between"><span>Danh mục:</span> <strong class="text-dark"><?= htmlspecialchars($product['category_name'] ?? 'Sneaker') ?></strong></li>
                        <li class="py-1 border-bottom d-flex justify-content-between"><span>Kiểu dáng:</span> <strong class="text-dark">Thể thao / Đi chơi</strong></li>
                        <li class="py-1 border-bottom d-flex justify-content-between"><span>Bảo hành:</span> <strong class="text-dark">12 tháng</strong></li>
                        <li class="py-1 d-flex justify-content-between"><span>Chính sách đổi trả:</span> <strong class="text-dark">Trong vòng 30 ngày</strong></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Hệ Thống Đánh Giá & Rating Sao (Chỉ khách đã mua mới được gửi) -->
    <section class="my-5 bg-white p-4 p-md-5 rounded-4 border shadow-sm">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 border-bottom pb-3 mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="fa-solid fa-star text-warning me-2"></i>Đánh Giá & Nhận Xét Từ Khách Hàng</h4>
                <span class="text-secondary small">Trải nghiệm thực tế từ những khách hàng đã mua sản phẩm</span>
            </div>
            <div class="badge bg-light text-dark border px-3 py-2 fs-6">
                <?= count($reviews) ?> đánh giá đã duyệt
            </div>
        </div>

        <!-- Khối Tổng Quan Điểm Số & Phân Bổ Sao -->
        <div class="row align-items-center bg-light p-4 rounded-4 mb-4 g-4">
            <div class="col-md-4 text-center border-md-end">
                <div class="display-3 fw-black text-danger mb-1"><?= $ratingStats['avg_rating'] ?? 5.0 ?></div>
                <div class="text-warning fs-5 mb-2">
                    <?php
                    $avgRound = round($ratingStats['avg_rating'] ?? 5.0);
                    echo str_repeat('★', (int)$avgRound) . str_repeat('☆', 5 - (int)$avgRound);
                    ?>
                </div>
                <div class="small text-secondary"><?= $ratingStats['total_reviews'] ?? count($reviews) ?> nhận xét đã xác thực</div>
            </div>
            <div class="col-md-8">
                <?php
                $totalRev = max(1, $ratingStats['total_reviews'] ?? count($reviews));
                for ($s = 5; $s >= 1; $s--):
                    $countStar = $ratingStats['star_' . $s] ?? 0;
                    $pct = round(($countStar / $totalRev) * 100);
                ?>
                    <div class="d-flex align-items-center gap-2 mb-1 small">
                        <span style="width: 45px;" class="text-secondary"><?= $s ?> sao</span>
                        <div class="progress flex-grow-1" style="height: 8px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $pct ?>%;"></div>
                        </div>
                        <span style="width: 40px;" class="text-end text-secondary"><?= $countStar ?></span>
                    </div>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Danh Sách Đánh Giá Đã Duyệt -->
        <div class="mb-5">
            <?php if (!empty($reviews)): ?>
                <?php foreach ($reviews as $review): ?>
                    <article class="border-bottom py-3">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px; font-size: 1.1rem;">
                                    <?= mb_substr($review['full_name'], 0, 1) ?>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($review['full_name']) ?></div>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.7rem;">
                                        <i class="fa-solid fa-circle-check me-1"></i>Đã mua hàng tại SoleVibe
                                    </span>
                                </div>
                            </div>
                            <time class="small text-secondary"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($review['created_at']))) ?></time>
                        </div>
                        <div class="text-warning my-2 fs-6">
                            <?= str_repeat('★', (int)$review['rating']) ?><?= str_repeat('☆', 5 - (int)$review['rating']) ?>
                        </div>
                        <p class="text-secondary mb-2" style="line-height: 1.6;"><?= nl2br(htmlspecialchars($review['content'])) ?></p>
                        <?php if (!empty($review['admin_response'])): ?>
                            <div class="bg-light rounded-3 p-3 mt-2 border-start border-danger border-4">
                                <div class="fw-bold text-dark small"><i class="fa-solid fa-store me-1 text-danger"></i> Phản hồi từ cửa hàng SoleVibe:</div>
                                <p class="mb-0 mt-1 small text-secondary"><?= nl2br(htmlspecialchars($review['admin_response'])) ?></p>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-4 bg-light rounded-3">
                    <i class="fa-regular fa-comment-dots fs-2 text-secondary mb-2"></i>
                    <p class="text-secondary mb-0">Sản phẩm chưa có đánh giá nào. Hãy là người đầu tiên chia sẻ cảm nhận sau khi mua hàng!</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Khối Gửi Đánh Giá: Logic Kiểm Tra Đã Mua Hàng -->
        <div class="p-4 rounded-4 border bg-light">
            <h5 class="fw-bold mb-3"><i class="fa-solid fa-pen-to-square me-2 text-danger"></i>Gửi Đánh Giá Của Bạn</h5>

            <?php if (!auth()): ?>
                <div class="alert alert-secondary d-flex align-items-center justify-content-between mb-0">
                    <div>
                        <i class="fa-solid fa-lock me-2 text-secondary"></i>
                        <span>Vui lòng <strong>đăng nhập</strong> tài khoản để có thể đánh giá sản phẩm.</span>
                    </div>
                    <a href="<?= route_url('login') ?>" class="btn btn-primary btn-sm rounded-pill px-4">Đăng Nhập Ngay</a>
                </div>
            <?php elseif (empty($hasPurchased)): ?>
                <!-- Đã đăng nhập nhưng chưa mua sản phẩm này -->
                <div class="alert alert-info d-flex align-items-center gap-3 mb-0">
                    <i class="fa-solid fa-shield-halved fs-3 text-info"></i>
                    <div>
                        <div class="fw-bold">Chính sách đánh giá xác thực:</div>
                        <div class="small">Chỉ khách hàng <strong>đã mua sản phẩm này thành công</strong> mới có thể gửi đánh giá và nhận xét sao. Hãy đặt hàng ngay để trải nghiệm sản phẩm nhé!</div>
                    </div>
                </div>
            <?php elseif ($userReview): ?>
                <!-- Đã mua và đã gửi đánh giá -->
                <div class="alert alert-success d-flex align-items-center gap-2 mb-0">
                    <i class="fa-solid fa-circle-check fs-4 text-success"></i>
                    <div>
                        <strong>Cảm ơn bạn!</strong>
                        <?= $userReview['status'] === 'pending' ? 'Đánh giá của bạn đã được tiếp nhận và đang chờ duyệt hiển thị.' : 'Đánh giá của bạn đã được phê duyệt và hiển thị.' ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- Đã mua thành công và chưa đánh giá -> Hiển thị Form Đánh Giá 5 Sao Trực Quan -->
                <div class="alert alert-success d-flex align-items-center gap-2 mb-3 py-2 small">
                    <i class="fa-solid fa-badge-check text-success"></i>
                    <span>Bạn đã mua sản phẩm này thành công! Hãy chia sẻ đánh giá chân thực để giúp cộng đồng nhé.</span>
                </div>

                <form action="<?= route_url('review-submit') ?>" method="POST" id="reviewForm">
                    <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                    <input type="hidden" name="rating" id="selectedRating" value="5">

                    <!-- Bộ chọn số sao trực quan tương tác -->
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Chọn mức độ hài lòng:</label>
                        <div class="d-flex align-items-center gap-2">
                            <div class="star-rating-widget d-flex gap-1 fs-3 cursor-pointer" id="starWidget">
                                <span class="star-item text-warning" data-val="1">★</span>
                                <span class="star-item text-warning" data-val="2">★</span>
                                <span class="star-item text-warning" data-val="3">★</span>
                                <span class="star-item text-warning" data-val="4">★</span>
                                <span class="star-item text-warning" data-val="5">★</span>
                            </div>
                            <span id="starRatingLabel" class="badge bg-warning text-dark fw-bold ms-2">Tuyệt vời (5 sao)</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary" for="reviewContent">Nội dung nhận xét chi tiết:</label>
                        <textarea class="form-control bg-white" id="reviewContent" name="content" rows="4" maxlength="3000" placeholder="Chất lượng giày, độ êm, form dáng chuẩn size không, độ hoàn thiện..." required></textarea>
                    </div>

                    <button class="btn btn-primary rounded-pill px-4 fw-bold" type="submit">
                        <i class="fa-solid fa-paper-plane me-1"></i> Gửi Đánh Giá Ngay
                    </button>
                    <small class="text-secondary ms-3">Đánh giá sẽ hiển thị ngay sau khi được hệ thống duyệt.</small>
                </form>
            <?php endif; ?>
        </div>
    </section>

    <!-- Sản Phẩm Liên Quan (Cùng Phân Khúc / Danh Mục) -->
    <?php if (!empty($relatedProducts)): ?>
        <div class="my-5">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <span class="text-danger fw-bold text-uppercase small">Gợi ý cho bạn</span>
                    <h4 class="fw-bold mb-0">Sản Phẩm Cùng Phân Khúc</h4>
                </div>
                <a href="<?= route_url('products', ['category_id' => $product['category_id'] ?? '']) ?>" class="btn btn-outline-dark rounded-pill btn-sm px-3">
                    Xem Thêm <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>

            <div class="row g-4">
                <?php foreach ($relatedProducts as $rel): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="product-card h-100 d-flex flex-column">
                            <div class="product-img-box">
                                <a href="<?= route_url('product-detail', ['id' => $rel['id']]) ?>">
                                    <img src="<?= htmlspecialchars(image_src($rel['image_url'] ?: 'uploads/products/no-product-image.svg')) ?>" alt="<?= htmlspecialchars($rel['name']) ?>" loading="lazy">
                                </a>
                                <?php if (!empty($rel['brand_name'])): ?>
                                    <span class="badge-brand"><?= htmlspecialchars($rel['brand_name']) ?></span>
                                <?php endif; ?>
                                <?php if (!empty($rel['min_sale_price']) && $rel['min_sale_price'] < $rel['min_price']): ?>
                                    <span class="badge-discount">Sale</span>
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
                                    <a href="<?= route_url('product-detail', ['id' => $rel['id']]) ?>" class="btn btn-outline-dark w-100 rounded-pill btn-sm fw-semibold">
                                        Xem Chi Tiết
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Bảng Đo Size Giày Chuẩn -->
<div class="modal fade" id="sizeGuideModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-ruler me-2 text-danger"></i> Bảng Quy Đổi Size Giày Chuẩn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="small text-secondary mb-3">Đo chiều dài bàn chân từ gót chân đến đầu ngón chân dài nhất để chọn size chuẩn xác:</p>
                <table class="table table-bordered table-striped text-center align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Size VN/EU</th>
                            <th>Chiều dài chân (cm)</th>
                            <th>Size US Men</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td><strong>38</strong></td><td>23.5 - 24.0 cm</td><td>6.0</td></tr>
                        <tr><td><strong>39</strong></td><td>24.1 - 24.5 cm</td><td>6.5</td></tr>
                        <tr><td><strong>40</strong></td><td>24.6 - 25.0 cm</td><td>7.5</td></tr>
                        <tr><td><strong>41</strong></td><td>25.1 - 26.0 cm</td><td>8.5</td></tr>
                        <tr><td><strong>42</strong></td><td>26.1 - 26.5 cm</td><td>9.0</td></tr>
                        <tr><td><strong>43</strong></td><td>26.6 - 27.5 cm</td><td>10.0</td></tr>
                        <tr><td><strong>44</strong></td><td>27.6 - 28.0 cm</td><td>10.5</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .product-3d-stage {
        min-height: 420px;
        display: flex;
        align-items: center;
        justify-content: center;
        perspective: 1100px;
        touch-action: pan-y;
        cursor: grab;
    }
    .product-3d-stage.is-dragging {
        cursor: grabbing;
    }
    .product-3d-stage #mainProductImg {
        width: 100%;
        max-height: 450px;
        object-fit: contain;
        transform: rotateY(0deg);
        transform-style: preserve-3d;
        transition: transform 0.12s ease-out, opacity 0.15s ease;
        user-select: none;
        -webkit-user-drag: none;
    }
    .product-3d-stage.is-dragging #mainProductImg {
        transition: none;
    }
</style>

<!-- JavaScript Điều Khiển Gallery Ảnh, Biến Thể Size/Màu & Rating Sao -->
<script>
// 1. Chuyển đổi ảnh trong Gallery
function changeGalleryImage(wrapper, imgUrl) {
    const mainImg = document.getElementById('mainProductImg');
    if (!mainImg) return;

    mainImg.style.opacity = '0.4';
    setTimeout(() => {
        mainImg.src = imgUrl;
        mainImg.alt = wrapper.dataset.imageAlt || mainImg.alt;
        mainImg.style.opacity = '1';
        setProductImageAngle(0);
    }, 150);
    const caption = document.getElementById('galleryImageCaption');
    if (caption) {
        caption.textContent = wrapper.dataset.imageAlt || '';
    }

    document.querySelectorAll('.thumbnail-wrapper').forEach(el => {
        el.classList.remove('border-danger', 'border-2');
        el.setAttribute('aria-pressed', 'false');
    });
    wrapper.classList.add('border-danger', 'border-2');
    wrapper.setAttribute('aria-pressed', 'true');
}

document.querySelectorAll('.thumbnail-wrapper').forEach(wrapper => {
    wrapper.addEventListener('click', () => {
        changeGalleryImage(wrapper, wrapper.dataset.imageUrl);
    });
});

// Xoay ảnh quanh trục dọc để tạo hiệu ứng xem 3D từ một ảnh sản phẩm.
const product3dStage = document.getElementById('product3dStage');
const product3dImage = document.getElementById('mainProductImg');
const product3dAngle = document.getElementById('product3dAngle');
const product3dAngleLabel = document.getElementById('product3dAngleLabel');
const product3dSpin = document.getElementById('product3dSpin');
const product3dReset = document.getElementById('product3dReset');
let productImageRotation = 0;
let productSpinFrame = null;
let productLastPointerX = null;

function setProductImageAngle(angle) {
    if (!product3dImage || !product3dAngle || !product3dAngleLabel) return;
    productImageRotation = ((angle % 360) + 360) % 360;
    product3dImage.style.transform = `rotateY(${productImageRotation}deg)`;
    product3dAngle.value = Math.round(productImageRotation);
    product3dAngleLabel.textContent = `${Math.round(productImageRotation)}°`;
}

function stopProductImageSpin() {
    if (productSpinFrame !== null) {
        cancelAnimationFrame(productSpinFrame);
        productSpinFrame = null;
    }
    if (product3dSpin) {
        product3dSpin.setAttribute('aria-pressed', 'false');
        product3dSpin.innerHTML = '<i class="fa-solid fa-rotate me-1"></i> Tự xoay';
    }
}

if (product3dStage && product3dImage && product3dAngle) {
    let previousSpinTime = 0;
    const spinFrame = (time) => {
        if (productSpinFrame === null) return;
        if (previousSpinTime) {
            setProductImageAngle(productImageRotation + (time - previousSpinTime) * 0.045);
        }
        previousSpinTime = time;
        productSpinFrame = requestAnimationFrame(spinFrame);
    };

    product3dStage.addEventListener('pointerdown', (event) => {
        if (event.target.closest('button')) return;
        stopProductImageSpin();
        productLastPointerX = event.clientX;
        product3dStage.classList.add('is-dragging');
        product3dStage.setPointerCapture(event.pointerId);
    });

    product3dStage.addEventListener('pointermove', (event) => {
        if (productLastPointerX === null) return;
        const deltaX = event.clientX - productLastPointerX;
        productLastPointerX = event.clientX;
        setProductImageAngle(productImageRotation + deltaX * 0.8);
    });

    const finishProductImageDrag = () => {
        productLastPointerX = null;
        product3dStage.classList.remove('is-dragging');
    };
    product3dStage.addEventListener('pointerup', finishProductImageDrag);
    product3dStage.addEventListener('pointercancel', finishProductImageDrag);
    product3dAngle.addEventListener('input', () => {
        stopProductImageSpin();
        setProductImageAngle(Number(product3dAngle.value));
    });
    product3dSpin.addEventListener('click', () => {
        if (productSpinFrame !== null) {
            stopProductImageSpin();
            return;
        }
        previousSpinTime = 0;
        product3dSpin.setAttribute('aria-pressed', 'true');
        product3dSpin.innerHTML = '<i class="fa-solid fa-pause me-1"></i> Dừng xoay';
        productSpinFrame = requestAnimationFrame(spinFrame);
    });
    product3dReset.addEventListener('click', () => {
        stopProductImageSpin();
        setProductImageAngle(0);
    });
}

// 2. Tăng giảm số lượng sản phẩm
function decreaseQuantity() {
    const q = document.getElementById('quantityInput');
    if (q && parseInt(q.value) > 1) {
        q.value = parseInt(q.value) - 1;
    }
}
function increaseQuantity() {
    const q = document.getElementById('quantityInput');
    if (q) {
        const maxVal = parseInt(q.getAttribute('max')) || 10;
        if (parseInt(q.value) < maxVal) {
            q.value = parseInt(q.value) + 1;
        }
    }
}

// 3. Xử lý logic chọn Size / Biến thể: cập nhật Giá, Tồn kho, SKU, Nút mua
document.querySelectorAll('.variant-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        const price = parseFloat(this.getAttribute('data-price')) || 0;
        const sale = parseFloat(this.getAttribute('data-sale')) || null;
        const stock = parseInt(this.getAttribute('data-stock')) || 0;
        const color = this.getAttribute('data-color') || 'Tiêu chuẩn';
        const size = this.getAttribute('data-size') || '';
        const sku = this.getAttribute('data-sku') || '';

        // Cập nhật thông tin text
        document.getElementById('variantColor').innerText = color;
        document.getElementById('variantSizeDisplay').innerText = size;
        document.getElementById('variantSku').innerText = sku;

        // Cập nhật tình trạng tồn kho
        const badgeEl = document.getElementById('variantStockBadge');
        const btnAdd = document.getElementById('btnAddToCart');
        const btnText = document.getElementById('addToCartText');
        const qInput = document.getElementById('quantityInput');

        if (stock > 5) {
            badgeEl.innerHTML = `<strong class="text-success fw-bold"><i class="fa-solid fa-circle-check me-1"></i>Còn ${stock} đôi trong kho</strong>`;
            btnAdd.removeAttribute('disabled');
            btnText.innerText = 'Thêm Vào Giỏ Hàng';
            qInput.setAttribute('max', stock);
        } else if (stock > 0) {
            badgeEl.innerHTML = `<strong class="text-warning fw-bold"><i class="fa-solid fa-triangle-exclamation me-1"></i>Chỉ còn ${stock} đôi (Sắp hết!)</strong>`;
            btnAdd.removeAttribute('disabled');
            btnText.innerText = 'Thêm Vào Giỏ Hàng';
            qInput.setAttribute('max', stock);
        } else {
            badgeEl.innerHTML = `<strong class="text-danger fw-bold"><i class="fa-solid fa-circle-xmark me-1"></i>Tạm hết hàng</strong>`;
            btnAdd.setAttribute('disabled', 'true');
            btnText.innerText = 'Tạm Hết Hàng';
            qInput.value = 1;
            qInput.setAttribute('max', 1);
        }

        // Cập nhật giá bán
        const displayPriceEl = document.getElementById('displayPrice');
        const displayOldPriceEl = document.getElementById('displayOldPrice');
        const discountTagEl = document.getElementById('discountTag');
        document.getElementById('colorStockMessage').classList.add('d-none');

        if (sale && sale < price) {
            displayPriceEl.innerText = new Intl.NumberFormat('vi-VN').format(sale) + ' ₫';
            displayOldPriceEl.innerText = new Intl.NumberFormat('vi-VN').format(price) + ' ₫';
            displayOldPriceEl.classList.remove('d-none');
            discountTagEl.innerText = 'Tiết kiệm ' + new Intl.NumberFormat('vi-VN').format(price - sale) + ' ₫';
            discountTagEl.classList.remove('d-none');
        } else {
            displayPriceEl.innerText = new Intl.NumberFormat('vi-VN').format(price) + ' ₫';
            displayOldPriceEl.classList.add('d-none');
            discountTagEl.classList.add('d-none');
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
            document.getElementById('colorStockMessage').classList.add('d-none');
            firstAvailableVariant.checked = true;
            firstAvailableVariant.dispatchEvent(new Event('change'));
        } else {
            const badgeEl = document.getElementById('variantStockBadge');
            badgeEl.innerHTML = '<strong class="text-danger fw-bold"><i class="fa-solid fa-circle-xmark me-1"></i>Tạm hết hàng</strong>';
            document.getElementById('colorStockMessage').classList.remove('d-none');
            document.getElementById('btnAddToCart').disabled = true;
            document.getElementById('addToCartText').innerText = 'Tạm Hết Hàng';
            document.getElementById('quantityInput').value = 1;
            document.getElementById('quantityInput').setAttribute('max', 1);
        }
    });
});

// 4. Xử lý Star Rating Widget tương tác trực quan
document.addEventListener('DOMContentLoaded', function() {
    const starWidget = document.getElementById('starWidget');
    const selectedRatingInput = document.getElementById('selectedRating');
    const starLabel = document.getElementById('starRatingLabel');
    if (!starWidget || !selectedRatingInput || !starLabel) return;

    const labels = {
        1: 'Rất tệ (1 sao)',
        2: 'Không hài lòng (2 sao)',
        3: 'Bình thường (3 sao)',
        4: 'Hài lòng (4 sao)',
        5: 'Tuyệt vời (5 sao)'
    };

    const stars = starWidget.querySelectorAll('.star-item');

    function updateStars(val) {
        stars.forEach(s => {
            const sVal = parseInt(s.getAttribute('data-val'));
            if (sVal <= val) {
                s.innerText = '★';
                s.classList.add('text-warning');
                s.classList.remove('text-secondary');
            } else {
                s.innerText = '☆';
                s.classList.remove('text-warning');
                s.classList.add('text-secondary');
            }
        });
        starLabel.innerText = labels[val] || `${val} sao`;
    }

    stars.forEach(star => {
        star.addEventListener('mouseenter', function() {
            const hoverVal = parseInt(this.getAttribute('data-val'));
            updateStars(hoverVal);
        });

        star.addEventListener('click', function() {
            const clickVal = parseInt(this.getAttribute('data-val'));
            selectedRatingInput.value = clickVal;
            updateStars(clickVal);
        });
    });

    starWidget.addEventListener('mouseleave', function() {
        const currentVal = parseInt(selectedRatingInput.value) || 5;
        updateStars(currentVal);
    });
});
</script>
