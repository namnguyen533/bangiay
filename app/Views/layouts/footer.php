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
        const suggestUrl = '<?= route_url('search-suggest') ?>';

        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            clearTimeout(debounceTimer);

            if (query.length < 1) {
                suggestBox.classList.add('d-none');
                suggestBox.innerHTML = '';
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`${suggestUrl}&q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(res => {
                        if (!res.success || !res.data || res.data.length === 0) {
                            suggestBox.innerHTML = `
                                <div class="p-3 text-center text-muted small">
                                    <i class="fa-solid fa-circle-info me-1"></i> Không tìm thấy giày hoặc mã SKU phù hợp
                                </div>`;
                            suggestBox.classList.remove('d-none');
                            return;
                        }

                        let html = '<div class="small fw-bold text-uppercase text-muted px-2 py-1 mb-1">Gợi ý tìm kiếm</div>';
                        res.data.forEach(item => {
                            html += `
                                <a href="${item.url}" class="d-flex align-items-center gap-3 p-2 text-decoration-none text-dark rounded-2 hover-bg-light transition border-bottom border-light">
                                    <img src="${item.image_url}" alt="${item.name}" class="rounded border" style="width: 46px; height: 46px; object-fit: cover;">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <div class="fw-semibold text-truncate small">${item.name}</div>
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <span class="text-danger fw-bold small">${item.price}</span>
                                            ${item.sku ? `<span class="badge bg-secondary" style="font-size: 0.65rem;">SKU: ${item.sku}</span>` : ''}
                                            ${item.brand_name ? `<span class="text-muted" style="font-size: 0.72rem;">${item.brand_name}</span>` : ''}
                                        </div>
                                    </div>
                                </a>`;
                        });
                        html += `
                            <div class="pt-2 text-center">
                                <a href="<?= route_url('products') ?>&q=${encodeURIComponent(query)}" class="small text-danger fw-bold text-decoration-none">
                                    Xem tất cả kết quả cho "${query}" <i class="fa-solid fa-arrow-right ms-1"></i>
                                </a>
                            </div>`;
                        suggestBox.innerHTML = html;
                        suggestBox.classList.remove('d-none');
                    })
                    .catch(() => {
                        suggestBox.classList.add('d-none');
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
