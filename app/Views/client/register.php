<div class="container my-5" style="max-width: 520px;">
    <div class="bg-white p-4 p-md-5 rounded-4 border shadow-sm">
        <div class="text-center mb-4">
            <i class="fa-solid fa-user-plus fs-1 text-danger mb-2"></i>
            <h3 class="fw-bold">Tạo Tài Khoản Mới</h3>
            <p class="text-secondary small">Trở thành thành viên SoleVibe nhận ngập tràn ưu đãi</p>
        </div>

        <form action="<?= route_url('post-register') ?>" method="POST">
            <div class="mb-3">
                <label class="form-label fw-semibold">Họ và Tên <span class="text-danger">*</span></label>
                <input type="text" name="full_name" class="form-control" placeholder="Nguyễn Văn A" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Email Đăng Ký <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Số Điện Thoại</label>
                <input type="tel" name="phone" class="form-control" placeholder="0912345678">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Mật Khẩu <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Xác Nhận Mật Khẩu <span class="text-danger">*</span></label>
                <input type="password" name="confirm_password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold mb-3">
                <i class="fa-solid fa-user-check me-2"></i> Đăng Ký Tài Khoản
            </button>
        </form>

        <div class="text-center mt-3">
            <span class="text-secondary small">Đã có tài khoản?</span>
            <a href="<?= route_url('login') ?>" class="small text-danger fw-bold text-decoration-none ms-1">Đăng nhập ngay</a>
        </div>
    </div>
</div>
