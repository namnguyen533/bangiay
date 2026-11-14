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
        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($fullName) || empty($email) || empty($password)) {
            set_flash('error', 'Vui lòng điền đầy đủ các thông tin bắt buộc!', 'danger');
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

        $userId = $this->userModel->register($fullName, $email, $phone, $password);

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
