<div class="container-fluid">
    <?php if (!empty($lowStock)): ?>
        <div class="alert alert-warning">
            <strong><i class="fa-solid fa-triangle-exclamation me-2"></i><?= count($lowStock) ?> biến thể sắp hết hàng.</strong>
            Ngưỡng cảnh báo: còn <?= (int)$threshold ?> đôi trở xuống.
            <div class="mt-2">
                <?php foreach ($lowStock as $variant): ?>
                    <span class="badge text-bg-warning me-1"><?= htmlspecialchars($variant['product_name']) ?> / <?= htmlspecialchars($variant['size']) ?> <?= htmlspecialchars($variant['color']) ?>: <?= (int)$variant['stock_quantity'] ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="row g-4 mb-4">
        <div class="col-xl-5">
            <section class="bg-white p-4 rounded-4 border shadow-sm">
                <h5 class="fw-bold mb-3">Tạo phiếu nhập kho</h5>
                <form action="<?= route_url('admin-stock-receipt-store') ?>" method="POST">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6"><label class="form-label">Nhà cung cấp</label><input name="supplier" class="form-control" maxlength="150"></div>
                        <div class="col-md-6"><label class="form-label">Ghi chú</label><input name="note" class="form-control"></div>
                    </div>
                    <div id="receiptItems" class="d-grid gap-2">
                        <div class="receipt-row row g-2 align-items-end">
                            <div class="col-12"><label class="form-label small">Sản phẩm / biến thể</label>
                                <select name="variant_id[]" class="form-select" required>
                                    <option value="">-- Chọn biến thể --</option>
                                    <?php foreach ($variants as $variant): ?>
                                        <option value="<?= (int)$variant['id'] ?>"><?= htmlspecialchars($variant['product_name']) ?> · <?= htmlspecialchars($variant['sku']) ?> · size <?= htmlspecialchars($variant['size']) ?> / <?= htmlspecialchars($variant['color']) ?> (tồn <?= (int)$variant['stock_quantity'] ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6"><label class="form-label small">Số lượng</label><input name="quantity[]" type="number" min="1" class="form-control" required></div>
                            <div class="col-6"><label class="form-label small">Giá nhập / đôi</label><input name="unit_cost[]" type="number" min="0" step="1000" value="0" class="form-control" required></div>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-outline-primary mt-3" type="button" id="addReceiptItem">Thêm dòng</button>
                    <button class="btn btn-primary w-100 mt-3" type="submit">Lưu phiếu và cộng tồn</button>
                </form>
            </section>
        </div>
        <div class="col-xl-7">
            <section class="bg-white p-4 rounded-4 border shadow-sm">
                <h5 class="fw-bold mb-3">Tồn kho biến thể</h5>
                <div class="table-responsive" style="max-height: 480px">
                    <table class="table table-sm table-hover align-middle">
                        <thead class="table-light"><tr><th>Sản phẩm</th><th>SKU</th><th>Size / màu</th><th class="text-end">Tồn</th></tr></thead>
                        <tbody>
                        <?php foreach ($variants as $variant): ?>
                            <tr>
                                <td><?= htmlspecialchars($variant['product_name']) ?></td>
                                <td><code><?= htmlspecialchars($variant['sku']) ?></code></td>
                                <td><?= htmlspecialchars($variant['size']) ?> · <?= htmlspecialchars($variant['color']) ?></td>
                                <td class="text-end fw-bold <?= (int)$variant['stock_quantity'] <= $threshold ? 'text-danger' : 'text-success' ?>"><?= (int)$variant['stock_quantity'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$variants): ?><tr><td colspan="4" class="text-center text-secondary">Chưa có biến thể sản phẩm đang bán.</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>

    <section class="bg-white p-4 rounded-4 border shadow-sm">
        <h5 class="fw-bold mb-3">Lịch sử phiếu nhập</h5>
        <?php foreach ($receipts as $receipt): ?>
            <details class="border rounded-3 p-3 mb-2">
                <summary class="fw-semibold"><?= htmlspecialchars($receipt['receipt_code']) ?> · <?= htmlspecialchars($receipt['received_at']) ?> · <?= htmlspecialchars($receipt['supplier'] ?: 'Không ghi nhà cung cấp') ?> <span class="text-secondary">— <?= htmlspecialchars($receipt['created_by_name']) ?></span></summary>
                <div class="table-responsive mt-3">
                    <table class="table table-sm mb-0"><thead><tr><th>Sản phẩm / SKU</th><th>Size / màu</th><th>Số lượng</th><th>Giá nhập</th></tr></thead><tbody>
                    <?php foreach ($receipt['items'] as $item): ?>
                        <tr><td><?= htmlspecialchars($item['product_name']) ?> <code><?= htmlspecialchars($item['sku']) ?></code></td><td><?= htmlspecialchars($item['size']) ?> / <?= htmlspecialchars($item['color']) ?></td><td><?= (int)$item['quantity'] ?></td><td><?= format_price($item['unit_cost']) ?></td></tr>
                    <?php endforeach; ?>
                    </tbody></table>
                </div>
                <?php if ($receipt['note']): ?><p class="small text-secondary mb-0"><?= htmlspecialchars($receipt['note']) ?></p><?php endif; ?>
            </details>
        <?php endforeach; ?>
        <?php if (!$receipts): ?><p class="text-secondary mb-0">Chưa có phiếu nhập nào.</p><?php endif; ?>
    </section>
</div>
<template id="receiptRowTemplate">
    <div class="receipt-row row g-2 align-items-end">
        <div class="col-12"><label class="form-label small">Sản phẩm / biến thể</label><select name="variant_id[]" class="form-select" required><option value="">-- Chọn biến thể --</option><?php foreach ($variants as $variant): ?><option value="<?= (int)$variant['id'] ?>"><?= htmlspecialchars($variant['product_name']) ?> · <?= htmlspecialchars($variant['sku']) ?> · size <?= htmlspecialchars($variant['size']) ?> / <?= htmlspecialchars($variant['color']) ?></option><?php endforeach; ?></select></div>
        <div class="col-5"><label class="form-label small">Số lượng</label><input name="quantity[]" type="number" min="1" class="form-control" required></div>
        <div class="col-5"><label class="form-label small">Giá nhập / đôi</label><input name="unit_cost[]" type="number" min="0" step="1000" value="0" class="form-control" required></div>
        <div class="col-2"><button type="button" class="btn btn-outline-danger w-100 remove-receipt-row">&times;</button></div>
    </div>
</template>
<script>
document.getElementById('addReceiptItem').addEventListener('click', () => {
    document.getElementById('receiptItems').insertAdjacentHTML('beforeend', document.getElementById('receiptRowTemplate').innerHTML);
});
document.getElementById('receiptItems').addEventListener('click', event => {
    const button = event.target.closest('.remove-receipt-row');
    if (button && document.querySelectorAll('.receipt-row').length > 1) button.closest('.receipt-row').remove();
});
</script>
