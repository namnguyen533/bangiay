<div class="container my-5" style="max-width: 480px;">
    <div class="bg-white p-4 p-md-5 rounded-4 border shadow-sm">
        <div class="text-center mb-4">
            <i class="fa-solid fa-shoe-prints fs-1 text-danger mb-2"></i>
            <h3 class="fw-bold">Đăng Nhập SoleVibe</h3>
            <p class="text-secondary small">Chào mừng bạn quay lại với thế giới sneaker</p>
        </div>

        <form action="<?= route_url('post-login') ?>" method="POST">
            <div class="mb-3">
                <label class="form-label fw-semibold">Email tài khoản</label>
                <input type="email" id="loginEmail" name="email" class="form-control" placeholder="admin@solemate.vn" required>
            </div>

            <div class="mb-4">
                <div class="d-flex justify-content-between">
                    <label class="form-label fw-semibold">Mật khẩu</label>
                    <a href="#" class="small text-danger text-decoration-none">Quên mật khẩu?</a>
                </div>
                <input type="password" id="loginPassword" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold mb-3">
                <i class="fa-solid fa-right-to-bracket me-2"></i> Đăng Nhập
            </button>
        </form>

        <!-- Tài Khoản Mẫu Để Test Nhanh -->
        <div class="alert alert-light border small text-secondary mt-3">
            <div class="fw-bold text-dark mb-1"><i class="fa-solid fa-key text-warning me-1"></i> Tài khoản mẫu kiểm thử:</div>
            <div>• <strong>Admin:</strong> <code>admin@solemate.vn</code> / <code>123456</code></div>
            <div>• <strong>Khách hàng:</strong> <code>phong@gmail.com</code> / <code>123456</code></div>
            <div class="mt-2 text-center">
                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-3" onclick="document.getElementById('loginEmail').value='admin@solemate.vn'; document.getElementById('loginPassword').value='123456';">
                    Điền nhanh tài khoản Admin
                </button>
            </div>
        </div>

        <div class="text-center mt-4">
            <span class="text-secondary small">Chưa có tài khoản?</span>
            <a href="<?= route_url('register') ?>" class="small text-danger fw-bold text-decoration-none ms-1">Đăng ký ngay</a>
        </div>
    </div>
</div>
