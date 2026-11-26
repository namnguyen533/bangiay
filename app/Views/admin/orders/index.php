<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Quản Lý Đơn Hàng</h4>
            <p class="text-secondary small mb-0">Theo dõi, duyệt đơn, in hóa đơn và chuyển trạng thái giao hàng</p>
        </div>
    </div>

    <!-- Bộ lọc trạng thái & Tìm kiếm -->
    <div class="bg-white p-3 rounded-4 border shadow-sm mb-4">
        <div class="row g-3 align-items-center justify-content-between">
            <div class="col-lg-8">
                <!-- Status Tabs -->
                <?php
                $statusList = [
                    'all'       => 'Tất cả',
                    'pending'   => 'Chờ xác nhận',
                    'confirmed' => 'Đã xác nhận',
                    'shipping'  => 'Đang giao',
                    'completed' => 'Hoàn thành',
                    'cancelled' => 'Đã hủy',
                ];
                ?>
                <ul class="nav nav-pills gap-1">
                    <?php foreach ($statusList as $stKey => $stLabel): ?>
                        <li class="nav-item">
                            <a class="nav-link rounded-pill px-3 py-1 fw-semibold small <?= ($currentStatus === $stKey) ? 'active bg-danger' : 'text-secondary' ?>" 
                               href="<?= route_url('admin-orders', ['status' => $stKey, 'q' => $keyword]) ?>">
                                <?= $stLabel ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="col-lg-4">
                <form action="<?= route_url('admin-orders') ?>" method="GET" class="d-flex">
                    <input type="hidden" name="act" value="admin-orders">
                    <input type="hidden" name="status" value="<?= htmlspecialchars($currentStatus) ?>">
                    <div class="input-group">
                        <input type="search" name="q" class="form-control form-control-sm" placeholder="Tìm mã đơn, tên khách, SĐT..." value="<?= htmlspecialchars($keyword) ?>">
                        <button class="btn btn-sm btn-dark" type="submit">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bảng Đơn Hàng -->
    <div class="bg-white p-4 rounded-4 border shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mã Đơn</th>
                        <th>Ngày Đặt</th>
                        <th>Khách Hàng</th>
                        <th>Số Điện Thoại</th>
                        <th>Tổng Tiền</th>
                        <th>Thanh Toán</th>
                        <th>Trạng Thái Đơn</th>
                        <th class="text-end">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $ord): ?>
                            <tr>
                                <td class="fw-bold text-danger"><?= htmlspecialchars($ord['order_code']) ?></td>
                                <td class="small text-secondary"><?= date('d/m/Y H:i', strtotime($ord['created_at'])) ?></td>
                                <td class="fw-semibold text-dark"><?= htmlspecialchars($ord['recipient_name']) ?></td>
                                <td><?= htmlspecialchars($ord['recipient_phone']) ?></td>
                                <td class="fw-bold text-dark"><?= format_price($ord['total_amount']) ?></td>
                                <td>
                                    <div class="small">
                                        <span class="badge bg-light text-dark border"><?= strtoupper($ord['payment_method']) ?></span>
                                        <?php if ($ord['payment_status'] === 'paid'): ?>
                                            <span class="badge bg-success-subtle text-success ms-1">Đã TT</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning-subtle text-dark ms-1">Chưa TT</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge <?= order_status_badge($ord['order_status']) ?>">
                                        <?= order_status_label($ord['order_status']) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="<?= route_url('admin-order-print', ['id' => $ord['id']]) ?>" target="_blank" class="btn btn-sm btn-outline-secondary" title="In Hóa Đơn">
                                            <i class="fa-solid fa-print"></i>
                                        </a>
                                        <a href="<?= route_url('admin-order-detail', ['id' => $ord['id']]) ?>" class="btn btn-sm btn-outline-dark" title="Chi Tiết & Cập Nhật">
                                            <i class="fa-solid fa-pen-to-square me-1"></i> Xử Lý
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center py-4 text-secondary">Không có đơn hàng nào phù hợp với bộ lọc.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
