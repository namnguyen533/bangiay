<div class="container my-5" style="max-width: 750px;">
    <div class="bg-white p-5 rounded-4 border shadow-sm text-center">
        <div class="mb-4">
            <i class="fa-solid fa-circle-check text-success display-1"></i>
        </div>

        <h2 class="fw-bold text-dark mb-2">Đặt Hàng Thành Công!</h2>
        <p class="text-secondary lead mb-4">
            Cảm ơn bạn đã tin tưởng mua sắm tại <strong>SoleVibe Store</strong>. Chúng tôi đã nhận được đơn và sẽ tiến hành đóng gói giao hàng sớm nhất.
        </p>

        <div class="alert alert-light border text-start p-4 rounded-3 mb-4">
            <div class="d-flex justify-content-between mb-2">
                <span class="text-secondary">Mã đơn hàng:</span>
                <strong class="text-danger fs-5"><?= htmlspecialchars($order['order_code']) ?></strong>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-secondary">Người nhận:</span>
                <strong class="text-dark"><?= htmlspecialchars($order['recipient_name']) ?> (<?= htmlspecialchars($order['recipient_phone']) ?>)</strong>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-secondary">Địa chỉ giao:</span>
                <span class="text-end text-dark fw-semibold" style="max-width: 400px;"><?= htmlspecialchars($order['shipping_address']) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-secondary">Phương thức thanh toán:</span>
                <span class="badge bg-primary px-3 py-1"><?= strtoupper($order['payment_method']) ?></span>
            </div>
            <div class="d-flex justify-content-between pt-2 border-top">
                <span class="fw-bold">Tổng thanh toán:</span>
                <strong class="text-danger fs-4"><?= format_price($order['total_amount']) ?></strong>
            </div>
        </div>

        <!-- Chi Tiết Sản Phẩm Đã Mua -->
        <?php if (!empty($order['items'])): ?>
            <div class="text-start mb-4">
                <h6 class="fw-bold mb-3">Sản phẩm trong đơn:</h6>
                <div class="list-group">
                    <?php foreach ($order['items'] as $item): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0 fw-bold"><?= htmlspecialchars($item['product_name']) ?></h6>
                                <small class="text-secondary">Size <?= htmlspecialchars($item['size']) ?> | Màu: <?= htmlspecialchars($item['color']) ?> | SL: <?= $item['quantity'] ?></small>
                            </div>
                            <span class="fw-bold text-danger"><?= format_price($item['line_total']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-center gap-3">
            <a href="<?= route_url() ?>" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold">
                <i class="fa-solid fa-house me-2"></i> Về Trang Chủ
            </a>
            <a href="<?= route_url('products') ?>" class="btn btn-outline-dark rounded-pill px-4 py-2 fw-semibold">
                <i class="fa-solid fa-shoe-prints me-2"></i> Tiếp Tục Mua Sắm
            </a>
        </div>
    </div>
</div>
