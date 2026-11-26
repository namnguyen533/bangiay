<?php

namespace App\Controllers\Admin;

use App\Core\BaseController;
use App\Models\Brand;

class BrandController extends BaseController
{
    private Brand $brandModel;

    public function __construct()
    {
        $this->brandModel = new Brand();
    }

    public function index(): void
    {
        $this->requireAdmin();
        $this->adminView('brands/index', [
            'pageTitle' => 'Quản Lý Thương Hiệu',
            'brands' => $this->brandModel->getAll(),
        ]);
    }

    public function store(): void
    {
        $this->requireAdmin();
        $name = trim($_POST['name'] ?? '');
        if ($name === '' || mb_strlen($name) > 120) {
            set_flash('error', 'Tên thương hiệu không được trống và tối đa 120 ký tự.', 'danger');
            $this->redirect(route_url('admin-brands'));
        }
        if ($this->brandModel->nameExists($name)) {
            set_flash('error', 'Tên thương hiệu này đã tồn tại.', 'danger');
            $this->redirect(route_url('admin-brands'));
        }

        $this->brandModel->create([
            'name' => $name,
            'slug' => $this->slugify($name),
            'status' => 'active',
        ]);
        set_flash('success', 'Đã thêm thương hiệu.', 'success');
        $this->redirect(route_url('admin-brands'));
    }

    public function edit(): void
    {
        $this->requireAdmin();
        $brand = $this->brandModel->findById((int)($_GET['id'] ?? 0));
        if (!$brand) {
            set_flash('error', 'Không tìm thấy thương hiệu.', 'danger');
            $this->redirect(route_url('admin-brands'));
        }
        $this->adminView('brands/edit', [
            'pageTitle' => 'Chỉnh Sửa Thương Hiệu',
            'brand' => $brand,
        ]);
    }

    public function update(): void
    {
        $this->requireAdmin();
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        if (!$this->brandModel->findById($id) || $name === '' || mb_strlen($name) > 120) {
            set_flash('error', 'Thương hiệu không hợp lệ hoặc tên đang để trống.', 'danger');
            $this->redirect(route_url('admin-brands'));
        }
        if ($this->brandModel->nameExists($name, $id)) {
            set_flash('error', 'Tên thương hiệu này đã được sử dụng.', 'danger');
            $this->redirect(route_url('admin-brand-edit', ['id' => $id]));
        }

        $this->brandModel->updateBrand($id, [
            'name' => $name,
            'slug' => $this->slugify($name),
            'status' => in_array($_POST['status'] ?? '', ['active', 'inactive'], true) ? $_POST['status'] : 'active',
        ]);
        set_flash('success', 'Đã cập nhật thương hiệu.', 'success');
        $this->redirect(route_url('admin-brands'));
    }

    public function delete(): void
    {
        $this->requireAdmin();
        $id = (int)($_POST['id'] ?? 0);
        $brand = $this->brandModel->findById($id);
        if (!$brand) {
            set_flash('error', 'Không tìm thấy thương hiệu.', 'danger');
        } else {
            $usage = $this->brandModel->fetchColumn(
                'SELECT COUNT(*) FROM products WHERE brand_id = :id',
                ['id' => $id]
            );
            if ((int)$usage > 0) {
                set_flash('error', 'Thương hiệu đang được sản phẩm sử dụng. Hãy tạm ẩn thay vì xóa.', 'danger');
            } else {
                $this->brandModel->deleteBrand($id);
                set_flash('success', 'Đã xóa thương hiệu.', 'success');
            }
        }
        $this->redirect(route_url('admin-brands'));
    }

    private function slugify(string $value): string
    {
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $ascii ?: $value), '-'));
        return $slug . '-' . bin2hex(random_bytes(3));
    }
}
