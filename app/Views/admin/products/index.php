<div class="container-fluid">
    <?php if (!empty($lowStock)): ?>
        <div class="alert alert-warning d-flex justify-content-between align-items-center">
            <span><i class="fa-solid fa-triangle-exclamation me-2"></i><strong><?= count($lowStock) ?></strong> biến thể đang sắp hết hàng (tồn không quá 5 đôi).</span>
            <a href="<?= route_url('admin-inventory') ?>" class="alert-link">Xem tồn kho / nhập hàng</a>
        </div>
    <?php endif; ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Danh Sách Sản Phẩm Giày</h4>
            <p class="text-secondary small mb-0">Quản lý kho giày, giá bán và các biến thể kích cỡ</p>
        </div>
        <a href="<?= route_url('admin-product-create') ?>" class="btn btn-primary rounded-pill px-4">
            <i class="fa-solid fa-plus me-1"></i> Thêm Giày Mới
        </a>
    </div>

    <div class="bg-white p-4 rounded-4 border shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead class="table-light">
                    <tr>
                        <th style="width: 70px;">Ảnh</th>
                        <th>Tên Giày</th>
                        <th>Hãng</th>
                        <th>Danh Mục</th>
                        <th>Giá Bán</th>
                        <th>Biến Thể</th>
                        <th>Tồn Kho</th>
                        <th>Trạng Thái</th>
                        <th class="text-end" style="width: 140px;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $p): ?>
                            <tr>
                                <td>
                                    <img src="<?= htmlspecialchars(image_src($p['image_url'] ?: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600')) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="rounded-3 border" style="width: 55px; height: 55px; object-fit: cover;">
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($p['name']) ?></div>
                                    <small class="text-secondary">Slug: <?= htmlspecialchars($p['slug']) ?></small>
                                </td>
                                <td><span class="badge bg-dark"><?= htmlspecialchars($p['brand_name'] ?? 'Chưa rõ') ?></span></td>
                                <td><?= htmlspecialchars($p['category_name'] ?? 'Chưa phân loại') ?></td>
                                <td>
                                    <span class="fw-bold text-danger"><?= format_price($p['min_price'] ?? 0) ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark"><?= $p['variant_count'] ?? 0 ?> size</span>
                                </td>
                                <td>
                                    <span class="fw-semibold <?= ($p['total_stock'] > 0) ? 'text-success' : 'text-danger' ?>">
                                        <?= $p['total_stock'] ?? 0 ?> đôi
                                    </span>
                                </td>
                                <td>
                                    <?php if ($p['status'] === 'active'): ?>
                                        <span class="badge bg-success">Đang bán</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Tạm ẩn</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <a href="<?= route_url('admin-product-edit', ['id' => $p['id']]) ?>" class="btn btn-sm btn-outline-primary me-1">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form class="d-inline" method="POST" action="<?= route_url('admin-product-delete') ?>" onsubmit="return confirm('Ẩn sản phẩm này khỏi cửa hàng?')">
                                        <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                        <button class="btn btn-sm btn-outline-danger" type="submit" aria-label="Ẩn sản phẩm"><i class="fa-solid fa-eye-slash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9" class="text-center py-4 text-secondary">Chưa có sản phẩm nào. Hãy thêm ngay!</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
