<?php

namespace App\Controllers\Admin;

use App\Core\BaseController;
use App\Models\Category;

class CategoryController extends BaseController
{
    private Category $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new Category();
    }

    public function index(): void
    {
        $categories = $this->categoryModel->getAllActive();

        $this->adminView('categories/index', [
            'pageTitle'  => 'Quản Lý Danh Mục Giày',
            'categories' => $categories,
        ]);
    }

    public function store(): void
    {
        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            set_flash('error', 'Tên danh mục không được để trống!', 'danger');
            $this->redirect(route_url('admin-categories'));
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));

        $this->categoryModel->create([
            'name'   => $name,
            'slug'   => $slug,
            'status' => 'active',
        ]);

        set_flash('success', 'Thêm danh mục mới thành công!', 'success');
        $this->redirect(route_url('admin-categories'));
    }

    public function delete(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $this->categoryModel->deleteCategory($id);
            set_flash('success', 'Đã xóa danh mục thành công!', 'info');
        }

        $this->redirect(route_url('admin-categories'));
    }
}
