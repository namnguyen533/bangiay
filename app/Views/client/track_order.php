<div class="container my-5" style="max-width: 820px;">
    <div class="text-center mb-4">
        <h3 class="fw-bold"><i class="fa-solid fa-truck-fast text-danger me-2"></i> Tra Cứu & Theo Dõi Đơn Hàng</h3>
        <p class="text-secondary small">Nhập mã đơn hàng và số điện thoại của bạn để kiểm tra tình trạng giao hàng nhanh chóng</p>
    </div>

    <!-- Form Tra Cứu -->
    <div class="bg-white p-4 rounded-4 border shadow-sm mb-4">
        <form action="<?= route_url('track-order') ?>" method="GET" class="row g-3 align-items-end">
            <input type="hidden" name="act" value="track-order">
            <div class="col-md-5">
                <label class="form-label fw-semibold small">Mã Đơn Hàng <span class="text-danger">*</span></label>
                <input type="text" name="order_code" class="form-control text-uppercase" placeholder="Ví dụ: ORD-20261119-XXXX" value="<?= htmlspecialchars($orderCode ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Số Điện Thoại Nhận Hàng</label>
                <input type="tel" name="phone" class="form-control" placeholder="Ví dụ: 0987654321" value="<?= htmlspecialchars($phone ?? '') ?>">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold">
                    <i class="fa-solid fa-magnifying-glass me-1"></i> Tra Cứu
                </button>
            </div>
        </form>
    </div>

    <!-- Kết Quả Tra Cứu -->
    <?php if (!empty($order)): ?>
        <div class="bg-white p-4 rounded-4 border shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                <div>
                    <h5 class="fw-bold mb-1 text-danger">#<?= htmlspecialchars($order['order_code']) ?></h5>
                    <span class="small text-secondary">Ngày đặt: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></span>
                </div>
                <div>
                    <span class="badge <?= order_status_badge($order['order_status']) ?> fs-6 px-3 py-2">
                        <?= order_status_label($order['order_status']) ?>
                    </span>
                </div>
            </div>

            <!-- Timeline -->
            <?php
            $steps = [
                'pending'    => ['label' => 'Chờ xác nhận', 'icon' => 'fa-clipboard-list'],
                'confirmed'  => ['label' => 'Đã xác nhận', 'icon' => 'fa-check-double'],
                'processing' => ['label' => 'Đang đóng gói', 'icon' => 'fa-box-open'],
                'shipping'   => ['label' => 'Đang giao hàng', 'icon' => 'fa-truck-fast'],
                'completed'  => ['label' => 'Giao thành công', 'icon' => 'fa-circle-check'],
            ];

            $orderStatusOrder = ['pending', 'confirmed', 'processing', 'shipping', 'completed'];
            $currentIndex = array_search($order['order_status'], $orderStatusOrder);
            if ($currentIndex === false) $currentIndex = 0;
            ?>
            <div class="position-relative my-4 mx-3">
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar bg-danger" role="progressbar" style="width: <?= ($currentIndex / (count($orderStatusOrder) - 1)) * 100 ?>%;"></div>
                </div>
                <div class="d-flex justify-content-between position-absolute top-0 start-0 w-100 translate-middle-y">
                    <?php foreach ($orderStatusOrder as $idx => $stKey): ?>
                        <?php 
                        $stepInfo = $steps[$stKey];
                        $isPassed = $idx <= $currentIndex;
                        $isCurrent = $idx === $currentIndex;
                        ?>
                        <div class="text-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 shadow-sm <?= $isPassed ? 'bg-danger text-white' : 'bg-white text-secondary border' ?>" style="width: 38px; height: 38px;">
                                <i class="fa-solid <?= $stepInfo['icon'] ?>"></i>
                            </div>
                            <span class="small fw-semibold d-block <?= $isCurrent ? 'text-danger fw-bold' : ($isPassed ? 'text-dark' : 'text-muted') ?>">
                                <?= $stepInfo['label'] ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div style="height: 35px;"></div>

            <!-- Sản Phẩm -->
            <div class="table-responsive mt-4">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Đơn giá</th>
                            <th class="text-center">Số lượng</th>
                            <th class="text-end">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order['items'] as $it): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($it['product_name']) ?></strong>
                                    <div class="small text-secondary">Size <?= htmlspecialchars($it['size']) ?> | <?= htmlspecialchars($it['color']) ?></div>
                                </td>
                                <td><?= format_price($it['unit_price']) ?></td>
                                <td class="text-center"><?= $it['quantity'] ?></td>
                                <td class="text-end fw-bold text-danger"><?= format_price($it['line_total']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                <div>
                    <span class="text-secondary small">Người nhận: <strong><?= htmlspecialchars($order['recipient_name']) ?></strong></span>
                    <span class="text-secondary small ms-3">SĐT: <strong><?= htmlspecialchars($order['recipient_phone']) ?></strong></span>
                </div>
                <div class="text-end">
                    <span class="text-secondary me-2">Tổng tiền:</span>
                    <span class="fs-5 fw-bold text-danger"><?= format_price($order['total_amount']) ?></span>
                </div>
            </div>
        </div>
    <?php elseif ($searched): ?>
        <div class="text-center py-4 bg-white rounded-4 border shadow-sm">
            <i class="fa-solid fa-circle-question fs-1 text-secondary mb-3"></i>
            <h5 class="fw-bold">Không tìm thấy đơn hàng!</h5>
            <p class="text-secondary small mb-0">Vui lòng kiểm tra lại chính xác Mã đơn hàng và Số điện thoại.</p>
        </div>
    <?php endif; ?>
</div>
