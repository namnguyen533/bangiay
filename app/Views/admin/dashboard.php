<div class="admin-dashboard">
    <section class="admin-page-heading d-flex justify-content-between align-items-center">
        <div>
            <h1>Tổng quan cửa hàng</h1>
            <p>Theo dõi hoạt động kinh doanh và xử lý công việc hằng ngày.</p>
        </div>
        <a href="<?= route_url('admin-product-create') ?>" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold">
            <i class="fa-solid fa-plus me-2"></i>Thêm sản phẩm
        </a>
    </section>

    <section class="row g-3 g-xl-4 mb-4" aria-label="Thống kê cửa hàng">
        <div class="col-12 col-sm-6 col-xl-3">
            <article class="card-stat">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="admin-stat-label">Doanh thu ghi nhận</div>
                        <div class="admin-stat-value text-danger"><?= format_price($stats['revenue']) ?></div>
                        <div class="admin-stat-note">Đơn đã xác nhận trở lên</div>
                    </div>
                    <span class="admin-stat-icon bg-danger-subtle text-danger"><i class="fa-solid fa-coins"></i></span>
                </div>
            </article>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <article class="card-stat">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="admin-stat-label">Tổng đơn hàng</div>
                        <div class="admin-stat-value"><?= number_format((int)$stats['total_orders']) ?></div>
                        <div class="admin-stat-note">Tất cả đơn đã tạo</div>
                    </div>
                    <span class="admin-stat-icon bg-primary-subtle text-primary"><i class="fa-solid fa-receipt"></i></span>
                </div>
            </article>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <article class="card-stat">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="admin-stat-label">Sản phẩm đang bán</div>
                        <div class="admin-stat-value"><?= number_format((int)$stats['total_products']) ?></div>
                        <div class="admin-stat-note">Mẫu giày đang hoạt động</div>
                    </div>
                    <span class="admin-stat-icon bg-success-subtle text-success"><i class="fa-solid fa-shoe-prints"></i></span>
                </div>
            </article>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <article class="card-stat">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="admin-stat-label">Thành viên</div>
                        <div class="admin-stat-value"><?= number_format((int)$stats['total_users']) ?></div>
                        <div class="admin-stat-note">Tài khoản khách hàng</div>
                    </div>
                    <span class="admin-stat-icon bg-warning-subtle text-warning"><i class="fa-solid fa-users"></i></span>
                </div>
            </article>
        </div>
    </section>

    <section class="admin-panel mb-4">
        <div class="admin-panel-heading">
            <h2><i class="fa-solid fa-bolt text-warning me-2"></i>Truy cập nhanh</h2>
        </div>
        <div class="row g-3 p-3 p-md-4">
            <div class="col-12 col-md-4">
                <a class="admin-quick-link" href="<?= route_url('admin-product-create') ?>">
                    <i class="fa-solid fa-plus"></i>
                    <span>Thêm sản phẩm mới</span>
                    <i class="fa-solid fa-arrow-right ms-auto border-0 bg-transparent"></i>
                </a>
            </div>
            <div class="col-12 col-md-4">
                <a class="admin-quick-link" href="<?= route_url('admin-orders') ?>">
                    <i class="fa-solid fa-list-check"></i>
                    <span>Quản lý đơn hàng</span>
                    <i class="fa-solid fa-arrow-right ms-auto border-0 bg-transparent"></i>
                </a>
            </div>
            <div class="col-12 col-md-4">
                <a class="admin-quick-link" href="<?= route_url('admin-categories') ?>">
                    <i class="fa-solid fa-tags"></i>
                    <span>Quản lý danh mục</span>
                    <i class="fa-solid fa-arrow-right ms-auto border-0 bg-transparent"></i>
                </a>
            </div>
        </div>
    </section>

    <section class="admin-panel">
        <div class="admin-panel-heading d-flex align-items-center justify-content-between gap-3">
            <div>
                <h2>Đơn hàng gần đây</h2>
                <p class="small text-secondary mb-0 mt-1">Các đơn mới nhất trong cửa hàng</p>
            </div>
            <a href="<?= route_url('admin-orders') ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3">Xem tất cả</a>
        </div>
        <div class="table-responsive">
            <table class="table align-middle table-hover admin-orders-table">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Điện thoại</th>
                        <th>Tổng tiền</th>
                        <th>Thanh toán</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($stats['recent_orders'])): ?>
                        <?php
                        $statusStyles = [
                            'pending' => 'background:#fff7e6;color:#a15c00;',
                            'confirmed' => 'background:#e8f4ff;color:#1769aa;',
                            'shipping' => 'background:#eef2ff;color:#4f46a5;',
                            'completed' => 'background:#e9f8ef;color:#207448;',
                            'cancelled' => 'background:#fff0f1;color:#b42332;',
                        ];
                        $statusLabels = [
                            'pending' => 'Chờ xử lý',
                            'confirmed' => 'Đã xác nhận',
                            'shipping' => 'Đang giao',
                            'completed' => 'Hoàn tất',
                            'cancelled' => 'Đã hủy',
                        ];
                        ?>
                        <?php foreach ($stats['recent_orders'] as $ord): ?>
                            <?php $status = $ord['order_status'] ?? ''; ?>
                            <tr>
                                <td><span class="admin-order-code"><?= htmlspecialchars($ord['order_code'] ?? '') ?></span></td>
                                <td class="fw-semibold text-dark"><?= htmlspecialchars($ord['recipient_name'] ?? '') ?></td>
                                <td><?= htmlspecialchars($ord['recipient_phone'] ?? '') ?></td>
                                <td class="fw-bold text-dark"><?= format_price($ord['total_amount'] ?? 0) ?></td>
                                <td><span class="badge rounded-pill bg-light text-dark border"><?= htmlspecialchars(strtoupper($ord['payment_method'] ?? '')) ?></span></td>
                                <td>
                                    <span class="admin-order-status" style="<?= $statusStyles[$status] ?? 'background:#f1f5f9;color:#475569;' ?>">
                                        <?= htmlspecialchars($statusLabels[$status] ?? ($status !== '' ? ucfirst($status) : 'Không rõ')) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="<?= route_url('admin-order-detail', ['id' => (int)($ord['id'] ?? 0)]) ?>" class="btn btn-sm btn-light rounded-circle" aria-label="Xem chi tiết đơn hàng">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="py-5 text-center">
                                <i class="fa-regular fa-clipboard d-block fs-3 text-secondary mb-2"></i>
                                <span class="text-secondary">Chưa có đơn hàng nào.</span>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
