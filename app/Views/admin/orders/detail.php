<div class="container-fluid" style="max-width: 960px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Chi Tiết Đơn Hàng #<?= htmlspecialchars($order['order_code']) ?></h4>
            <p class="text-secondary small mb-0">Thời gian đặt: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
        </div>
        <a href="<?= route_url('admin-orders') ?>" class="btn btn-outline-secondary rounded-pill">
            <i class="fa-solid fa-arrow-left me-1"></i> Quay Lại
        </a>
    </div>

    <div class="row g-4">
        <!-- Thông Tin Khách Hàng & Cập Nhật Trạng Thái -->
        <div class="col-md-5">
            <div class="bg-white p-4 rounded-4 border shadow-sm mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Thông Tin Khách Hàng</h5>
                <p class="mb-2"><strong>Người nhận:</strong> <?= htmlspecialchars($order['recipient_name']) ?></p>
                <p class="mb-2"><strong>Số điện thoại:</strong> <?= htmlspecialchars($order['recipient_phone']) ?></p>
                <p class="mb-2"><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['shipping_address']) ?></p>
                <?php if (!empty($order['note'])): ?>
                    <p class="mb-2"><strong>Ghi chú:</strong> <em class="text-secondary"><?= htmlspecialchars($order['note']) ?></em></p>
                <?php endif; ?>
                <p class="mb-0"><strong>Phương thức TT:</strong> <span class="badge bg-secondary"><?= strtoupper($order['payment_method']) ?></span></p>
            </div>

            <!-- Form Cập Nhật Trạng Thái Đơn -->
            <div class="bg-white p-4 rounded-4 border shadow-sm">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Cập Nhật Trạng Thái</h5>
                <form action="<?= route_url('admin-order-update-status') ?>" method="POST">
                    <input type="hidden" name="id" value="<?= $order['id'] ?>">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Trạng Thái Đơn Hàng</label>
                        <select name="order_status" class="form-select">
                            <option value="pending" <?= ($order['order_status'] === 'pending') ? 'selected' : '' ?>>Chờ xử lý (Pending)</option>
                            <option value="confirmed" <?= ($order['order_status'] === 'confirmed') ? 'selected' : '' ?>>Đã xác nhận (Confirmed)</option>
                            <option value="shipping" <?= ($order['order_status'] === 'shipping') ? 'selected' : '' ?>>Đang giao hàng (Shipping)</option>
                            <option value="completed" <?= ($order['order_status'] === 'completed') ? 'selected' : '' ?>>Hoàn thành (Completed)</option>
                            <option value="cancelled" <?= ($order['order_status'] === 'cancelled') ? 'selected' : '' ?>>Đã hủy (Cancelled)</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold">
                        <i class="fa-solid fa-check me-1"></i> Lưu Trạng Thái Mới
                    </button>
                </form>
            </div>
        </div>

        <!-- Danh Sách Sản Phẩm Trong Đơn -->
        <div class="col-md-7">
            <div class="bg-white p-4 rounded-4 border shadow-sm">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Sản Phẩm Trong Đơn</h5>
                <div class="table-responsive mb-3">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Đơn giá</th>
                                <th class="text-center">SL</th>
                                <th class="text-end">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order['items'] as $item): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($item['product_name']) ?></div>
                                        <div class="small text-secondary">
                                            Size: <strong><?= htmlspecialchars($item['size']) ?></strong> | Màu: <?= htmlspecialchars($item['color']) ?>
                                        </div>
                                    </td>
                                    <td><?= format_price($item['unit_price']) ?></td>
                                    <td class="text-center"><?= $item['quantity'] ?></td>
                                    <td class="text-end fw-bold text-danger"><?= format_price($item['line_total']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="border-top pt-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Tạm tính:</span>
                        <span><?= format_price($order['subtotal']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Phí vận chuyển:</span>
                        <span><?= format_price($order['shipping_fee']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between fs-5 fw-bold text-danger pt-2 border-top">
                        <span>Tổng tiền:</span>
                        <span><?= format_price($order['total_amount']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
