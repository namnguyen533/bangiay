<div class="container my-5" style="max-width: 980px;">
    <!-- Nút quay lại & Tiêu đề -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">
                Đơn Hàng <span class="text-danger">#<?= htmlspecialchars($order['order_code']) ?></span>
            </h3>
            <p class="text-secondary small mb-0">Thời gian đặt: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= route_url('my-orders') ?>" class="btn btn-outline-secondary rounded-pill">
                <i class="fa-solid fa-arrow-left me-1"></i> Lịch Sử Đơn
            </a>
            <a href="<?= route_url('track-order', ['order_code' => $order['order_code'], 'phone' => $order['recipient_phone']]) ?>" class="btn btn-outline-primary rounded-pill">
                <i class="fa-solid fa-truck-fast me-1"></i> Tra Cứu Đơn
            </a>
        </div>
    </div>

    <!-- Timeline Tiến Trình Đơn Hàng (Theo Dõi Đơn) -->
    <div class="bg-white p-4 rounded-4 border shadow-sm mb-4">
        <h5 class="fw-bold mb-4"><i class="fa-solid fa-route text-danger me-2"></i> Tiến Trình Đơn Hàng</h5>

        <?php if ($order['order_status'] === 'cancelled'): ?>
            <div class="alert alert-danger d-flex align-items-center py-3">
                <i class="fa-solid fa-ban fs-2 me-3"></i>
                <div>
                    <h6 class="fw-bold mb-1">Đơn hàng này đã bị hủy</h6>
                    <p class="small mb-0">Số lượng giày đã được hoàn trả lại tồn kho cửa hàng. <?= htmlspecialchars($order['note'] ?? '') ?></p>
                </div>
            </div>
        <?php else: ?>
            <?php
            $steps = [
                'pending'    => ['label' => 'Chờ xác nhận', 'icon' => 'fa-clipboard-list'],
                'confirmed'  => ['label' => 'Đã xác nhận', 'icon' => 'fa-check-double'],
                'processing' => ['label' => 'Đang đóng gói', 'icon' => 'fa-box-open'],
                'shipping'   => ['label' => 'Đang giao hàng', 'icon' => 'fa-truck-fast'],
                'completed'  => ['label' => 'Giao thành công', 'icon' => 'fa-circle-check'],
            ];

            $orderStatusOrder = ['pending', 'confirmed', 'processing', 'shipping', 'completed'];
            $currentIndex = array_search($order['order_status'], $orderStatusOrder);
            if ($currentIndex === false) $currentIndex = 0;
            ?>
            <div class="position-relative m-4">
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar bg-danger" role="progressbar" style="width: <?= ($currentIndex / (count($orderStatusOrder) - 1)) * 100 ?>%;"></div>
                </div>
                <div class="d-flex justify-content-between position-absolute top-0 start-0 w-100 translate-middle-y">
                    <?php foreach ($orderStatusOrder as $idx => $stKey): ?>
                        <?php 
                        $stepInfo = $steps[$stKey];
                        $isPassed = $idx <= $currentIndex;
                        $isCurrent = $idx === $currentIndex;
                        ?>
                        <div class="text-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 shadow-sm <?= $isPassed ? 'bg-danger text-white' : 'bg-white text-secondary border' ?>" style="width: 38px; height: 38px;">
                                <i class="fa-solid <?= $stepInfo['icon'] ?>"></i>
                            </div>
                            <span class="small fw-semibold d-block <?= $isCurrent ? 'text-danger fw-bold' : ($isPassed ? 'text-dark' : 'text-muted') ?>">
                                <?= $stepInfo['label'] ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div style="height: 35px;"></div>
        <?php endif; ?>
    </div>

    <div class="row g-4">
        <!-- Thông Tin Nhận Hàng & Thanh Toán -->
        <div class="col-lg-5">
            <div class="bg-white p-4 rounded-4 border shadow-sm mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Thông Tin Nhận Hàng</h5>
                <p class="mb-2"><strong>Người nhận:</strong> <?= htmlspecialchars($order['recipient_name']) ?></p>
                <p class="mb-2"><strong>Số điện thoại:</strong> <?= htmlspecialchars($order['recipient_phone']) ?></p>
                <p class="mb-2"><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['shipping_address']) ?></p>
                <?php if (!empty($order['note'])): ?>
                    <p class="mb-2"><strong>Ghi chú:</strong> <em class="text-secondary"><?= htmlspecialchars($order['note']) ?></em></p>
                <?php endif; ?>
                <hr>
                <p class="mb-2"><strong>Phương thức TT:</strong> <?= payment_method_label($order['payment_method']) ?></p>
                <p class="mb-0">
                    <strong>Trạng thái TT:</strong>
                    <?php if ($order['payment_status'] === 'paid'): ?>
                        <span class="badge bg-success">Đã thanh toán</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark">Chưa thanh toán</span>
                        <?php if (in_array($order['payment_method'], ['banking', 'vnpay', 'momo']) && $order['order_status'] !== 'cancelled'): ?>
                            <a href="<?= route_url('payment-gateway', ['id' => $order['id']]) ?>" class="btn btn-sm btn-outline-danger ms-2 py-0 px-2 rounded-pill">Thanh toán ngay</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </p>
            </div>

            <!-- Nút Hủy Đơn Hàng Cho Khách (Khi Đơn Chưa Vận Chuyển) -->
            <?php if (in_array($order['order_status'], ['pending', 'confirmed'])): ?>
                <div class="bg-white p-4 rounded-4 border shadow-sm">
                    <h6 class="fw-bold text-danger mb-2"><i class="fa-solid fa-triangle-exclamation me-1"></i> Hủy Đơn Hàng</h6>
                    <p class="small text-secondary mb-3">Bạn có thể tự hủy đơn hàng khi đơn chưa được chuyển cho đơn vị vận chuyển. Toàn bộ sản phẩm sẽ được tự động hoàn lại số lượng vào kho.</p>
                    
                    <form action="<?= route_url('order-cancel') ?>" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?');">
                        <input type="hidden" name="id" value="<?= $order['id'] ?>">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Lý do hủy đơn:</label>
                            <input type="text" name="reason" class="form-control form-control-sm" placeholder="Ví dụ: Đổi ý, muốn đổi size khác..." required>
                        </div>
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100 rounded-pill fw-semibold">
                            Xác Nhận Hủy Đơn Hàng
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <!-- Chi Tiết Sản Phẩm & Tính Tiền -->
        <div class="col-lg-7">
            <div class="bg-white p-4 rounded-4 border shadow-sm">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Sản Phẩm Trong Đơn</h5>

                <div class="table-responsive mb-3">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Sản Phẩm</th>
                                <th>Đơn Giá</th>
                                <th class="text-center">SL</th>
                                <th class="text-end">Thành Tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order['items'] as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="<?= htmlspecialchars($item['image_url'] ?: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600') ?>" alt="Shoe" class="rounded-2 border" style="width: 45px; height: 45px; object-fit: cover;">
                                            <div>
                                                <div class="fw-bold small text-dark"><?= htmlspecialchars($item['product_name']) ?></div>
                                                <div class="small text-secondary">Size <?= htmlspecialchars($item['size']) ?> | <?= htmlspecialchars($item['color']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="small"><?= format_price($item['unit_price']) ?></td>
                                    <td class="text-center fw-semibold"><?= $item['quantity'] ?></td>
                                    <td class="text-end fw-bold text-danger small"><?= format_price($item['line_total']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Bảng Tính Tiền Đầy Đủ -->
                <div class="border-top pt-3">
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-secondary">Tạm tính:</span>
                        <span><?= format_price($order['subtotal']) ?></span>
                    </div>

                    <?php if (!empty($order['discount_amount']) && $order['discount_amount'] > 0): ?>
                        <div class="d-flex justify-content-between mb-2 small text-danger">
                            <span>Giảm giá voucher <?= !empty($order['voucher_code']) ? '(' . htmlspecialchars($order['voucher_code']) . ')' : '' ?>:</span>
                            <span>- <?= format_price($order['discount_amount']) ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-secondary">Phí vận chuyển:</span>
                        <span><?= ($order['shipping_fee'] == 0) ? 'Miễn phí' : format_price($order['shipping_fee']) ?></span>
                    </div>

                    <div class="d-flex justify-content-between fs-5 fw-bold text-danger pt-2 border-top">
                        <span>Tổng thanh toán:</span>
                        <span><?= format_price($order['total_amount']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
