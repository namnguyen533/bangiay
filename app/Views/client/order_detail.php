<?php
/**
 * @var array $order
 */
if (!function_exists('route_url')) {
    require_once dirname(__DIR__, 2) . '/Core/Helper.php';
}

if (empty($order)) {
    echo '<div class="container my-5 text-center"><div class="alert alert-warning py-4 rounded-4 shadow-sm"><h5><i class="fa-solid fa-circle-exclamation text-warning me-2"></i>Không tìm thấy thông tin đơn hàng</h5><p class="text-secondary small mb-3">Vui lòng đăng nhập hoặc kiểm tra lại đường dẫn đơn hàng của bạn.</p><a href="' . route_url('my-orders') . '" class="btn btn-primary rounded-pill px-4">Xem Đơn Hàng Của Tôi</a></div></div>';
    return;
}

$orderCode = htmlspecialchars($order['order_code'] ?? '');
$orderDate = !empty($order['created_at']) ? date('d/m/Y H:i', strtotime($order['created_at'])) : date('d/m/Y H:i');
$orderStatus = $order['order_status'] ?? 'pending';
$paymentMethod = $order['payment_method'] ?? 'cod';
$paymentStatus = $order['payment_status'] ?? 'unpaid';
$recipientName = htmlspecialchars($order['recipient_name'] ?? '');
$recipientPhone = htmlspecialchars($order['recipient_phone'] ?? '');
$shippingAddress = htmlspecialchars($order['shipping_address'] ?? '');
$orderNote = htmlspecialchars($order['note'] ?? '');
$cancellationReason = htmlspecialchars($order['cancellation_reason'] ?? '');
$subtotal = (float)($order['subtotal'] ?? 0);
$discountAmount = (float)($order['discount_amount'] ?? 0);
$shippingFee = (float)($order['shipping_fee'] ?? 0);
$totalAmount = (float)($order['total_amount'] ?? 0);
$voucherCode = htmlspecialchars($order['voucher_code'] ?? '');
$orderItems = !empty($order['items']) && is_array($order['items']) ? $order['items'] : [];
$orderId = (int)($order['id'] ?? 0);
?>
<div class="container my-5" style="max-width: 980px;">
    <!-- Nút quay lại & Tiêu đề -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">
                Đơn Hàng <span class="text-danger">#<?= $orderCode ?></span>
            </h3>
            <p class="text-secondary small mb-0">Thời gian đặt: <?= $orderDate ?></p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= route_url('my-orders') ?>" class="btn btn-outline-secondary rounded-pill">
                <i class="fa-solid fa-arrow-left me-1"></i> Lịch Sử Đơn
            </a>
            <a href="<?= route_url('track-order', ['order_code' => $orderCode, 'phone' => $recipientPhone]) ?>" class="btn btn-outline-primary rounded-pill">
                <i class="fa-solid fa-truck-fast me-1"></i> Tra Cứu Đơn
            </a>
        </div>
    </div>

    <!-- Timeline Tiến Trình Đơn Hàng (Theo Dõi Đơn) -->
    <div class="bg-white p-4 rounded-4 border shadow-sm mb-4">
        <h5 class="fw-bold mb-4"><i class="fa-solid fa-route text-danger me-2"></i> Tiến Trình Đơn Hàng</h5>

        <?php if ($orderStatus === 'cancelled'): ?>
            <div class="alert alert-danger d-flex align-items-center py-3">
                <i class="fa-solid fa-ban fs-2 me-3"></i>
                <div>
                    <h6 class="fw-bold mb-1">Đơn hàng này đã bị hủy</h6>
                    <p class="small mb-0">Số lượng giày đã được hoàn trả lại tồn kho cửa hàng.</p>
                    <?php if ($cancellationReason !== ''): ?>
                        <p class="small mb-0 mt-2"><strong>Lý do hủy:</strong> <?= nl2br($cancellationReason) ?></p>
                    <?php endif; ?>

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
            $currentIndex = array_search($orderStatus, $orderStatusOrder);
            if ($currentIndex === false) {
                $currentIndex = 0;
            }
            $progressPercent = ($currentIndex / (count($orderStatusOrder) - 1)) * 100;
            ?>
            <div class="position-relative m-4">
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar bg-danger" role="progressbar" style="width: <?= $progressPercent ?>%;"></div>
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
                <p class="mb-2"><strong>Người nhận:</strong> <?= $recipientName ?></p>
                <p class="mb-2"><strong>Số điện thoại:</strong> <?= $recipientPhone ?></p>
                <p class="mb-2"><strong>Địa chỉ:</strong> <?= $shippingAddress ?></p>
                <?php if (!empty($orderNote)): ?>
                    <p class="mb-2"><strong>Ghi chú:</strong> <em class="text-secondary"><?= $orderNote ?></em></p>
                <?php endif; ?>
                <hr>
                <p class="mb-2"><strong>Phương thức TT:</strong> <?= payment_method_label($paymentMethod) ?></p>
                <p class="mb-0">
                    <strong>Trạng thái TT:</strong>
                    <?php if ($paymentStatus === 'paid'): ?>
                        <span class="badge bg-success">Đã thanh toán</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark">Chưa thanh toán</span>
                        <?php if (in_array($paymentMethod, ['banking', 'vnpay', 'momo']) && $orderStatus !== 'cancelled'): ?>
                            <a href="<?= route_url('payment-gateway', ['id' => $orderId]) ?>" class="btn btn-sm btn-outline-danger ms-2 py-0 px-2 rounded-pill">Thanh toán ngay</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </p>
            </div>

            <!-- Hủy đơn / yêu cầu hủy -->
            <?php if (auth() && in_array($orderStatus, ['pending', 'confirmed'], true)): ?>
                <div id="cancel-order" class="bg-white p-4 rounded-4 border shadow-sm">
                    <h6 class="fw-bold text-danger mb-2"><i class="fa-solid fa-triangle-exclamation me-1"></i> Hủy Đơn Hàng</h6>
                    <p class="small text-secondary mb-3">
                        <?= $orderStatus === 'pending'
                            ? 'Đơn chưa được xác nhận: bạn có thể hủy trực tiếp.'
                            : 'Đơn đã được xác nhận: yêu cầu hủy cần quản trị viên chấp thuận.' ?>
                    </p>
                    
                    <?php if (($order['cancellation_status'] ?? 'none') === 'pending'): ?>
                        <div class="alert alert-warning small mb-3">Yêu cầu hủy đang chờ quản trị viên xác nhận.</div>
                    <?php elseif (($order['cancellation_status'] ?? 'none') === 'rejected'): ?>
                        <div class="alert alert-danger small mb-3">
                            Yêu cầu hủy bị từ chối:
                            <?= nl2br(htmlspecialchars($order['cancellation_rejection_reason'] ?? '')) ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= route_url('order-cancel') ?>" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?');">
                        <input type="hidden" name="id" value="<?= $orderId ?>">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Lý do hủy đơn:</label>
                            <textarea name="reason" class="form-control form-control-sm" rows="3" maxlength="500" placeholder="Ví dụ: Đổi ý, muốn đổi size khác..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100 rounded-pill fw-semibold">
                            Xác Nhận Hủy Đơn Hàng
                        </button>
                    </form>
                </div>
            <?php endif; ?>

            <?php if (auth() && $orderStatus === 'shipping'): ?>
                <div class="bg-white p-4 rounded-4 border shadow-sm mt-4">
                    <h6 class="fw-bold mb-3">Xác Nhận Khi Nhận Hàng</h6>
                    <form action="<?= route_url('order-confirm-received') ?>" method="POST" class="mb-3" onsubmit="return confirm('Xác nhận bạn đã nhận được hàng?');">
                        <input type="hidden" name="id" value="<?= $orderId ?>">
                        <button class="btn btn-success w-100 rounded-pill fw-bold">Xác nhận đã nhận được hàng</button>
                    </form>

                    <h6 class="fw-bold text-warning mb-2">Yêu cầu đổi/trả hàng</h6>
                    <form action="<?= route_url('order-request-return') ?>" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= $orderId ?>">
                        <textarea name="reason" class="form-control form-control-sm mb-2" rows="3" maxlength="500" placeholder="Lý do đổi/trả hàng" required></textarea>
                        <input type="file" name="images[]" class="form-control form-control-sm mb-2" accept="image/jpeg,image/png,image/webp" multiple required>
                        <button class="btn btn-outline-warning w-100 rounded-pill fw-semibold">Gửi yêu cầu đổi/trả</button>
                    </form>
                    <?php if (($order['return_status'] ?? 'none') === 'rejected'): ?>
                        <div class="alert alert-danger small mt-3 mb-0">
                            Yêu cầu đổi/trả bị từ chối: <?= nl2br(htmlspecialchars($order['return_rejection_reason'] ?? '')) ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($orderStatus === 'return_requested'): ?>
                <div class="alert alert-warning mt-4 mb-0">Yêu cầu đổi/trả đang chờ quản trị viên xem xét.</div>
            <?php elseif ($orderStatus === 'returned'): ?>
                <div class="alert alert-secondary mt-4 mb-0">
                    <?= (($order['return_status'] ?? '') === 'received')
                        ? 'Đơn đã được xác nhận trả hàng và hoàn tiền.'
                        : 'Yêu cầu đổi/trả đã được đồng ý. Vui lòng chờ quản trị viên xác nhận đã nhận hàng.' ?>
                </div>
            <?php elseif ($orderStatus === 'completed'): ?>
                <div class="alert alert-success mt-4 mb-0">Đơn hàng đã hoàn tất sau khi bạn xác nhận nhận hàng.</div>
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
                            <?php if (!empty($orderItems)): ?>
                                <?php foreach ($orderItems as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="<?= htmlspecialchars(image_src($item['image_url'] ?: 'uploads/products/no-product-image.svg')) ?>" alt="Shoe" class="rounded-2 border" style="width: 45px; height: 45px; object-fit: cover;">
                                                <div>
                                                    <div class="fw-bold small text-dark"><?= htmlspecialchars($item['product_name'] ?? 'Giày') ?></div>
                                                    <div class="small text-secondary">Size <?= htmlspecialchars($item['size'] ?? '') ?> | <?= htmlspecialchars($item['color'] ?? '') ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="small"><?= format_price($item['unit_price'] ?? 0) ?></td>
                                        <td class="text-center fw-semibold"><?= (int)($item['quantity'] ?? 1) ?></td>
                                        <td class="text-end fw-bold text-danger small"><?= format_price($item['line_total'] ?? 0) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center text-muted py-3">Không có sản phẩm nào.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Bảng Tính Tiền Đầy Đủ -->
                <div class="border-top pt-3">
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-secondary">Tạm tính:</span>
                        <span><?= format_price($subtotal) ?></span>
                    </div>

                    <?php if ($discountAmount > 0): ?>
                        <div class="d-flex justify-content-between mb-2 small text-danger">
                            <span>Giảm giá voucher <?= !empty($voucherCode) ? '(' . $voucherCode . ')' : '' ?>:</span>
                            <span>- <?= format_price($discountAmount) ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-secondary">Phí vận chuyển:</span>
                        <span><?= ($shippingFee == 0) ? 'Miễn phí' : format_price($shippingFee) ?></span>
                    </div>

                    <div class="d-flex justify-content-between fs-5 fw-bold text-danger pt-2 border-top">
                        <span>Tổng thanh toán:</span>
                        <span><?= format_price($totalAmount) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
