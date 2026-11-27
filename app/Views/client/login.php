<main class="auth-page container">
    <div class="auth-card">
        <section class="auth-showcase" aria-label="Giới thiệu Apple Meo Meo">
            <div>
                <span class="auth-brand-mark"><img src="<?= htmlspecialchars(asset('images/apple-meo-meo-logo.png')) ?>" alt="Apple Meo Meo Shoes Store"></span>
                <p class="auth-eyebrow text-white-50">Apple Meo Meo Shoes Store</p>
                <h1>Phong cách bắt đầu từ bước chân của bạn.</h1>
                <p class="auth-showcase-copy">Đăng nhập để tiếp tục khám phá những đôi sneaker dành riêng cho cá tính của bạn.</p>
                <ul class="auth-benefits">
                    <li><i class="fa-solid fa-circle-check"></i><span>Sneaker chính hãng</span></li>
                    <li><i class="fa-solid fa-truck-fast"></i><span>Giao hàng nhanh chóng</span></li>
                    <li><i class="fa-solid fa-arrow-rotate-left"></i><span>Đổi trả trong 30 ngày</span></li>
                </ul>
            </div>
            <p class="auth-showcase-note mb-0">Bước đi tự tin. Phong cách khác biệt.</p>
        </section>

        <section class="auth-form-panel">
            <div class="auth-form-content">
                <p class="auth-eyebrow">Chào mừng trở lại</p>
                <h2 class="auth-form-title">Đăng nhập</h2>
                <p class="auth-form-subtitle">Nhập thông tin tài khoản để tiếp tục mua sắm.</p>

                <form action="<?= route_url('post-login') ?>" method="POST" class="auth-form">
                    <div class="mb-3">
                        <label for="loginEmail" class="form-label">Email</label>
                        <input type="email" id="loginEmail" name="email" class="form-control" placeholder="ban@email.com" autocomplete="email" required>
                    </div>

                    <div class="mb-4">
                        <label for="loginPassword" class="form-label">Mật khẩu</label>
                        <input type="password" id="loginPassword" name="password" class="form-control" placeholder="Nhập mật khẩu của bạn" autocomplete="current-password" required>
                    </div>

                    <button type="submit" class="btn btn-primary auth-submit w-100 fw-bold">
                        Đăng nhập <i class="fa-solid fa-arrow-right ms-2"></i>
                    </button>
                </form>

                <div class="auth-divider">hoặc dùng tài khoản thử nghiệm</div>

                <div class="auth-demo small">
                    <div class="fw-bold text-dark mb-1">Tài khoản Admin</div>
                    <div class="text-secondary mb-2"><code>admin@applemeomeo.vn</code> / <code>123456</code></div>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="document.getElementById('loginEmail').value='admin@applemeomeo.vn'; document.getElementById('loginPassword').value='123456';">
                        <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Điền thông tin
                    </button>
                </div>

                <p class="auth-switch">Chưa có tài khoản? <a href="<?= route_url('register') ?>">Tạo tài khoản mới</a></p>

                <div class="auth-admin-entry">
                    <p class="auth-admin-entry-label">Dành cho quản trị viên</p>
                    <a href="<?= route_url('admin') ?>" class="btn btn-outline-dark w-100 fw-semibold">
                        <i class="fa-solid fa-gauge-high me-2"></i>Vào trang quản trị
                    </a>
                </div>
            </div>
        </section>
    </div>
</main>
