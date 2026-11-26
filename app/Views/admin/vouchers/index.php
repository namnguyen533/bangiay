<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Quản Lý Mã Giảm Giá (Voucher)</h4>
            <p class="text-secondary small mb-0">Tạo mới, chỉnh sửa và quản lý các chương trình khuyến mãi theo % hoặc số tiền cố định</p>
        </div>
        <a href="<?= route_url('admin-voucher-create') ?>" class="btn btn-primary rounded-pill px-3">
            <i class="fa-solid fa-plus me-1"></i> Tạo Voucher Mới
        </a>
    </div>

    <div class="bg-white p-4 rounded-4 border shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mã Code</th>
                        <th>Mô Tả Khuyến Mãi</th>
                        <th>Loại Giảm</th>
                        <th>Giá Trị</th>
                        <th>Đơn Tối Thiểu</th>
                        <th>Giảm Tối Đa</th>
                        <th>Đã Dùng / Giới Hạn</th>
                        <th>Hạn Dùng</th>
                        <th>Trạng Thái</th>
                        <th class="text-end">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($vouchers)): ?>
                        <?php foreach ($vouchers as $v): ?>
                            <tr>
                                <td class="fw-bold text-danger">
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle fs-6 px-2 py-1">
                                        <?= htmlspecialchars($v['code']) ?>
                                    </span>
                                </td>
                                <td class="small text-secondary" style="max-width: 200px;">
                                    <?= htmlspecialchars($v['description'] ?: 'Khuyến mãi mua hàng') ?>
                                </td>
                                <td>
                                    <?= ($v['discount_type'] === 'percent') ? '<span class="badge bg-info text-dark">Phần trăm (%)</span>' : '<span class="badge bg-secondary">Tiền mặt (₫)</span>' ?>
                                </td>
                                <td class="fw-bold text-dark">
                                    <?= ($v['discount_type'] === 'percent') ? ((int)$v['discount_value'] . '%') : format_price($v['discount_value']) ?>
                                </td>
                                <td class="small"><?= format_price($v['min_order_value']) ?></td>
                                <td class="small text-secondary">
                                    <?= !empty($v['max_discount']) ? format_price($v['max_discount']) : 'Không giới hạn' ?>
                                </td>
                                <td class="small">
                                    <strong><?= $v['used_count'] ?></strong> / <?= $v['usage_limit'] !== null ? $v['usage_limit'] : '∞' ?>
                                </td>
                                <td class="small text-secondary">
                                    <?php if (!empty($v['starts_at']) || !empty($v['ends_at'])): ?>
                                        <?= !empty($v['starts_at']) ? date('d/m/Y', strtotime($v['starts_at'])) : 'Bất đầu' ?>
                                        - <?= !empty($v['ends_at']) ? date('d/m/Y', strtotime($v['ends_at'])) : 'Vô hạn' ?>
                                    <?php else: ?>
                                        Không thời hạn
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?= ($v['status'] === 'active') ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= ($v['status'] === 'active') ? 'Hoạt động' : 'Tạm khóa' ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="<?= route_url('admin-voucher-edit', ['id' => $v['id']]) ?>" class="btn btn-sm btn-outline-dark" title="Sửa">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <a href="<?= route_url('admin-voucher-delete', ['id' => $v['id']]) ?>" class="btn btn-sm btn-outline-danger" title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa mã voucher này?');">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="10" class="text-center py-4 text-secondary">Chưa có mã giảm giá nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
