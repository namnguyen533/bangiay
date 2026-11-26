    <?php $footerBrands = (new \App\Models\Brand())->getAllActive(); ?>
    <!-- Footer -->
    <footer class="py-5 mt-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <h5 class="text-white fw-bold mb-3">
                        <i class="fa-solid fa-shoe-prints me-2 text-danger"></i>SoleVibe Sneaker Store
                    </h5>
                    <p class="small text-secondary mb-3">
                        Hệ thống phân phối giày sneaker chính hãng hàng đầu Việt Nam. Cam kết 100% chính hãng, đền gấp 10 lần nếu phát hiện hàng giả.
                    </p>
                    <div class="d-flex gap-3 text-white fs-5">
                        <a href="#" class="text-secondary hover-white"><i class="fa-brands fa-facebook"></i></a>
                        <a href="#" class="text-secondary hover-white"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="text-secondary hover-white"><i class="fa-brands fa-tiktok"></i></a>
                        <a href="#" class="text-secondary hover-white"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h6 class="text-white fw-bold mb-3">Thương Hiệu</h6>
                    <ul class="list-unstyled small">
                        <?php foreach ($footerBrands as $brand): ?>
                            <li class="mb-2"><a href="<?= route_url('products', ['brand_id' => $brand['id']]) ?>" class="text-decoration-none text-secondary"><?= htmlspecialchars($brand['name']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h6 class="text-white fw-bold mb-3">Chính Sách & Hỗ Trợ</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="#" class="text-decoration-none text-secondary">Chính sách bảo hành 12 tháng</a></li>
                        <li class="mb-2"><a href="#" class="text-decoration-none text-secondary">Chính sách đổi trả trong 30 ngày</a></li>
                        <li class="mb-2"><a href="#" class="text-decoration-none text-secondary">Hướng dẫn chọn size giày chuẩn</a></li>
                        <li class="mb-2"><a href="#" class="text-decoration-none text-secondary">Phương thức vận chuyển & thanh toán</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h6 class="text-white fw-bold mb-3">Liên Hệ Trực Tiếp</h6>
                    <p class="small text-secondary mb-2"><i class="fa-solid fa-location-dot me-2 text-danger"></i> 123 Đường Cầu Giấy, Hà Nội</p>
                    <p class="small text-secondary mb-2"><i class="fa-solid fa-phone me-2 text-danger"></i> 0901.234.567</p>
                    <p class="small text-secondary mb-2"><i class="fa-solid fa-envelope me-2 text-danger"></i> hotro@solevibe.vn</p>
                    <p class="small text-secondary"><i class="fa-solid fa-clock me-2 text-danger"></i> 08:30 - 22:00 tất cả các ngày</p>
                </div>
            </div>

            <hr class="border-secondary my-4">

            <div class="d-flex flex-wrap justify-content-between align-items-center small text-secondary">
                <p class="mb-0">&copy; <?= date('Y') ?> SoleVibe Store. Thiết kế theo kiến trúc chuẩn PHP MVC (PDO & MySQL).</p>
                <p class="mb-0">Dự Án Website Bán Giày Chuyên Nghiệp</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Live Search Suggestion Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const suggestBox = document.getElementById('searchSuggestBox');
        if (!searchInput || !suggestBox) return;

        let debounceTimer = null;
        let latestRequest = 0;
        const suggestUrl = '<?= route_url('search-suggest') ?>';
        const resultsUrl = '<?= route_url('products') ?>';
        const escapeHtml = value => String(value || '').replace(/[&<>"']/g, character => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        })[character]);

        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            const requestId = ++latestRequest;
            clearTimeout(debounceTimer);

            if (query.length < 1) {
                suggestBox.classList.add('d-none');
                suggestBox.innerHTML = '';
                return;
            }

            debounceTimer = setTimeout(() => {
                const requestUrl = new URL(suggestUrl, window.location.href);
                requestUrl.searchParams.set('q', query);
                fetch(requestUrl)
                    .then(res => {
                        if (!res.ok) throw new Error('Search suggestions request failed');
                        return res.json();
                    })
                    .then(res => {
                        if (requestId !== latestRequest) return;
                        if (!res.success) throw new Error('Search suggestions unavailable');

                        const brands = res.brands || [];
                        const products = res.products || [];
                        if (brands.length === 0 && products.length === 0) {
                            suggestBox.innerHTML = `
                                <div class="p-3 text-center text-muted small">
                                    <i class="fa-solid fa-circle-info me-1"></i> Không tìm thấy hãng hoặc giày phù hợp
                                </div>`;
                            suggestBox.classList.remove('d-none');
                            return;
                        }

                        let html = '';
                        if (brands.length > 0) {
                            html += '<div class="small fw-bold text-uppercase text-muted px-2 py-1 mb-1">Thương hiệu</div>';
                            brands.forEach(brand => {
                                html += `
                                    <a href="${escapeHtml(brand.url)}" class="d-flex justify-content-between align-items-center p-2 text-decoration-none text-dark rounded-2 border-bottom border-light">
                                        <span class="fw-semibold small"><i class="fa-solid fa-tag text-danger me-2"></i>${escapeHtml(brand.name)}</span>
                                        <span class="text-muted small">${escapeHtml(brand.product_count)} sản phẩm</span>
                                    </a>`;
                            });
                        }
                        if (products.length > 0) {
                            html += '<div class="small fw-bold text-uppercase text-muted px-2 py-1 mt-2 mb-1">Giày</div>';
                            products.forEach(item => {
                            html += `
                                <a href="${escapeHtml(item.url)}" class="d-flex align-items-center gap-3 p-2 text-decoration-none text-dark rounded-2 hover-bg-light transition border-bottom border-light">
                                    <img src="${escapeHtml(item.image_url)}" alt="${escapeHtml(item.name)}" class="rounded border" style="width: 46px; height: 46px; object-fit: cover;">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <div class="fw-semibold text-truncate small">${escapeHtml(item.name)}</div>
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <span class="text-danger fw-bold small">${escapeHtml(item.price)}</span>
                                            ${item.sku ? `<span class="badge bg-secondary" style="font-size: 0.65rem;">SKU: ${escapeHtml(item.sku)}</span>` : ''}
                                            ${item.brand_name ? `<span class="text-muted" style="font-size: 0.72rem;">${escapeHtml(item.brand_name)}</span>` : ''}
                                        </div>
                                    </div>
                                </a>`;
                            });
                        }
                        const allResultsUrl = new URL(resultsUrl, window.location.href);
                        allResultsUrl.searchParams.set('q', query);
                        html += `
                            <div class="pt-2 text-center">
                                <a href="${escapeHtml(allResultsUrl.toString())}" class="small text-danger fw-bold text-decoration-none">
                                    Xem tất cả kết quả cho "${escapeHtml(query)}" <i class="fa-solid fa-arrow-right ms-1"></i>
                                </a>
                            </div>`;
                        suggestBox.innerHTML = html;
                        suggestBox.classList.remove('d-none');
                    })
                    .catch(() => {
                        if (requestId !== latestRequest) return;
                        suggestBox.innerHTML = '<div class="p-3 text-center text-muted small">Không tải được gợi ý. Bạn vẫn có thể nhấn Enter để tìm kiếm.</div>';
                        suggestBox.classList.remove('d-none');
                    });
            }, 250);
        });

        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !suggestBox.contains(e.target)) {
                suggestBox.classList.add('d-none');
            }
        });

        searchInput.addEventListener('focus', function() {
            if (this.value.trim().length >= 1 && suggestBox.innerHTML.trim() !== '') {
                suggestBox.classList.remove('d-none');
            }
        });
    });
    </script>
</body>
</html>
