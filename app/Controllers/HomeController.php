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

        // 8 sản phẩm mới nhất
        $featuredProducts = $productModel->getList([], 8);
        $categories = $categoryModel->getAllActive();
        $brands = $brandModel->getAllActive();

        $this->view('home', [
            'pageTitle'        => 'SoleVibe - Thế Giới Giày Sneaker Chính Hãng',
            'featuredProducts' => $featuredProducts,
            'categories'       => $categories,
            'brands'           => $brands,
        ]);
    }
}
