<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;

class HomeController extends BaseController
{
    public function index(): void
    {
        $productModel = new Product();
        $categoryModel = new Category();
        $brandModel = new Brand();

        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 8;
        $offset = ($page - 1) * $perPage;

        $featuredProducts = $productModel->getList([], $perPage, $offset);
        $totalProducts = $productModel->countFiltered([]);
        $totalPages = (int)ceil($totalProducts / $perPage);

        $categories = $categoryModel->getAllActive();
        $brands = $brandModel->getAllActive();

        $this->view('home', [
            'pageTitle'        => 'Apple Meo Meo - Thế Giới Giày Sneaker Chính Hãng',
            'featuredProducts' => $featuredProducts,
            'categories'       => $categories,
            'brands'           => $brands,
            'currentPage'      => $page,
            'totalPages'       => $totalPages,
            'totalProducts'    => $totalProducts,
        ]);
    }
}
