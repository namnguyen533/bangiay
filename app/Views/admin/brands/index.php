<div class="container-fluid">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="bg-white p-4 rounded-4 border shadow-sm">
                <h5 class="fw-bold mb-3">Thêm thương hiệu</h5>
                <form action="<?= route_url('admin-brand-store') ?>" method="POST">
                    <label class="form-label fw-bold" for="brandName">Tên thương hiệu</label>
                    <input id="brandName" name="name" class="form-control mb-3" required maxlength="120" placeholder="Nike, Adidas, Puma...">
                    <button class="btn btn-primary w-100" type="submit"><i class="fa-solid fa-plus me-1"></i>Thêm</button>
                </form>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="bg-white p-4 rounded-4 border shadow-sm">
                <h5 class="fw-bold mb-3">Thương hiệu hiện có</h5>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead><tr><th>Tên</th><th>Slug</th><th>Sản phẩm</th><th>Trạng thái</th><th></th></tr></thead>
                        <tbody>
                        <?php foreach ($brands as $brand): ?>
                            <tr>
                                <td class="fw-semibold"><?= htmlspecialchars($brand['name']) ?></td>
                                <td><code><?= htmlspecialchars($brand['slug']) ?></code></td>
                                <td><?= (int)$brand['product_count'] ?></td>
                                <td><span class="badge <?= $brand['status'] === 'active' ? 'bg-success' : 'bg-secondary' ?>"><?= $brand['status'] === 'active' ? 'Hiển thị' : 'Tạm ẩn' ?></span></td>
                                <td class="text-end text-nowrap">
                                    <a class="btn btn-sm btn-outline-primary" href="<?= route_url('admin-brand-edit', ['id' => $brand['id']]) ?>"><i class="fa-solid fa-pen"></i></a>
                                    <form class="d-inline" action="<?= route_url('admin-brand-delete') ?>" method="POST" onsubmit="return confirm('Xóa thương hiệu này?')">
                                        <input type="hidden" name="id" value="<?= (int)$brand['id'] ?>">
                                        <button class="btn btn-sm btn-outline-danger" type="submit"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$brands): ?><tr><td colspan="5" class="text-center text-secondary">Chưa có thương hiệu.</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
