<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\User;

class AuthController extends BaseController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function login(): void
    {
        if (auth()) {
            $this->redirect(is_admin() ? route_url('admin') : route_url());
        }

        $this->view('login', [
            'pageTitle' => 'Đăng Nhập Tài Khoản - SoleVibe',
        ]);
    }

    public function postLogin(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            set_flash('error', 'Vui lòng nhập đầy đủ Email và Mật khẩu!', 'danger');
            $this->redirect(route_url('login'));
        }

        $user = $this->userModel->checkLogin($email, $password);

        if (!$user) {
            set_flash('error', 'Email hoặc mật khẩu không chính xác!', 'danger');
            $this->redirect(route_url('login'));
        }

        $_SESSION['user'] = [
            'id'        => $user['id'],
            'full_name' => $user['full_name'],
            'email'     => $user['email'],
            'role'      => $user['role'],
            'role_name' => $user['role_name'] ?? 'Khách hàng',
            'is_admin'  => $user['is_admin'],
        ];

        set_flash('success', "Chào mừng {$user['full_name']} quay trở lại!", 'success');

        if ($user['is_admin']) {
            $this->redirect(route_url('admin'));
        } else {
            $this->redirect(route_url());
        }
    }

    public function register(): void
    {
        if (auth()) {
            $this->redirect(route_url());
        }

        $this->view('register', [
            'pageTitle' => 'Đăng Ký Tài Khoản Mới - SoleVibe',
        ]);
    }

    public function postRegister(): void
    {
        $fullNameInput = $_POST['full_name'] ?? null;
        $emailInput = $_POST['email'] ?? null;
        $phoneInput = $_POST['phone'] ?? '';
        $password = $_POST['password'] ?? null;
        $confirmPassword = $_POST['confirm_password'] ?? null;

        if (
            !is_string($fullNameInput) ||
            !is_string($emailInput) ||
            !is_string($phoneInput) ||
            !is_string($password) ||
            !is_string($confirmPassword)
        ) {
            set_flash('error', 'Vui lòng điền đầy đủ các thông tin bắt buộc!', 'danger');
            $this->redirect(route_url('register'));
        }

        $fullName = trim($fullNameInput);
        $email = strtolower(trim($emailInput));
        $phone = trim($phoneInput);
        $nameLength = preg_match_all('/./us', $fullName);

        if ($nameLength === false || $nameLength < 2 || $nameLength > 100 || preg_match('/[\x00-\x1F\x7F]/', $fullName)) {
            set_flash('error', 'Họ và tên phải có từ 2 đến 100 ký tự hợp lệ!', 'danger');
            $this->redirect(route_url('register'));
        }

        if (strlen($email) > 254 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            set_flash('error', 'Vui lòng nhập địa chỉ email hợp lệ!', 'danger');
            $this->redirect(route_url('register'));
        }

        if ($phone !== '') {
            $digits = preg_replace('/\D/', '', $phone);
            if (
                strlen($phone) > 20 ||
                preg_match('/\A\+?[0-9\s().-]+\z/', $phone) !== 1 ||
                $digits === null ||
                strlen($digits) < 8 ||
                strlen($digits) > 15
            ) {
                set_flash('error', 'Số điện thoại không hợp lệ!', 'danger');
                $this->redirect(route_url('register'));
            }
        }

        $passwordLength = preg_match_all('/./us', $password);
        if ($passwordLength === false || $passwordLength < 8) {
            set_flash('error', 'Mật khẩu phải có ít nhất 8 ký tự!', 'danger');
            $this->redirect(route_url('register'));
        }

        if (strlen($password) > 72) {
            set_flash('error', 'Mật khẩu không được dài quá 72 byte!', 'danger');
            $this->redirect(route_url('register'));
        }

        if ($password !== $confirmPassword) {
            set_flash('error', 'Mật khẩu xác nhận không khớp!', 'danger');
            $this->redirect(route_url('register'));
        }

        $existing = $this->userModel->findByEmail($email);
        if ($existing) {
            set_flash('error', 'Email này đã được sử dụng, vui lòng chọn email khác!', 'danger');
            $this->redirect(route_url('register'));
        }

        try {
            $userId = $this->userModel->register($fullName, $email, $phone, $password);
        } catch (\PDOException $exception) {
            error_log('Account registration failed: ' . $exception->getMessage());
            set_flash('error', 'Không thể lưu thông tin đăng ký lúc này. Vui lòng thử lại sau!', 'danger');
            $this->redirect(route_url('register'));
        }

        if ($userId) {
            set_flash('success', 'Đăng ký tài khoản thành công! Vui lòng đăng nhập.', 'success');
            $this->redirect(route_url('login'));
        } else {
            set_flash('error', 'Đăng ký thất bại, vui lòng thử lại sau!', 'danger');
            $this->redirect(route_url('register'));
        }
    }

    public function logout(): void
    {
        unset($_SESSION['user']);
        set_flash('success', 'Đã đăng xuất tài khoản thành công!', 'info');
        $this->redirect(route_url());
    }
}
