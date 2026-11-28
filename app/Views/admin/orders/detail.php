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
                <?php if (!empty($order['cancellation_reason'])): ?>
                    <p class="mb-2"><strong>Lý do hủy:</strong> <em class="text-secondary"><?= nl2br(htmlspecialchars($order['cancellation_reason'])) ?></em></p>
                <?php endif; ?>
                <hr>
                <p class="mb-2"><strong>Phương thức TT:</strong> <?= payment_method_label($order['payment_method']) ?></p>
                <p class="mb-0">
                    <strong>Trạng thái TT:</strong>
                    <span class="badge <?= match ($order['payment_status']) {
                        'paid' => 'bg-success',
                        'failed' => 'bg-danger',
                        'refunded', 'partially_refunded' => 'bg-secondary',
                        default => 'bg-warning text-dark',
                    } ?>">
                        <?= payment_status_label($order['payment_status']) ?>
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
                            <option value="pending" <?= ($order['order_status'] === 'pending') ? 'selected' : '' ?>>Chờ xác nhận</option>
                            <option value="confirmed" <?= ($order['order_status'] === 'confirmed') ? 'selected' : '' ?>>Đã xác nhận</option>
                            <option value="processing" <?= ($order['order_status'] === 'processing') ? 'selected' : '' ?>>Đang đóng gói</option>
                            <option value="shipping" <?= ($order['order_status'] === 'shipping') ? 'selected' : '' ?>>Đang giao hàng</option>
                            <?php if ($order['order_status'] === 'completed'): ?>
                                <option value="completed" selected>Đã hoàn tất (do khách xác nhận)</option>
                            <?php endif; ?>
                            <?php if ($order['order_status'] === 'returned'): ?>
                                <option value="returned" selected>Đã trả hàng</option>
                            <?php endif; ?>
                        </select>
                        <small class="text-muted d-block mt-1">Chỉ chuyển từng bước: chờ xác nhận → xác nhận → đóng gói → giao hàng.</small>
                    </div>

                    <?php if (in_array($order['order_status'], ['pending','confirmed','processing'], true)): ?>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Lý do hủy (nếu hủy đơn)</label>
                            <textarea name="cancellation_reason" class="form-control form-control-sm" rows="2" maxlength="500" placeholder="Bắt buộc nếu hủy đơn"></textarea>
                        </div>
                        <div class="mb-3">
                            <button type="submit" name="order_status" value="cancelled" class="btn btn-outline-danger w-100 rounded-pill fw-bold">
                                <i class="fa-solid fa-ban me-1"></i> Hủy Đơn Hàng
                            </button>
                        </div>
                    <?php endif; ?>

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

            <?php if (($order['cancellation_status'] ?? 'none') === 'pending'): ?>
                <div class="bg-white p-4 rounded-4 border shadow-sm mt-4">
                    <h5 class="fw-bold text-danger mb-3">Yêu Cầu Hủy Đơn Của Khách</h5>
                    <p class="small"><strong>Lý do:</strong> <?= nl2br(htmlspecialchars($order['cancellation_reason'])) ?></p>
                    <div class="d-flex gap-2">
                        <form action="<?= route_url('admin-order-cancel-approve') ?>" method="POST" class="flex-grow-1">
                            <input type="hidden" name="id" value="<?= $order['id'] ?>">
                            <button class="btn btn-danger w-100 rounded-pill" onclick="return confirm('Đồng ý hủy đơn và hoàn kho?');">Đồng ý hủy</button>
                        </form>
                        <form action="<?= route_url('admin-order-cancel-reject') ?>" method="POST" class="flex-grow-1">
                            <input type="hidden" name="id" value="<?= $order['id'] ?>">
                            <input name="reason" class="form-control form-control-sm mb-2" placeholder="Lý do từ chối" maxlength="500" required>
                            <button class="btn btn-outline-secondary w-100 rounded-pill">Từ chối hủy</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (($order['return_status'] ?? 'none') === 'pending'): ?>
                <div class="bg-white p-4 rounded-4 border shadow-sm mt-4">
                    <h5 class="fw-bold text-warning mb-3">Yêu Cầu Đổi/Trả Hàng</h5>
                    <p class="small"><strong>Lý do:</strong> <?= nl2br(htmlspecialchars($order['return_reason'] ?? '')) ?></p>
                    <?php $returnImages = json_decode($order['return_images'] ?? '[]', true); ?>
                    <?php if (is_array($returnImages)): ?>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <?php foreach ($returnImages as $path): ?>
                                <a href="<?= htmlspecialchars(image_src($path)) ?>" target="_blank">
                                    <img src="<?= htmlspecialchars(image_src($path)) ?>" class="rounded border" style="width:90px;height:90px;object-fit:cover">
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <form action="<?= route_url('admin-order-return-approve') ?>" method="POST" class="mb-2">
                        <input type="hidden" name="id" value="<?= $order['id'] ?>">
                        <button class="btn btn-warning w-100 rounded-pill fw-bold">Đồng ý đổi/trả</button>
                    </form>
                    <form action="<?= route_url('admin-order-return-reject') ?>" method="POST">
                        <input type="hidden" name="id" value="<?= $order['id'] ?>">
                        <input name="reason" class="form-control form-control-sm mb-2" placeholder="Lý do từ chối đổi/trả" maxlength="500" required>
                        <button class="btn btn-outline-secondary w-100 rounded-pill">Từ chối đổi/trả</button>
                    </form>
                </div>
            <?php endif; ?>

            <?php if (($order['return_status'] ?? 'none') === 'approved'): ?>
                <div class="bg-white p-4 rounded-4 border shadow-sm mt-4">
                    <h5 class="fw-bold mb-3">Xác Nhận Nhận Hàng Trả</h5>
                    <form action="<?= route_url('admin-order-return-received') ?>" method="POST">
                        <input type="hidden" name="id" value="<?= $order['id'] ?>">
                        <button class="btn btn-dark w-100 rounded-pill" onclick="return confirm('Xác nhận đã nhận hàng trả lại?');">Xác nhận đã nhận hàng</button>
                    </form>
                </div>
            <?php endif; ?>
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
