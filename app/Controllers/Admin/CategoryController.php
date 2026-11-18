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
        $this->requireAdmin();
        $categories = $this->categoryModel->getFlatList();

        $this->adminView('categories/index', [
            'pageTitle'  => 'Quản Lý Danh Mục Giày',
            'categories' => $categories,
            'parentCategories' => $this->categoryModel->getFlatList(true),
        ]);
    }

    public function store(): void
    {
        $this->requireAdmin();
        $name = trim($_POST['name'] ?? '');
        if ($name === '' || mb_strlen($name) > 120) {
            set_flash('error', 'Tên danh mục không được trống và tối đa 120 ký tự.', 'danger');
            $this->redirect(route_url('admin-categories'));
        }

        $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
        if ($parentId !== null && !$this->categoryModel->findById($parentId)) {
            set_flash('error', 'Danh mục cha không tồn tại.', 'danger');
            $this->redirect(route_url('admin-categories'));
        }

        $this->categoryModel->create([
            'parent_id' => $parentId,
            'name'   => $name,
            'slug'   => $this->slugify($name),
            'status' => 'active',
        ]);

        set_flash('success', 'Thêm danh mục mới thành công!', 'success');
        $this->redirect(route_url('admin-categories'));
    }

    public function delete(): void
    {
        $this->requireAdmin();
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->categoryModel->deleteCategory($id);
            set_flash('success', 'Đã xóa danh mục thành công!', 'info');
        }

        $this->redirect(route_url('admin-categories'));
    }

    public function edit(): void
    {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        $category = $this->categoryModel->findById($id);
        if (!$category) {
            set_flash('error', 'Không tìm thấy danh mục.', 'danger');
            $this->redirect(route_url('admin-categories'));
        }

        $this->adminView('categories/edit', [
            'pageTitle' => 'Chỉnh Sửa Danh Mục',
            'category' => $category,
            'parentCategories' => array_values(array_filter(
                $this->categoryModel->getFlatList(),
                fn(array $item): bool => !$this->categoryModel->isDescendant($id, (int)$item['id'])
            )),
        ]);
    }

    public function update(): void
    {
        $this->requireAdmin();
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
        $category = $this->categoryModel->findById($id);

        if (!$category || $name === '' || mb_strlen($name) > 120) {
            set_flash('error', 'Danh mục không hợp lệ hoặc tên đang để trống.', 'danger');
            $this->redirect(route_url('admin-categories'));
        }
        if ($parentId === $id || ($parentId !== null && $this->categoryModel->isDescendant($id, $parentId))) {
            set_flash('error', 'Không thể chọn chính danh mục này hoặc danh mục con làm danh mục cha.', 'danger');
            $this->redirect(route_url('admin-category-edit', ['id' => $id]));
        }
        if ($parentId !== null && !$this->categoryModel->findById($parentId)) {
            set_flash('error', 'Danh mục cha không tồn tại.', 'danger');
            $this->redirect(route_url('admin-category-edit', ['id' => $id]));
        }

        $this->categoryModel->updateCategory($id, [
            'parent_id' => $parentId,
            'name' => $name,
            'slug' => $this->slugify($name),
            'status' => in_array($_POST['status'] ?? '', ['active', 'inactive'], true) ? $_POST['status'] : 'active',
        ]);
        set_flash('success', 'Đã cập nhật danh mục.', 'success');
        $this->redirect(route_url('admin-categories'));
    }

    private function slugify(string $value): string
    {
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $ascii ?: $value), '-'));
        return $slug . '-' . bin2hex(random_bytes(3));
    }
}
