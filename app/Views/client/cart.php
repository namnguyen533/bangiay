<div class="container my-5">
    <h3 class="fw-bold mb-4"><i class="fa-solid fa-cart-shopping me-2 text-danger"></i> Giỏ Hàng Của Bạn</h3>

    <?php if (!empty($items)): ?>
        <div class="row g-4">
            <!-- Danh Sách Sản Phẩm Trong Giỏ -->
            <div class="col-lg-8">
                <div class="bg-white p-4 rounded-4 border shadow-sm">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" style="min-width: 260px;">Sản Phẩm Giày</th>
                                    <th scope="col">Đơn Giá</th>
                                    <th scope="col" style="width: 140px;">Số Lượng</th>
                                    <th scope="col">Thành Tiền</th>
                                    <th scope="col" class="text-end">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="<?= htmlspecialchars($item['image_url'] ?: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600') ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" class="rounded-3 border" style="width: 70px; height: 70px; object-fit: cover;">
                                                <div>
                                                    <h6 class="mb-1 fw-bold">
                                                        <a href="<?= route_url('product-detail', ['id' => $item['product_id']]) ?>" class="text-dark text-decoration-none">
                                                            <?= htmlspecialchars($item['product_name']) ?>
                                                        </a>
                                                    </h6>
                                                    
                                                    <!-- Đổi Size / Màu sắc -->
                                                    <?php if (!empty($item['siblings']) && count($item['siblings']) > 1): ?>
                                                        <form action="<?= route_url('cart-change-variant') ?>" method="POST" class="d-inline-flex align-items-center gap-1 mt-1">
                                                            <input type="hidden" name="old_variant_id" value="<?= $item['variant_id'] ?>">
                                                            <span class="small text-secondary">Phân loại:</span>
                                                            <select name="new_variant_id" class="form-select form-select-sm py-0 ps-2 pe-4" style="font-size: 0.8rem; height: 26px;" onchange="this.form.submit()">
                                                                <?php foreach ($item['siblings'] as $sib): ?>
                                                                    <option value="<?= $sib['id'] ?>" <?= ($sib['id'] == $item['variant_id']) ? 'selected' : '' ?> <?= ($sib['stock_quantity'] <= 0) ? 'disabled' : '' ?>>
                                                                        Size <?= $sib['size'] ?> - <?= $sib['color'] ?> (Kho: <?= $sib['stock_quantity'] ?>)
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </form>
                                                    <?php else: ?>
                                                        <div class="small text-secondary">
                                                            Size: <span class="badge bg-light text-dark border"><?= htmlspecialchars($item['size']) ?></span>
                                                            | Màu: <?= htmlspecialchars($item['color']) ?>
                                                        </div>
                                                    <?php endif; ?>

                                                    <div class="small mt-1 text-muted">
                                                        Tồn kho: <span class="fw-semibold <?= ($item['stock'] < 5) ? 'text-danger' : 'text-success' ?>"><?= $item['stock'] ?> đôi</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="fw-semibold"><?= format_price($item['price']) ?></td>
                                        <td>
                                            <form action="<?= route_url('cart-update') ?>" method="POST" class="d-flex align-items-center gap-1">
                                                <input type="hidden" name="variant_id" value="<?= $item['variant_id'] ?>">
                                                <button type="button" class="btn btn-sm btn-outline-secondary px-2" onclick="let input=this.nextElementSibling; if(input.value>1){input.value--; this.form.submit();}">-</button>
                                                <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock'] ?>" class="form-control form-control-sm text-center" style="width: 50px;" onchange="this.form.submit()">
                                                <button type="button" class="btn btn-sm btn-outline-secondary px-2" onclick="let input=this.previousElementSibling; if(input.value < <?= $item['stock'] ?>){input.value++; this.form.submit();} else { alert('Đã đạt số lượng tối đa trong kho!'); }">+</button>
                                            </form>
                                        </td>
                                        <td class="fw-bold text-danger"><?= format_price($item['price'] * $item['quantity']) ?></td>
                                        <td class="text-end">
                                            <a href="<?= route_url('cart-remove', ['variant_id' => $item['variant_id']]) ?>" class="btn btn-sm btn-outline-danger" title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn bỏ sản phẩm này?');">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="<?= route_url('products') ?>" class="btn btn-outline-secondary rounded-pill">
                            <i class="fa-solid fa-arrow-left me-1"></i> Tiếp Tục Mua Sắm
                        </a>
                        <span class="small text-secondary">
                            <i class="fa-solid fa-shield-check text-success me-1"></i> Kiểm tra hàng và đổi size miễn phí
                        </span>
                    </div>
                </div>

                <!-- Danh Sách Mã Giảm Giá Đang Có -->
                <?php if (!empty($availableVouchers)): ?>
                    <div class="bg-white p-4 rounded-4 border shadow-sm mt-4">
                        <h6 class="fw-bold mb-3"><i class="fa-solid fa-ticket text-danger me-2"></i> Mã Giảm Giá Dành Cho Bạn</h6>
                        <div class="row g-2">
                            <?php foreach ($availableVouchers as $voc): ?>
                                <div class="col-md-6">
                                    <div class="border rounded-3 p-3 bg-light d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-bold text-danger"><?= htmlspecialchars($voc['code']) ?></div>
                                            <small class="text-secondary d-block"><?= htmlspecialchars($voc['description'] ?: 'Mã ưu đãi') ?></small>
                                            <small class="text-muted">Đơn từ: <?= format_price($voc['min_order_value']) ?></small>
                                        </div>
                                        <form action="<?= route_url('cart-apply-voucher') ?>" method="POST" class="ms-2">
                                            <input type="hidden" name="voucher_code" value="<?= htmlspecialchars($voc['code']) ?>">
                                            <input type="hidden" name="redirect_to" value="<?= route_url('cart') ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3" <?= (!empty($appliedVoucher) && $appliedVoucher['code'] === $voc['code']) ? 'disabled' : '' ?>>
                                                <?= (!empty($appliedVoucher) && $appliedVoucher['code'] === $voc['code']) ? 'Đang dùng' : 'Áp dụng' ?>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Cột Phải: Áp Dụng Voucher & Tóm Tắt Đơn Hàng -->
            <div class="col-lg-4">
                <div class="bg-white p-4 rounded-4 border shadow-sm sticky-top" style="top: 90px;">
                    <!-- Form Nhập Voucher -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase">Mã Ưu Đãi / Voucher</label>
                        <?php if (!empty($appliedVoucher)): ?>
                            <div class="alert alert-success d-flex justify-content-between align-items-center py-2 px-3 mb-0">
                                <div>
                                    <i class="fa-solid fa-circle-check me-1"></i>
                                    <strong><?= htmlspecialchars($appliedVoucher['code']) ?></strong>
                                    <small class="d-block text-success-emphasis">Đã giảm <?= format_price($discountAmount) ?></small>
                                </div>
                                <a href="<?= route_url('cart-remove-voucher', ['redirect_to' => route_url('cart')]) ?>" class="btn btn-sm btn-outline-danger py-1 px-2 rounded-circle" title="Gỡ mã">
                                    <i class="fa-solid fa-xmark"></i>
                                </a>
                            </div>
                        <?php else: ?>
                            <form action="<?= route_url('cart-apply-voucher') ?>" method="POST" class="d-flex gap-2">
                                <input type="hidden" name="redirect_to" value="<?= route_url('cart') ?>">
                                <input type="text" name="voucher_code" class="form-control text-uppercase" placeholder="Nhập mã voucher...">
                                <button type="submit" class="btn btn-dark rounded-pill px-3">Áp Dụng</button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <h5 class="fw-bold mb-3 border-bottom pb-2">Tóm Tắt Đơn Hàng</h5>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Tạm tính (<?= $totalQuantity ?> đôi):</span>
                        <span class="fw-semibold"><?= format_price($subtotal) ?></span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Giảm giá voucher:</span>
                        <span class="fw-semibold text-danger">
                            <?= ($discountAmount > 0) ? '- ' . format_price($discountAmount) : '0 ₫' ?>
                        </span>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-secondary">Phí vận chuyển:</span>
                        <span class="fw-semibold <?= ($shippingFee == 0) ? 'text-success' : '' ?>">
                            <?= ($shippingFee == 0) ? 'Miễn phí' : format_price($shippingFee) ?>
                        </span>
                    </div>

                    <?php if ($subtotal < 2000000): ?>
                        <div class="alert alert-warning small py-2 mb-3">
                            <i class="fa-solid fa-circle-exclamation me-1"></i> Mua thêm <strong><?= format_price(2000000 - $subtotal) ?></strong> để nhận <strong>Freeship</strong>!
                        </div>
                    <?php endif; ?>

                    <hr>

                    <div class="d-flex justify-content-between mb-4">
                        <span class="fw-bold fs-5">Tổng thanh toán:</span>
                        <span class="fw-bold fs-4 text-danger"><?= format_price($finalTotal) ?></span>
                    </div>

                    <a href="<?= route_url('checkout') ?>" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow-sm">
                        Tiến Hành Thanh Toán <i class="fa-solid fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-5 bg-white rounded-4 border shadow-sm">
            <div class="mb-3 text-secondary" style="font-size: 4rem;">
                <i class="fa-solid fa-cart-arrow-down"></i>
            </div>
            <h4 class="fw-bold">Giỏ hàng của bạn đang trống!</h4>
            <p class="text-secondary small mb-4">Hãy chọn cho mình những đôi sneaker ưng ý nhất tại cửa hàng của chúng tôi.</p>
            <a href="<?= route_url('products') ?>" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold">
                Tiếp Tục Mua Sắm
            </a>
        </div>
    <?php endif; ?>
</div>
