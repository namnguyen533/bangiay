<main class="auth-page container">
    <div class="auth-card">
        <section class="auth-showcase" aria-label="Giới thiệu Apple Meo Meo">
            <div>
                <span class="auth-brand-mark"><img src="<?= htmlspecialchars(asset('images/apple-meo-meo-logo.png')) ?>" alt="Apple Meo Meo Shoes Store"></span>
                <p class="auth-eyebrow text-white-50">Gia nhập cộng đồng</p>
                <h1>Mỗi đôi giày là một câu chuyện của riêng bạn.</h1>
                <p class="auth-showcase-copy">Tạo tài khoản Apple Meo Meo để lưu lại hành trình phong cách và khám phá những ưu đãi dành riêng cho bạn.</p>
                <ul class="auth-benefits">
                    <li><i class="fa-solid fa-tags"></i><span>Ưu đãi thành viên</span></li>
                    <li><i class="fa-solid fa-heart"></i><span>Khám phá phong cách</span></li>
                    <li><i class="fa-solid fa-shield-halved"></i><span>Mua sắm an tâm</span></li>
                </ul>
            </div>
            <p class="auth-showcase-note mb-0">Bước đi tự tin. Phong cách khác biệt.</p>
        </section>

        <section class="auth-form-panel">
            <div class="auth-form-content">
                <p class="auth-eyebrow">Bắt đầu ngay hôm nay</p>
                <h2 class="auth-form-title">Tạo tài khoản</h2>
                <p class="auth-form-subtitle">Điền thông tin bên dưới để trở thành thành viên Apple Meo Meo.</p>

                <form action="<?= route_url('post-register') ?>" method="POST" class="auth-form">
                    <div class="mb-3">
                        <label for="registerName" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" id="registerName" name="full_name" class="form-control" placeholder="Nguyễn Văn A" autocomplete="name" minlength="2" maxlength="100" required>
                    </div>

                    <div class="mb-3">
                        <label for="registerEmail" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" id="registerEmail" name="email" class="form-control" placeholder="ban@email.com" autocomplete="email" maxlength="254" required>
                    </div>

                    <div class="mb-3">
                        <label for="registerPhone" class="form-label">Số điện thoại</label>
                        <input type="tel" id="registerPhone" name="phone" class="form-control" placeholder="0912 345 678" autocomplete="tel" maxlength="20">
                    </div>

                    <div class="mb-3">
                        <label for="registerPassword" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" id="registerPassword" name="password" class="form-control" placeholder="Tạo mật khẩu (ít nhất 8 ký tự)" autocomplete="new-password" minlength="8" maxlength="72" required>
                    </div>

                    <div class="mb-4">
                        <label for="registerConfirmPassword" class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" id="registerConfirmPassword" name="confirm_password" class="form-control" placeholder="Nhập lại mật khẩu" autocomplete="new-password" minlength="8" maxlength="72" required>
                    </div>

                    <button type="submit" class="btn btn-primary auth-submit w-100 fw-bold">
                        Tạo tài khoản <i class="fa-solid fa-arrow-right ms-2"></i>
                    </button>
                </form>

                <p class="auth-switch">Đã có tài khoản? <a href="<?= route_url('login') ?>">Đăng nhập</a></p>

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
