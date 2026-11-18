<div class="container-fluid">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="bg-white p-4 rounded-4 border shadow-sm">
                <h5 class="fw-bold mb-3">Thêm danh mục</h5>
                <form action="<?= route_url('admin-category-store') ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold" for="categoryName">Tên danh mục *</label>
                        <input id="categoryName" type="text" name="name" class="form-control" maxlength="120" required placeholder="Nam, Nữ, Sneaker...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" for="categoryParent">Danh mục cha</label>
                        <select id="categoryParent" name="parent_id" class="form-select">
                            <option value="">-- Danh mục gốc --</option>
                            <?php foreach ($parentCategories as $parent): ?>
                                <option value="<?= (int)$parent['id'] ?>"><?= str_repeat('&nbsp;&nbsp;', (int)$parent['depth']) ?><?= htmlspecialchars($parent['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-plus me-1"></i>Tạo danh mục</button>
                </form>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="bg-white p-4 rounded-4 border shadow-sm">
                <h5 class="fw-bold mb-3">Cây danh mục</h5>
                <div class="table-responsive">
                    <table class="table align-middle table-hover">
                        <thead class="table-light"><tr><th>Tên</th><th>Slug</th><th>Trạng thái</th><th class="text-end">Thao tác</th></tr></thead>
                        <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td class="fw-semibold"><?= str_repeat('&mdash; ', (int)($category['depth'] ?? 0)) ?><?= htmlspecialchars($category['name']) ?></td>
                                <td><code><?= htmlspecialchars($category['slug']) ?></code></td>
                                <td><span class="badge <?= $category['status'] === 'active' ? 'bg-success' : 'bg-secondary' ?>"><?= $category['status'] === 'active' ? 'Hiển thị' : 'Tạm ẩn' ?></span></td>
                                <td class="text-end text-nowrap">
                                    <a class="btn btn-sm btn-outline-primary" href="<?= route_url('admin-category-edit', ['id' => $category['id']]) ?>"><i class="fa-solid fa-pen"></i></a>
                                    <form class="d-inline" method="POST" action="<?= route_url('admin-category-delete') ?>" onsubmit="return confirm('Xóa danh mục này? Sản phẩm sẽ bị bỏ phân loại.')">
                                        <input type="hidden" name="id" value="<?= (int)$category['id'] ?>">
                                        <button class="btn btn-sm btn-outline-danger" type="submit"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$categories): ?><tr><td colspan="4" class="text-center text-secondary">Chưa có danh mục.</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
