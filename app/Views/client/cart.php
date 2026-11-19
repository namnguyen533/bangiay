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
                                    <th scope="col" style="min-width: 250px;">Sản Phẩm Giày</th>
                                    <th scope="col">Đơn Giá</th>
                                    <th scope="col" style="width: 140px;">Số Lượng</th>
                                    <th scope="col">Thành Tiền</th>
                                    <th scope="col" class="text-end">Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="<?= htmlspecialchars($item['image_url'] ?: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600') ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" class="rounded-3 border" style="width: 65px; height: 65px; object-fit: cover;">
                                                <div>
                                                    <h6 class="mb-1 fw-bold">
                                                        <a href="<?= route_url('product-detail', ['id' => $item['product_id']]) ?>" class="text-dark text-decoration-none">
                                                            <?= htmlspecialchars($item['product_name']) ?>
                                                        </a>
                                                    </h6>
                                                    <div class="small text-secondary">
                                                        Size: <span class="badge bg-light text-dark border"><?= htmlspecialchars($item['size']) ?></span>
                                                        | Màu: <?= htmlspecialchars($item['color']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="fw-semibold"><?= format_price($item['price']) ?></td>
                                        <td>
                                            <form action="<?= route_url('cart-update') ?>" method="POST" class="d-flex align-items-center gap-1">
                                                <input type="hidden" name="variant_id" value="<?= $item['variant_id'] ?>">
                                                <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" max="10" class="form-control form-control-sm text-center" style="width: 65px;" onchange="this.form.submit()">
                                            </form>
                                        </td>
                                        <td class="fw-bold text-danger"><?= format_price($item['price'] * $item['quantity']) ?></td>
                                        <td class="text-end">
                                            <a href="<?= route_url('cart-remove', ['variant_id' => $item['variant_id']]) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc chắn muốn bỏ sản phẩm này?');">
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
                    </div>
                </div>
            </div>

            <!-- Tóm Tắt Đơn Hàng -->
            <div class="col-lg-4">
                <div class="bg-white p-4 rounded-4 border shadow-sm sticky-top" style="top: 90px;">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Tóm Tắt Đơn Hàng</h5>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Tạm tính (<?= $totalQuantity ?> đôi):</span>
                        <span class="fw-semibold"><?= format_price($totalAmount) ?></span>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-secondary">Phí vận chuyển:</span>
                        <span class="fw-semibold text-success">
                            <?= ($totalAmount >= 2000000) ? 'Miễn phí' : '30.000 ₫' ?>
                        </span>
                    </div>

                    <?php if ($totalAmount < 2000000): ?>
                        <div class="alert alert-warning small py-2 mb-3">
                            <i class="fa-solid fa-circle-exclamation me-1"></i> Mua thêm <strong><?= format_price(2000000 - $totalAmount) ?></strong> để nhận <strong>Freeship</strong>!
                        </div>
                    <?php endif; ?>

                    <hr>

                    <div class="d-flex justify-content-between mb-4">
                        <span class="fw-bold fs-5">Tổng thanh toán:</span>
                        <span class="fw-bold fs-4 text-danger">
                            <?= format_price($totalAmount + ($totalAmount >= 2000000 ? 0 : 30000)) ?>
                        </span>
                    </div>

                    <?php if (auth()): ?>
                        <a href="<?= route_url('checkout') ?>" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold">
                            Tiến Hành Thanh Toán <i class="fa-solid fa-arrow-right ms-2"></i>
                        </a>
                    <?php else: ?>
                        <a href="<?= route_url('login', ['return' => 'checkout']) ?>" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold">
                            <i class="fa-solid fa-user me-2"></i> Đăng Nhập Để Đặt Hàng
                        </a>
                        <p class="text-center small text-secondary mt-2 mb-0">
                            Bạn vẫn có thể thêm giày vào giỏ hàng khi chưa đăng nhập.
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-5 bg-white rounded-4 border shadow-sm">
            <i class="fa-solid fa-cart-shopping fs-1 text-secondary mb-3"></i>
            <h4 class="fw-bold">Giỏ hàng của bạn đang trống</h4>
            <p class="text-secondary">Hãy lựa chọn những đôi giày ưng ý để thêm vào giỏ hàng ngay!</p>
            <a href="<?= route_url('products') ?>" class="btn btn-primary rounded-pill px-4 mt-2">
                <i class="fa-solid fa-bag-shopping me-2"></i> Khám Phá Giày Ngay
            </a>
        </div>
    <?php endif; ?>
</div>
