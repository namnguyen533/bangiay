<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;

class ProductController extends BaseController
{
    public function index(): void
    {
        $productModel = new Product();
        $categoryModel = new Category();
        $brandModel = new Brand();

        $filters = [
            'category_id' => isset($_GET['category_id']) && $_GET['category_id'] !== '' ? (int)$_GET['category_id'] : null,
            'brand_id'    => isset($_GET['brand_id']) && $_GET['brand_id'] !== '' ? (int)$_GET['brand_id'] : null,
            'keyword'     => trim($_GET['q'] ?? ''),
            'sort'        => $_GET['sort'] ?? 'newest',
        ];

        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        $products = $productModel->getList($filters, $perPage, $offset);
        $totalProducts = $productModel->countFiltered($filters);
        $totalPages = ceil($totalProducts / $perPage);

        $categories = $categoryModel->getAllActive();
        $brands = $brandModel->getAllActive();

        $this->view('products', [
            'pageTitle'     => 'Danh Sách Giày - SoleVibe',
            'products'      => $products,
            'categories'    => $categories,
            'brands'        => $brands,
            'filters'       => $filters,
            'currentPage'   => $page,
            'totalPages'    => $totalPages,
            'totalProducts' => $totalProducts,
        ]);
    }

    public function detail(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            $this->redirect(route_url('products'));
        }

        $productModel = new Product();
        $product = $productModel->getDetail($id);

        if (!$product) {
            set_flash('error', 'Sản phẩm giày không tồn tại!', 'danger');
            $this->redirect(route_url('products'));
        }

        $relatedProducts = $productModel->getRelatedProducts($id, $product['category_id'] ?? null, 4);

        $this->view('detail', [
            'pageTitle'       => $product['name'] . ' - SoleVibe',
            'product'         => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }
}
