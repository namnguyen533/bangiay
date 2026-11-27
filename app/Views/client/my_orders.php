<?php
/**
 * @var array $orders
 */
if (!function_exists('route_url')) {
    require_once dirname(__DIR__, 2) . '/Core/Helper.php';
}
$orders = !empty($orders) && is_array($orders) ? $orders : [];
?>
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="fa-solid fa-clock-rotate-left me-2 text-danger"></i> Lịch Sử Đơn Hàng</h3>
            <p class="text-secondary small mb-0">Theo dõi tiến trình và danh sách những đơn hàng bạn đã mua</p>
        </div>
        <a href="<?= route_url('products') ?>" class="btn btn-outline-dark rounded-pill">
            <i class="fa-solid fa-shoe-prints me-1"></i> Mua Thêm Giày
        </a>
    </div>

    <?php if (!empty($orders)): ?>
        <div class="bg-white p-4 rounded-4 border shadow-sm">
            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Mã Đơn</th>
                            <th>Ngày Đặt</th>
                            <th>Số Lượng</th>
                            <th>Tổng Tiền</th>
                            <th>Thanh Toán</th>
                            <th>Trạng Thái Đơn</th>
                            <th class="text-end">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $ord): ?>
                            <tr>
                                <td class="fw-bold text-danger">
                                    <a href="<?= route_url('order-detail', ['id' => $ord['id']]) ?>" class="text-danger text-decoration-none">
                                        <?= htmlspecialchars($ord['order_code'] ?? '') ?>
                                    </a>
                                </td>
                                <td class="small text-secondary"><?= !empty($ord['created_at']) ? date('d/m/Y H:i', strtotime($ord['created_at'])) : '' ?></td>
                                <td><?= (int)($ord['total_quantity'] ?? 1) ?> đôi</td>
                                <td class="fw-bold text-dark"><?= format_price($ord['total_amount'] ?? 0) ?></td>
                                <td>
                                    <?php if (($ord['payment_status'] ?? '') === 'paid'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Đã thanh toán</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-dark border border-warning-subtle">Chưa thanh toán</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?= order_status_badge($ord['order_status'] ?? 'pending') ?>">
                                        <?= order_status_label($ord['order_status'] ?? 'pending') ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="<?= route_url('order-detail', ['id' => $ord['id']]) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        <i class="fa-solid fa-eye me-1"></i> Chi Tiết
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-5 bg-white rounded-4 border shadow-sm">
            <div class="mb-3 text-secondary" style="font-size: 4rem;">
                <i class="fa-solid fa-box-open"></i>
            </div>
            <h4 class="fw-bold">Bạn chưa có đơn hàng nào!</h4>
            <p class="text-secondary small mb-4">Hãy bắt đầu lựa chọn những đôi giày thể thao năng động và chất lượng tại Apple Meo Meo.</p>
            <a href="<?= route_url('products') ?>" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold">
                Bắt Đầu Mua Sắm
            </a>
        </div>
    <?php endif; ?>
</div>
