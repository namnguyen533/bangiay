<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductReview;
use App\Models\ProductVariant;
use App\Models\Order;

class ProductController extends BaseController
{
    public function index(): void
    {
        $productModel = new Product();
        $categoryModel = new Category();
        $brandModel = new Brand();
        $variantModel = new ProductVariant();

        $minPrice = null;
        $maxPrice = null;
        $priceRange = $_GET['price_range'] ?? '';
        if ($priceRange !== '') {
            $parts = explode('-', $priceRange);
            if (isset($parts[0]) && is_numeric($parts[0])) {
                $minPrice = (float)$parts[0];
            }
            if (isset($parts[1]) && is_numeric($parts[1]) && (float)$parts[1] > 0) {
                $maxPrice = (float)$parts[1];
            }
        } elseif (isset($_GET['min_price']) || isset($_GET['max_price'])) {
            if (isset($_GET['min_price']) && is_numeric($_GET['min_price'])) {
                $minPrice = (float)$_GET['min_price'];
            }
            if (isset($_GET['max_price']) && is_numeric($_GET['max_price']) && (float)$_GET['max_price'] > 0) {
                $maxPrice = (float)$_GET['max_price'];
            }
        }

        $filters = [
            'category_id' => isset($_GET['category_id']) && $_GET['category_id'] !== '' ? (int)$_GET['category_id'] : null,
            'brand_id'    => isset($_GET['brand_id']) && $_GET['brand_id'] !== '' ? (int)$_GET['brand_id'] : null,
            'keyword'     => trim($_GET['q'] ?? ''),
            'size'        => trim($_GET['size'] ?? ''),
            'min_price'   => $minPrice,
            'max_price'   => $maxPrice,
            'price_range' => $priceRange,
            'sort'        => $_GET['sort'] ?? 'newest',
        ];

        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        $products = $productModel->getList($filters, $perPage, $offset);
        $totalProducts = $productModel->countFiltered($filters);
        $totalPages = (int)ceil($totalProducts / $perPage);

        $categories = $categoryModel->getAllActive();
        $brands = $brandModel->getAllActive();
        $availableSizes = $variantModel->getAvailableSizes();

        $this->view('products', [
            'pageTitle'      => 'Danh Sách Giày - Apple Meo Meo',
            'products'       => $products,
            'categories'     => $categories,
            'brands'         => $brands,
            'availableSizes' => $availableSizes,
            'filters'        => $filters,
            'currentPage'    => $page,
            'totalPages'     => $totalPages,
            'totalProducts'  => $totalProducts,
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
        $reviewModel = new ProductReview();
        $reviews = $reviewModel->getVisibleForProduct($id);
        $ratingStats = $reviewModel->getRatingStats($id);

        $user = auth();
        $userReview = $user ? $reviewModel->findForUser($id, (int)$user['id']) : null;
        $hasPurchased = false;
        if ($user) {
            $hasPurchased = (new Order())->hasUserPurchasedProduct((int)$user['id'], $id);
        }

        $this->view('detail', [
            'pageTitle'       => $product['name'] . ' - Apple Meo Meo',
            'product'         => $product,
            'relatedProducts' => $relatedProducts,
            'reviews'         => $reviews,
            'ratingStats'     => $ratingStats,
            'userReview'      => $userReview,
            'hasPurchased'    => $hasPurchased,
        ]);
    }

    public function searchSuggest(): void
    {
        $keyword = trim($_GET['q'] ?? '');
        if (mb_strlen($keyword) < 1) {
            $this->json(['success' => true, 'brands' => [], 'products' => []]);
        }

        $productModel = new Product();
        $brandModel = new Brand();
        $results = $productModel->getSearchSuggestions($keyword, 6);
        $brands = $brandModel->getSearchSuggestions($keyword, 4);

        $this->json([
            'success' => true,
            'brands' => array_map(function($brand) {
                return [
                    'id'           => (int)$brand['id'],
                    'name'         => $brand['name'],
                    'product_count'=> (int)$brand['product_count'],
                    'url'          => route_url('products', ['brand_id' => $brand['id']]),
                ];
            }, $brands),
            'products' => array_map(function($p) {
                return [
                    'id'         => (int)$p['id'],
                    'name'       => $p['name'],
                    'brand_name' => $p['brand_name'] ?? '',
                    'sku'        => $p['matched_sku'] ?? '',
                    'image_url'  => image_src($p['image_url'] ?: 'uploads/products/no-product-image.svg'),
                    'price'      => format_price($p['min_sale_price'] ?: $p['min_price']),
                    'url'        => route_url('product-detail', ['id' => $p['id']]),
                ];
            }, $results)
        ]);
    }
}
