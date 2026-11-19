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
</body>
</html>
