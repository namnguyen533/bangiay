<div class="container-fluid" style="max-width: 980px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Chi Tiết Đơn Hàng <span class="text-danger">#<?= htmlspecialchars($order['order_code']) ?></span></h4>
            <p class="text-secondary small mb-0">Thời gian đặt hàng: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= route_url('admin-order-print', ['id' => $order['id']]) ?>" target="_blank" class="btn btn-outline-dark rounded-pill">
                <i class="fa-solid fa-print me-1"></i> In Hóa Đơn
            </a>
            <a href="<?= route_url('admin-orders') ?>" class="btn btn-outline-secondary rounded-pill">
                <i class="fa-solid fa-arrow-left me-1"></i> Quay Lại
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Thông Tin Khách Hàng & Cập Nhật Trạng Thái -->
        <div class="col-md-5">
            <div class="bg-white p-4 rounded-4 border shadow-sm mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Thông Tin Khách Hàng</h5>
                <p class="mb-2"><strong>Người nhận:</strong> <?= htmlspecialchars($order['recipient_name']) ?></p>
                <p class="mb-2"><strong>Số điện thoại:</strong> <?= htmlspecialchars($order['recipient_phone']) ?></p>
                <p class="mb-2"><strong>Địa chỉ giao:</strong> <?= htmlspecialchars($order['shipping_address']) ?></p>
                <?php if (!empty($order['user_email'])): ?>
                    <p class="mb-2"><strong>Tài khoản:</strong> <?= htmlspecialchars($order['user_email']) ?></p>
                <?php endif; ?>
                <?php if (!empty($order['note'])): ?>
                    <p class="mb-2"><strong>Ghi chú đơn:</strong> <em class="text-secondary"><?= htmlspecialchars($order['note']) ?></em></p>
                <?php endif; ?>
                <hr>
                <p class="mb-2"><strong>Phương thức TT:</strong> <?= payment_method_label($order['payment_method']) ?></p>
                <p class="mb-0">
                    <strong>Trạng thái TT:</strong>
                    <span class="badge <?= ($order['payment_status'] === 'paid') ? 'bg-success' : 'bg-warning text-dark' ?>">
                        <?= ($order['payment_status'] === 'paid') ? 'Đã thanh toán' : 'Chưa thanh toán' ?>
                    </span>
                </p>
            </div>

            <!-- Form Cập Nhật Trạng Thái Đơn & Thanh Toán -->
            <div class="bg-white p-4 rounded-4 border shadow-sm">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Xử Lý & Cập Nhật</h5>
                <form action="<?= route_url('admin-order-update-status') ?>" method="POST">
                    <input type="hidden" name="id" value="<?= $order['id'] ?>">

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Trạng Thái Đơn Hàng</label>
                        <select name="order_status" class="form-select">
                            <option value="pending" <?= ($order['order_status'] === 'pending') ? 'selected' : '' ?>>Chờ xác nhận (Pending)</option>
                            <option value="confirmed" <?= ($order['order_status'] === 'confirmed') ? 'selected' : '' ?>>Đã xác nhận / Duyệt đơn (Confirmed)</option>
                            <option value="processing" <?= ($order['order_status'] === 'processing') ? 'selected' : '' ?>>Đang đóng gói kho (Processing)</option>
                            <option value="shipping" <?= ($order['order_status'] === 'shipping') ? 'selected' : '' ?>>Đang giao hàng (Shipping)</option>
                            <option value="completed" <?= ($order['order_status'] === 'completed') ? 'selected' : '' ?>>Đã giao thành công (Completed)</option>
                            <option value="cancelled" <?= ($order['order_status'] === 'cancelled') ? 'selected' : '' ?>>Hủy đơn hàng (Cancelled - Hoàn kho)</option>
                        </select>
                        <small class="text-muted d-block mt-1">Khi chọn "Hủy đơn hàng", hệ thống sẽ tự động hoàn trả số lượng giày vào kho.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Trạng Thái Thanh Toán</label>
                        <select name="payment_status" class="form-select">
                            <option value="unpaid" <?= ($order['payment_status'] === 'unpaid') ? 'selected' : '' ?>>Chưa thanh toán (Unpaid)</option>
                            <option value="pending" <?= ($order['payment_status'] === 'pending') ? 'selected' : '' ?>>Đang chờ xử lý (Pending)</option>
                            <option value="paid" <?= ($order['payment_status'] === 'paid') ? 'selected' : '' ?>>Đã thanh toán (Paid)</option>
                            <option value="refunded" <?= ($order['payment_status'] === 'refunded') ? 'selected' : '' ?>>Đã hoàn tiền (Refunded)</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold">
                        <i class="fa-solid fa-check me-1"></i> Lưu Cập Nhật
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
                                            | SKU: <code><?= htmlspecialchars($item['sku']) ?></code>
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

                    <?php if (!empty($order['discount_amount']) && $order['discount_amount'] > 0): ?>
                        <div class="d-flex justify-content-between mb-2 text-danger">
                            <span>Giảm giá voucher <?= !empty($order['voucher_code']) ? '(' . htmlspecialchars($order['voucher_code']) . ')' : '' ?>:</span>
                            <span>- <?= format_price($order['discount_amount']) ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Phí vận chuyển:</span>
                        <span><?= ($order['shipping_fee'] == 0) ? 'Miễn phí' : format_price($order['shipping_fee']) ?></span>
                    </div>

                    <div class="d-flex justify-content-between fs-5 fw-bold text-danger pt-2 border-top">
                        <span>Tổng tiền thu:</span>
                        <span><?= format_price($order['total_amount']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
