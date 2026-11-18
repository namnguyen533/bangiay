<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <p class="text-secondary mb-0">Duyệt, ẩn hoặc gửi phản hồi cho đánh giá của khách hàng.</p>
        <form method="GET" class="d-flex gap-2">
            <input type="hidden" name="act" value="admin-reviews">
            <select name="status" class="form-select" aria-label="Lọc trạng thái">
                <option value="" <?= $selectedStatus === '' ? 'selected' : '' ?>>Tất cả trạng thái</option>
                <option value="pending" <?= $selectedStatus === 'pending' ? 'selected' : '' ?>>Chờ duyệt</option>
                <option value="visible" <?= $selectedStatus === 'visible' ? 'selected' : '' ?>>Đang hiển thị</option>
                <option value="hidden" <?= $selectedStatus === 'hidden' ? 'selected' : '' ?>>Đã ẩn</option>
            </select>
            <button class="btn btn-outline-primary" type="submit">Lọc</button>
        </form>
    </div>
    <?php foreach ($reviews as $review): ?>
        <article class="bg-white p-4 rounded-4 border shadow-sm mb-3">
            <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
                <div>
                    <a class="fw-bold text-decoration-none" href="<?= route_url('product-detail', ['id' => $review['product_id']]) ?>" target="_blank"><?= htmlspecialchars($review['product_name']) ?></a>
                    <div class="small text-secondary"><?= htmlspecialchars($review['full_name']) ?> · <?= htmlspecialchars($review['email']) ?> · <?= htmlspecialchars($review['created_at']) ?></div>
                </div>
                <div><span class="text-warning"><?= str_repeat('★', (int)$review['rating']) ?><?= str_repeat('☆', 5 - (int)$review['rating']) ?></span> <span class="badge <?= $review['status'] === 'visible' ? 'bg-success' : ($review['status'] === 'hidden' ? 'bg-secondary' : 'bg-warning text-dark') ?>"><?= htmlspecialchars($review['status']) ?></span></div>
            </div>
            <p class="mb-3"><?= nl2br(htmlspecialchars($review['content'])) ?></p>
            <form action="<?= route_url('admin-review-update') ?>" method="POST" class="row g-3">
                <input type="hidden" name="id" value="<?= (int)$review['id'] ?>">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Trạng thái</label>
                    <select class="form-select" name="status">
                        <option value="pending" <?= $review['status'] === 'pending' ? 'selected' : '' ?>>Chờ duyệt</option>
                        <option value="visible" <?= $review['status'] === 'visible' ? 'selected' : '' ?>>Hiển thị</option>
                        <option value="hidden" <?= $review['status'] === 'hidden' ? 'selected' : '' ?>>Ẩn</option>
                    </select>
                </div>
                <div class="col-md-7">
                    <label class="form-label small fw-bold">Phản hồi của cửa hàng</label>
                    <textarea class="form-control" rows="2" name="admin_response" maxlength="3000"><?= htmlspecialchars($review['admin_response'] ?? '') ?></textarea>
                </div>
                <div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary w-100" type="submit">Lưu</button></div>
            </form>
        </article>
    <?php endforeach; ?>
    <?php if (!$reviews): ?><div class="bg-white p-5 rounded-4 border text-center text-secondary">Chưa có đánh giá phù hợp.</div><?php endif; ?>
</div>
