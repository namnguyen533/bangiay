<?php
/**
 * @var array $order
 */
if (!function_exists('route_url')) {
    require_once dirname(__DIR__, 2) . '/Core/Helper.php';
}

if (empty($order)) {
    echo '<div class="container my-5 text-center"><div class="alert alert-warning">Không tìm thấy thông tin đơn hàng thanh toán.</div></div>';
    return;
}

$orderId = (int)($order['id'] ?? 0);
$orderCode = htmlspecialchars($order['order_code'] ?? '');
$totalAmount = (float)($order['total_amount'] ?? 0);
$paymentMethod = $order['payment_method'] ?? 'banking';
$recipientName = htmlspecialchars($order['recipient_name'] ?? '');
$recipientPhone = htmlspecialchars($order['recipient_phone'] ?? '');

$bankId = 'MB'; // MB Bank
$accountNo = '0987654321';
$accountName = 'SOLEVIBE SNEAKER STORE';
$vietQrUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-compact2.png?amount=" . (int)$totalAmount . "&addInfo=" . urlencode($orderCode) . "&accountName=" . urlencode($accountName);
?>
<div class="container my-5" style="max-width: 680px;">
    <div class="bg-white p-4 p-md-5 rounded-4 border shadow-sm text-center">
        <!-- Logo / Icon Cổng Thanh Toán -->
        <div class="mb-3">
            <?php if ($paymentMethod === 'momo'): ?>
                <span class="badge rounded-circle p-3 bg-danger-subtle text-danger fs-1" style="background-color: #ffeff5 !important; color: #a50064 !important;">
                    <i class="fa-solid fa-wallet"></i>
                </span>
                <h4 class="fw-bold mt-2">Cổng Thanh Toán Ví MoMo</h4>
            <?php elseif ($paymentMethod === 'vnpay'): ?>
                <span class="badge rounded-circle p-3 bg-primary-subtle text-primary fs-1">
                    <i class="fa-solid fa-credit-card"></i>
                </span>
                <h4 class="fw-bold mt-2">Cổng Thanh Toán VNPay Sandbox</h4>
            <?php else: ?>
                <span class="badge rounded-circle p-3 bg-success-subtle text-success fs-1">
                    <i class="fa-solid fa-qrcode"></i>
                </span>
                <h4 class="fw-bold mt-2">Cổng Thanh Toán VietQR Chuyển Khoản</h4>
            <?php endif; ?>
            <p class="text-secondary small">Vui lòng quét mã QR bên dưới để thanh toán đơn hàng của bạn</p>
        </div>

        <!-- Box Thông Tin Đơn Hàng -->
        <div class="bg-light p-3 rounded-3 mb-4 text-start small">
            <div class="row g-2">
                <div class="col-6"><strong>Mã đơn hàng:</strong> <span class="text-danger fw-bold"><?= $orderCode ?></span></div>
                <div class="col-6 text-end"><strong>Tổng thanh toán:</strong> <span class="text-danger fw-bold fs-6"><?= format_price($totalAmount) ?></span></div>
                <div class="col-6"><strong>Người nhận:</strong> <?= $recipientName ?></div>
                <div class="col-6 text-end"><strong>Số điện thoại:</strong> <?= $recipientPhone ?></div>
            </div>
        </div>

        <!-- QR Code Thanh Toán Động (VietQR chuẩn) -->
        <div class="p-3 border rounded-4 d-inline-block bg-white shadow-sm mb-3">
            <img src="<?= $vietQrUrl ?>" alt="Mã QR Thanh Toán" class="img-fluid" style="max-width: 280px; border-radius: 12px;">
        </div>

        <div class="text-muted small mb-4">
            <div>Ngân hàng thụ hưởng: <strong>MBBank (Ngân hàng Quân Đội)</strong></div>
            <div>Số tài khoản: <strong class="text-dark">0987654321</strong></div>
            <div>Chủ tài khoản: <strong>SOLEVIBE SNEAKER STORE</strong></div>
            <div>Nội dung chuyển khoản: <strong class="text-danger fs-6"><?= $orderCode ?></strong></div>
        </div>

        <!-- Nút Xác Nhận Thanh Toán (Hoặc Giả Lập Callback) -->
        <div class="d-flex flex-column gap-2">
            <form action="<?= route_url('payment-confirm') ?>" method="POST">
                <input type="hidden" name="id" value="<?= $orderId ?>">
                <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill fw-bold shadow-sm">
                    <i class="fa-solid fa-check-circle me-2"></i> Tôi Đã Chuyển Khoản Thành Công
                </button>
            </form>

            <a href="<?= route_url('order-detail', ['id' => $orderId]) ?>" class="btn btn-outline-secondary rounded-pill">
                Để Sau, Xem Chi Tiết Đơn Hàng
            </a>
        </div>
    </div>
</div>
