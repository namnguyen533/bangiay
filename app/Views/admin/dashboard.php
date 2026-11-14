<div class="container-fluid">
    <!-- Thống kê nhanh 4 Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card card-stat bg-white p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary small fw-bold text-uppercase">Tổng Doanh Thu</span>
                        <h3 class="fw-black text-danger mt-1 mb-0"><?= format_price($stats['revenue']) ?></h3>
                    </div>
                    <div class="bg-danger-subtle p-3 rounded-circle text-danger fs-3">
                        <i class="fa-solid fa-coins"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card card-stat bg-white p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary small fw-bold text-uppercase">Tổng Đơn Hàng</span>
                        <h3 class="fw-black text-dark mt-1 mb-0"><?= $stats['total_orders'] ?></h3>
                    </div>
                    <div class="bg-primary-subtle p-3 rounded-circle text-primary fs-3">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card card-stat bg-white p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary small fw-bold text-uppercase">Mẫu Giày Đang Bán</span>
                        <h3 class="fw-black text-dark mt-1 mb-0"><?= $stats['total_products'] ?></h3>
                    </div>
                    <div class="bg-success-subtle p-3 rounded-circle text-success fs-3">
                        <i class="fa-solid fa-shoe-prints"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card card-stat bg-white p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary small fw-bold text-uppercase">Thành Viên</span>
                        <h3 class="fw-black text-dark mt-1 mb-0"><?= $stats['total_users'] ?></h3>
                    </div>
                    <div class="bg-warning-subtle p-3 rounded-circle text-warning fs-3">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hàng nút thao tác nhanh -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="bg-white p-4 rounded-4 border shadow-sm d-flex flex-wrap gap-3 align-items-center justify-content-between">
                <div>
                    <h5 class="fw-bold mb-1">Thao Tác Nhanh</h5>
                    <p class="text-secondary small mb-0">Quản lý kho hàng và đơn hàng giày nhanh chóng</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= route_url('admin-product-create') ?>" class="btn btn-primary rounded-pill px-3">
                        <i class="fa-solid fa-plus me-1"></i> Thêm Giày Mới
                    </a>
                    <a href="<?= route_url('admin-orders') ?>" class="btn btn-outline-dark rounded-pill px-3">
                        <i class="fa-solid fa-list-check me-1"></i> Xử Lý Đơn Hàng
                    </a>
                    <a href="<?= route_url('admin-categories') ?>" class="btn btn-outline-secondary rounded-pill px-3">
                        <i class="fa-solid fa-tags me-1"></i> Quản Lý Danh Mục
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bảng Đơn Hàng Mới Nhất -->
    <div class="bg-white p-4 rounded-4 border shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Đơn Hàng Mới Nhất</h5>
            <a href="<?= route_url('admin-orders') ?>" class="btn btn-sm btn-outline-primary rounded-pill">Xem tất cả</a>
        </div>

        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Mã Đơn</th>
                        <th>Khách Hàng</th>
                        <th>Số Điện Thoại</th>
                        <th>Tổng Tiền</th>
                        <th>Thanh Toán</th>
                        <th>Trạng Thái</th>
                        <th>Chi Tiết</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($stats['recent_orders'])): ?>
                        <?php foreach ($stats['recent_orders'] as $ord): ?>
                            <tr>
                                <td class="fw-bold text-danger"><?= htmlspecialchars($ord['order_code']) ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($ord['recipient_name']) ?></td>
                                <td><?= htmlspecialchars($ord['recipient_phone']) ?></td>
                                <td class="fw-bold"><?= format_price($ord['total_amount']) ?></td>
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
                                <td>
                                    <a href="<?= route_url('admin-order-detail', ['id' => $ord['id']]) ?>" class="btn btn-sm btn-outline-dark">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center py-4 text-secondary">Chưa có đơn hàng nào</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
