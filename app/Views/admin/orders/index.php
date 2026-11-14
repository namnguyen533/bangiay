<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Quản Lý Đơn Hàng</h4>
            <p class="text-secondary small mb-0">Theo dõi, xác nhận và cập nhật trạng thái đơn hàng của khách</p>
        </div>
    </div>

    <div class="bg-white p-4 rounded-4 border shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Mã Đơn</th>
                        <th>Khách Hàng</th>
                        <th>Số Điện Thoại</th>
                        <th>Địa Chỉ Giao</th>
                        <th>Tổng Tiền</th>
                        <th>Phương Thức</th>
                        <th>Trạng Thái Đơn</th>
                        <th class="text-end">Xem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $ord): ?>
                            <tr>
                                <td class="fw-bold text-danger"><?= htmlspecialchars($ord['order_code']) ?></td>
                                <td class="fw-semibold text-dark"><?= htmlspecialchars($ord['recipient_name']) ?></td>
                                <td><?= htmlspecialchars($ord['recipient_phone']) ?></td>
                                <td class="small text-secondary text-truncate" style="max-width: 250px;">
                                    <?= htmlspecialchars($ord['shipping_address']) ?>
                                </td>
                                <td class="fw-bold text-dark"><?= format_price($ord['total_amount']) ?></td>
                                <td>
                                    <span class="badge bg-light text-dark border"><?= strtoupper($ord['payment_method']) ?></span>
                                </td>
                                <td>
                                    <?php
                                    $statusBadges = [
                                        'pending'   => 'bg-warning text-dark',
                                        'confirmed' => 'bg-info text-dark',
                                        'shipping'  => 'bg-primary',
                                        'completed' => 'bg-success',
                                        'cancelled' => 'bg-danger',
                                    ];
                                    $badgeClass = $statusBadges[$ord['order_status']] ?? 'bg-secondary';
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= ucfirst($ord['order_status']) ?></span>
                                </td>
                                <td class="text-end">
                                    <a href="<?= route_url('admin-order-detail', ['id' => $ord['id']]) ?>" class="btn btn-sm btn-outline-dark">
                                        <i class="fa-solid fa-eye me-1"></i> Chi Tiết
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center py-4 text-secondary">Chưa có đơn hàng nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
