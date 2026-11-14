<div class="container my-5">
    <h3 class="fw-bold mb-4"><i class="fa-solid fa-credit-card me-2 text-danger"></i> Thanh Toán Đơn Hàng</h3>

    <form action="<?= route_url('checkout-process') ?>" method="POST">
        <div class="row g-4">
            <!-- Cột Trái: Thông Tin Nhận Hàng & Thanh Toán -->
            <div class="col-lg-7">
                <div class="bg-white p-4 rounded-4 border shadow-sm mb-4">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">1. Thông Tin Người Nhận Hàng</h5>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Họ và Tên <span class="text-danger">*</span></label>
                        <input type="text" name="recipient_name" class="form-control" placeholder="Ví dụ: Nguyễn Văn A" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Số Điện Thoại <span class="text-danger">*</span></label>
                            <input type="tel" name="recipient_phone" class="form-control" placeholder="Ví dụ: 0912345678" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Nhận Hóa Đơn</label>
                            <input type="email" class="form-control" placeholder="name@example.com" value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Địa Chỉ Giao Hàng Chi Tiết <span class="text-danger">*</span></label>
                        <textarea name="shipping_address" class="form-control" rows="2" placeholder="Số nhà, ngõ/ngách, tên đường, phường/xã, quận/huyện, tỉnh/thành phố..." required></textarea>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold">Ghi Chú Đơn Hàng (Nếu có)</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="Ví dụ: Giao giờ hành chính, gọi trước khi đến 15 phút..."></textarea>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-4 border shadow-sm">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">2. Phương Thức Thanh Toán</h5>

                    <div class="form-check p-3 border rounded-3 mb-3 bg-light">
                        <input class="form-check-input ms-0 me-3" type="radio" name="payment_method" id="pay_cod" value="cod" checked>
                        <label class="form-check-label fw-bold d-block" for="pay_cod">
                            <i class="fa-solid fa-money-bill-wave text-success me-2"></i> Thanh toán khi nhận hàng (COD)
                            <small class="d-block text-secondary fw-normal">Bạn chỉ cần thanh toán tiền mặt cho shipper khi nhận và kiểm tra giày.</small>
                        </label>
                    </div>

                    <div class="form-check p-3 border rounded-3 bg-light">
                        <input class="form-check-input ms-0 me-3" type="radio" name="payment_method" id="pay_banking" value="banking">
                        <label class="form-check-label fw-bold d-block" for="pay_banking">
                            <i class="fa-solid fa-building-columns text-primary me-2"></i> Chuyển khoản ngân hàng (Quét mã QR VietQR)
                            <small class="d-block text-secondary fw-normal">Chuyển khoản nhanh 24/7 qua Internet Banking của tất cả ngân hàng.</small>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Cột Phải: Tóm Tắt Sản Phẩm Đã Chọn -->
            <div class="col-lg-5">
                <div class="bg-white p-4 rounded-4 border shadow-sm sticky-top" style="top: 90px;">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Đơn Hàng (<?= count($items) ?> sản phẩm)</h5>

                    <div class="order-items-list mb-3" style="max-height: 320px; overflow-y: auto;">
                        <?php foreach ($items as $item): ?>
                            <div class="d-flex align-items-center gap-3 py-2 border-bottom">
                                <img src="<?= htmlspecialchars($item['image_url'] ?: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600') ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" class="rounded-3 border" style="width: 55px; height: 55px; object-fit: cover;">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 fw-bold small text-truncate" style="max-width: 220px;"><?= htmlspecialchars($item['product_name']) ?></h6>
                                    <span class="small text-secondary">Size <?= htmlspecialchars($item['size']) ?> | SL: <?= $item['quantity'] ?></span>
                                </div>
                                <span class="fw-bold small text-danger"><?= format_price($item['price'] * $item['quantity']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Tạm tính:</span>
                        <span class="fw-semibold"><?= format_price($subtotal) ?></span>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-secondary">Phí giao hàng:</span>
                        <span class="fw-semibold text-success">
                            <?= ($shippingFee == 0) ? 'Miễn phí' : format_price($shippingFee) ?>
                        </span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-4">
                        <span class="fw-bold fs-5">Tổng tiền phải trả:</span>
                        <span class="fw-black fs-4 text-danger"><?= format_price($totalAmount) ?></span>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold">
                        <i class="fa-solid fa-lock me-2"></i> Xác Nhận Đặt Hàng
                    </button>

                    <p class="text-center small text-secondary mt-3 mb-0">
                        <i class="fa-solid fa-shield-check text-success me-1"></i> Thông tin đặt hàng của bạn được bảo mật tuyệt đối
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>
