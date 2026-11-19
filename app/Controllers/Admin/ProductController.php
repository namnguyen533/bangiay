<?php

namespace App\Controllers\Admin;

use App\Core\BaseController;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductVariant;

class ProductController extends BaseController
{
    private Product $productModel;
    private Category $categoryModel;
    private Brand $brandModel;
    private ProductVariant $variantModel;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->brandModel = new Brand();
        $this->variantModel = new ProductVariant();
    }

    public function index(): void
    {
        $products = $this->productModel->getAllForAdmin();

        $this->adminView('products/index', [
            'pageTitle' => 'Quản Lý Sản Phẩm Giày',
            'products'  => $products,
        ]);
    }

    public function create(): void
    {
        $categories = $this->categoryModel->getAllActive();
        $brands = $this->brandModel->getAllActive();

        $this->adminView('products/create', [
            'pageTitle'  => 'Thêm Mới Sản Phẩm Giày',
            'categories' => $categories,
            'brands'     => $brands,
        ]);
    }

    public function store(): void
    {
        $name = trim($_POST['name'] ?? '');
        $brandId = !empty($_POST['brand_id']) ? (int)$_POST['brand_id'] : null;
        $categoryId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
        $description = trim($_POST['description'] ?? '');
        $imageUrl = trim($_POST['image_url'] ?? '');
        $imageUrls = $this->parseImageUrls($_POST['image_urls'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $salePrice = !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null;
        $stock = max(0, (int)($_POST['stock_quantity'] ?? 10));
        $sizes = $_POST['sizes'] ?? ['39', '40', '41', '42'];
        $color = trim($_POST['color'] ?? 'Tiêu chuẩn');

        if (empty($name) || $price <= 0) {
            set_flash('error', 'Tên sản phẩm và giá tiền không được để trống!', 'danger');
            $this->redirect(route_url('admin-product-create'));
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-')) . '-' . time();

        $productId = $this->productModel->createProduct([
            'brand_id'    => $brandId,
            'name'        => $name,
            'slug'        => $slug,
            'description' => $description,
            'status'      => 'active',
        ], $categoryId, array_merge(
            $imageUrl ? [$imageUrl] : [],
            $imageUrls
        ));

        // Tạo các biến thể kích thước giày
        foreach ($sizes as $size) {
            $sku = strtoupper(substr($slug, 0, 4)) . '-' . $size . '-' . rand(100, 999);
            $this->variantModel->create([
                'product_id'     => $productId,
                'sku'            => $sku,
                'size'           => $size,
                'color'          => $color,
                'price'          => $price,
                'sale_price'     => $salePrice,
                'stock_quantity' => $stock,
                'status'         => 'active',
            ]);
        }

        set_flash('success', 'Đã thêm sản phẩm giày mới thành công!', 'success');
        $this->redirect(route_url('admin-products'));
    }

    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $product = $this->productModel->getDetail($id);

        if (!$product) {
            set_flash('error', 'Không tìm thấy sản phẩm!', 'danger');
            $this->redirect(route_url('admin-products'));
        }

        $categories = $this->categoryModel->getAllActive();
        $brands = $this->brandModel->getAllActive();

        $this->adminView('products/edit', [
            'pageTitle'  => 'Chỉnh Sửa Sản Phẩm: ' . $product['name'],
            'product'    => $product,
            'categories' => $categories,
            'brands'     => $brands,
        ]);
    }

    public function update(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $brandId = !empty($_POST['brand_id']) ? (int)$_POST['brand_id'] : null;
        $categoryId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
        $description = trim($_POST['description'] ?? '');
        $imageUrl = trim($_POST['image_url'] ?? '');
        $imageUrls = $this->parseImageUrls($_POST['image_urls'] ?? '');
        $status = $_POST['status'] ?? 'active';

        if (empty($name)) {
            set_flash('error', 'Tên sản phẩm không được để trống!', 'danger');
            $this->redirect(route_url('admin-product-edit', ['id' => $id]));
        }

        $this->productModel->updateProduct($id, [
            'brand_id'    => $brandId,
            'name'        => $name,
            'description' => $description,
            'status'      => $status,
        ], $categoryId, array_merge(
            $imageUrl ? [$imageUrl] : [],
            $imageUrls
        ));

        set_flash('success', 'Đã cập nhật sản phẩm giày thành công!', 'success');
        $this->redirect(route_url('admin-products'));
    }

    public function delete(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $this->productModel->deleteProduct($id);
            set_flash('success', 'Đã xóa sản phẩm thành công!', 'info');
        }

        $this->redirect(route_url('admin-products'));
    }

    private function parseImageUrls(string $text): array
    {
        $urls = [];

        foreach (preg_split('/\r\n|\r|\n/', $text) ?: [] as $url) {
            $url = trim($url);
            if ($url !== '' && filter_var($url, FILTER_VALIDATE_URL)) {
                $urls[] = $url;
            }
        }

        return array_values(array_unique($urls));
    }
}
