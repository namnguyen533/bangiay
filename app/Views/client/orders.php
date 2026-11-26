<div class="container my-5" style="max-width: 1000px;">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Đơn hàng của tôi</h2>
            <p class="text-secondary mb-0">Theo dõi trạng thái và yêu cầu hủy đơn hàng của bạn.</p>
        </div>
        <a href="<?= route_url('products') ?>" class="btn btn-outline-dark rounded-pill">
            <i class="fa-solid fa-shoe-prints me-1"></i> Tiếp tục mua sắm
        </a>
    </div>

    <?php if (empty($orders)): ?>
        <div class="bg-white border rounded-4 p-5 text-center">
            <i class="fa-solid fa-box-open fs-1 text-secondary mb-3"></i>
            <h5 class="fw-bold">Bạn chưa có đơn hàng nào</h5>
            <p class="text-secondary">Các đơn hàng sau khi đặt sẽ xuất hiện tại đây.</p>
            <a href="<?= route_url('products') ?>" class="btn btn-primary rounded-pill px-4">Khám phá sản phẩm</a>
        </div>
    <?php else: ?>
        <?php
        $statusLabels = [
            'pending' => 'Chờ xử lý',
            'confirmed' => 'Đã xác nhận',
            'shipping' => 'Đang giao',
            'completed' => 'Hoàn tất',
            'cancelled' => 'Đã hủy',
        ];
        $statusClasses = [
            'pending' => 'bg-warning text-dark',
            'confirmed' => 'bg-info text-dark',
            'shipping' => 'bg-primary',
            'completed' => 'bg-success',
            'cancelled' => 'bg-danger',
        ];
        ?>
        <div class="d-grid gap-3">
            <?php foreach ($orders as $order): ?>
                <?php
                $status = $order['order_status'];
                $canCancel = in_array($status, ['pending', 'confirmed'], true);
                ?>
                <article class="bg-white border rounded-4 p-4 shadow-sm">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 border-bottom pb-3 mb-3">
                        <div>
                            <div class="fw-bold fs-5"><?= htmlspecialchars($order['order_code']) ?></div>
                            <div class="small text-secondary">Đặt lúc <?= htmlspecialchars(date('d/m/Y H:i', strtotime($order['created_at']))) ?></div>
                        </div>
                        <span class="badge <?= $statusClasses[$status] ?? 'bg-secondary' ?>">
                            <?= htmlspecialchars($statusLabels[$status] ?? $status) ?>
                        </span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="small text-secondary">Người nhận</div>
                            <div class="fw-semibold"><?= htmlspecialchars($order['recipient_name']) ?></div>
                            <div class="small text-secondary mt-2">Địa chỉ giao hàng</div>
                            <div><?= htmlspecialchars($order['shipping_address']) ?></div>
                            <div class="small text-secondary mt-2">
                                Thanh toán: <?= htmlspecialchars(strtoupper($order['payment_method'])) ?>
                                (<?= htmlspecialchars($order['payment_status']) ?>)
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="small text-secondary">Tổng thanh toán</div>
                            <div class="fs-4 fw-bold text-danger"><?= format_price($order['total_amount']) ?></div>
                        </div>
                    </div>

                    <?php if ($status === 'cancelled' && !empty($order['cancellation_reason'])): ?>
                        <div class="alert alert-light border mt-3 mb-0">
                            <strong>Lý do hủy:</strong> <?= nl2br(htmlspecialchars($order['cancellation_reason'])) ?>
                        </div>
                    <?php elseif ($canCancel): ?>
                        <form action="<?= route_url('order-cancel') ?>" method="POST" class="mt-4 pt-3 border-top">
                            <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
                            <label for="cancelReason<?= (int)$order['id'] ?>" class="form-label fw-semibold">
                                Nếu muốn hủy, vui lòng cho biết lý do
                            </label>
                            <textarea
                                id="cancelReason<?= (int)$order['id'] ?>"
                                name="reason"
                                class="form-control mb-3"
                                rows="2"
                                maxlength="500"
                                required
                                placeholder="Nhập lý do hủy đơn (tối đa 500 ký tự)"
                            ></textarea>
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="fa-solid fa-ban me-1"></i> Xác nhận hủy đơn
                            </button>
                        </form>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
