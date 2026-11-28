<?php

namespace App\Controllers\Admin;

use App\Core\BaseController;
use App\Core\DescriptionSanitizer;
use App\Core\ImageUploader;
use App\Core\ProductVideoUploader;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use InvalidArgumentException;
use PDOException;
use RuntimeException;

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
        $this->requireAdmin();
        $this->adminView('products/index', [
            'pageTitle' => 'Quản Lý Sản Phẩm Giày',
            'products' => $this->productModel->getAllForAdmin(),
            'lowStock' => $this->variantModel->getLowStock(5),
        ]);
    }

    public function create(): void
    {
        $this->requireAdmin();
        $this->adminView('products/create', [
            'pageTitle' => 'Thêm Mới Sản Phẩm Giày',
            'categories' => $this->categoryModel->getFlatList(true),
            'brands' => $this->brandModel->getAllActive(),
        ]);
    }

    public function store(): void
    {
        $this->requireAdmin();
        $name = trim($_POST['name'] ?? '');
        $brandId = $this->optionalId($_POST['brand_id'] ?? null);
        $categoryId = $this->optionalId($_POST['category_id'] ?? null);
        $description = $this->submittedDescription('admin-product-create');
        $imageUrl = $this->validatedImageUrl(trim($_POST['image_url'] ?? ''), 'admin-product-create');
        $imageUrls = $this->parseImageUrls($_POST['image_urls'] ?? '', 'admin-product-create');
        if ($name === '' || mb_strlen($name) > 200) {
            $this->fail('Tên sản phẩm không được trống và tối đa 200 ký tự.', 'admin-product-create');
        }
        if ($brandId !== null && !$this->brandModel->findById($brandId)) {
            $this->fail('Thương hiệu không tồn tại.', 'admin-product-create');
        }
        if ($categoryId !== null && !$this->categoryModel->findById($categoryId)) {
            $this->fail('Danh mục không tồn tại.', 'admin-product-create');
        }

        $uploadedImages = [];
        $uploadedVideo = null;
        try {
            $uploadedImages = ImageUploader::storeMany($_FILES['images'] ?? []);
            $uploadedVideo = ProductVideoUploader::store($_FILES['rotation_video'] ?? []);
            $this->productModel->beginTransaction();
            $slug = $this->makeSlug($name);
            $productId = $this->productModel->createProduct([
                'brand_id' => $brandId,
                'name' => $name,
                'slug' => $slug,
                'description' => $description,
                'status' => 'active',
            ], $categoryId, array_merge($imageUrl !== '' ? [$imageUrl] : [], $imageUrls));
            $this->productModel->addImages($productId, $uploadedImages);
            if ($uploadedVideo !== null) {
                $this->productModel->replaceRotationVideo($productId, $uploadedVideo);
            }
            $this->saveVariants($productId, $this->submittedVariants());
            $this->productModel->commit();
        } catch (InvalidArgumentException | RuntimeException $exception) {
            $this->rollbackIfActive();
            $this->deleteUploadedFiles($uploadedImages);
            if ($uploadedVideo !== null) {
                ProductVideoUploader::delete($uploadedVideo);
            }
            $this->fail($exception->getMessage(), 'admin-product-create');
        } catch (PDOException $exception) {
            $this->rollbackIfActive();
            $this->deleteUploadedFiles($uploadedImages);
            if ($uploadedVideo !== null) {
                ProductVideoUploader::delete($uploadedVideo);
            }
            error_log('Product creation failed: ' . $exception->getMessage());
            $this->fail('Không thể lưu sản phẩm. Kiểm tra mã SKU và cấu trúc cơ sở dữ liệu rồi thử lại.', 'admin-product-create');
        }

        set_flash('success', 'Đã thêm sản phẩm cùng các biến thể.', 'success');
        $this->redirect(route_url('admin-products'));
    }

    public function edit(): void
    {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        $product = $this->productModel->getDetail($id);
        if (!$product) {
            set_flash('error', 'Không tìm thấy sản phẩm.', 'danger');
            $this->redirect(route_url('admin-products'));
        }
        $product['description'] = DescriptionSanitizer::sanitize((string)($product['description'] ?? ''));

        $this->adminView('products/edit', [
            'pageTitle' => 'Chỉnh Sửa Sản Phẩm: ' . $product['name'],
            'product' => $product,
            'variants' => $this->variantModel->getAllByProductId($id),
            'categories' => $this->categoryModel->getFlatList(),
            'brands' => $this->brandModel->getAll(),
        ]);
    }

    public function update(): void
    {
        $this->requireAdmin();
        $id = (int)($_POST['id'] ?? 0);
        $product = $this->productModel->getDetail($id);
        if (!$product) {
            $this->fail('Không tìm thấy sản phẩm.', 'admin-products');
        }

        $name = trim($_POST['name'] ?? '');
        $brandId = $this->optionalId($_POST['brand_id'] ?? null);
        $categoryId = $this->optionalId($_POST['category_id'] ?? null);
        $description = $this->submittedDescription('admin-product-edit', ['id' => $id]);
        $imageUrl = $this->validatedImageUrl(trim($_POST['image_url'] ?? ''), 'admin-product-edit', ['id' => $id]);
        $imageUrls = $this->parseImageUrls($_POST['image_urls'] ?? '', 'admin-product-edit', ['id' => $id]);
        $status = $_POST['status'] ?? '';
        if ($name === '' || mb_strlen($name) > 200 || !in_array($status, ['active', 'inactive'], true)) {
            $this->fail('Thông tin sản phẩm không hợp lệ.', 'admin-product-edit', ['id' => $id]);
        }
        if ($brandId !== null && !$this->brandModel->findById($brandId)) {
            $this->fail('Thương hiệu không tồn tại.', 'admin-product-edit', ['id' => $id]);
        }
        if ($categoryId !== null && !$this->categoryModel->findById($categoryId)) {
            $this->fail('Danh mục không tồn tại.', 'admin-product-edit', ['id' => $id]);
        }

        $uploadedImages = [];
        $uploadedVideo = null;
        $removedImagePaths = [];
        $removedVideoPath = null;
        try {
            $uploadedImages = ImageUploader::storeMany($_FILES['images'] ?? []);
            $uploadedVideo = ProductVideoUploader::store($_FILES['rotation_video'] ?? []);
            $this->productModel->beginTransaction();
            $this->productModel->updateProduct($id, [
                'brand_id' => $brandId,
                'name' => $name,
                'description' => $description,
                'status' => $status,
            ], $categoryId);

            foreach (array_unique(array_map('intval', (array)($_POST['remove_image_ids'] ?? []))) as $imageId) {
                if ($imageId > 0) {
                    $path = $this->productModel->removeImage($id, $imageId);
                    if ($path !== null) {
                        $removedImagePaths[] = $path;
                    }
                }
            }
            $newImageUrls = array_merge($imageUrl !== '' ? [$imageUrl] : [], $imageUrls);
            if ($newImageUrls) {
                $this->productModel->addImages($id, $newImageUrls);
            }
            $this->productModel->addImages($id, $uploadedImages);
            if ($uploadedVideo !== null) {
                $removedVideoPath = $this->productModel->replaceRotationVideo($id, $uploadedVideo);
            } elseif (!empty($_POST['remove_rotation_video'])) {
                $removedVideoPath = $this->productModel->removeRotationVideo($id);
            }
            $this->saveVariants($id, $this->submittedVariants());
            $this->productModel->commit();
        } catch (InvalidArgumentException | RuntimeException $exception) {
            $this->rollbackIfActive();
            $this->deleteUploadedFiles($uploadedImages);
            if ($uploadedVideo !== null) {
                ProductVideoUploader::delete($uploadedVideo);
            }
            $this->fail($exception->getMessage(), 'admin-product-edit', ['id' => $id]);
        } catch (PDOException $exception) {
            $this->rollbackIfActive();
            $this->deleteUploadedFiles($uploadedImages);
            if ($uploadedVideo !== null) {
                ProductVideoUploader::delete($uploadedVideo);
            }
            error_log('Product update failed: ' . $exception->getMessage());
            $this->fail('Không thể cập nhật sản phẩm. Kiểm tra SKU và dữ liệu rồi thử lại.', 'admin-product-edit', ['id' => $id]);
        }

        foreach ($removedImagePaths as $path) {
            ImageUploader::delete($path);
        }
        if ($removedVideoPath !== null) {
            ProductVideoUploader::delete($removedVideoPath);
        }
        set_flash('success', 'Đã cập nhật thông tin, ảnh/video và biến thể sản phẩm.', 'success');
        $this->redirect(route_url('admin-products'));
    }

    public function delete(): void
    {
        $this->requireAdmin();
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->productModel->deleteProduct($id);
            set_flash('success', 'Đã ẩn sản phẩm khỏi cửa hàng; lịch sử kho và đơn hàng được giữ lại.', 'info');
        }
        $this->redirect(route_url('admin-products'));
    }

    private function parseImageUrls(string $text, string $route, array $params = []): array
    {
        $urls = [];

        foreach (preg_split('/\r\n|\r|\n/', $text) ?: [] as $url) {
            $url = trim($url);
            if ($url !== '') {
                $urls[] = $this->validatedImageUrl($url, $route, $params);
            }
        }

        return array_values(array_unique($urls));
    }

    private function submittedDescription(string $route, array $params = []): string
    {
        $description = $_POST['description'] ?? '';
        if (!is_string($description)) {
            $this->fail('Nội dung mô tả không hợp lệ.', $route, $params);
        }

        return DescriptionSanitizer::sanitize($description);
    }

    private function submittedVariants(): array
    {
        $rows = $_POST['variants'] ?? null;
        if (!is_array($rows) || !$rows) {
            throw new InvalidArgumentException('Cần khai báo ít nhất một biến thể.');
        }
        return array_values($rows);
    }

    private function saveVariants(int $productId, array $rows): void
    {
        $existing = [];
        foreach ($this->variantModel->getAllByProductId($productId) as $variant) {
            $existing[(int)$variant['id']] = $variant;
        }

        $submittedIds = [];
        $uniquePairs = [];
        $skus = [];
        foreach ($rows as $row) {
            if (!is_array($row)) {
                throw new InvalidArgumentException('Dữ liệu biến thể không hợp lệ.');
            }
            $size = trim((string)($row['size'] ?? ''));
            $color = trim((string)($row['color'] ?? ''));
            $sku = trim((string)($row['sku'] ?? ''));
            $price = filter_var($row['price'] ?? null, FILTER_VALIDATE_FLOAT);
            $saleInput = trim((string)($row['sale_price'] ?? ''));
            $salePrice = $saleInput === '' ? null : filter_var($saleInput, FILTER_VALIDATE_FLOAT);
            $status = $row['status'] ?? 'active';
            $variantId = (int)($row['id'] ?? 0);

            if ($size === '' || mb_strlen($size) > 20 || $color === '' || mb_strlen($color) > 50 ||
                $price === false || $price <= 0 || ($saleInput !== '' && ($salePrice === false || $salePrice <= 0 || $salePrice > $price)) ||
                !in_array($status, ['active', 'inactive'], true)) {
                throw new InvalidArgumentException('Mỗi biến thể cần có size, màu sắc và giá hợp lệ; giá khuyến mãi không được lớn hơn giá gốc.');
            }
            if ($variantId > 0 && !isset($existing[$variantId])) {
                throw new InvalidArgumentException('Có biến thể không thuộc sản phẩm này.');
            }

            $pair = mb_strtolower($size . '|' . $color);
            if (isset($uniquePairs[$pair])) {
                throw new InvalidArgumentException('Không thể lặp lại cùng một cặp size và màu sắc trong một sản phẩm.');
            }
            $uniquePairs[$pair] = true;

            if ($sku === '') {
                $sku = $this->makeSku($productId, $size, $color);
            }
            if (mb_strlen($sku) > 80 || isset($skus[$sku])) {
                throw new InvalidArgumentException('Mã SKU không hợp lệ hoặc bị trùng trong danh sách biến thể.');
            }
            $skus[$sku] = true;

            $data = [
                'sku' => $sku,
                'size' => $size,
                'color' => $color,
                'price' => $price,
                'sale_price' => $salePrice,
                'status' => $status,
            ];
            if ($variantId > 0) {
                $this->variantModel->updateVariant($variantId, $data);
                $submittedIds[$variantId] = true;
            } else {
                $this->variantModel->create($data + [
                    'product_id' => $productId,
                    'stock_quantity' => 0,
                ]);
            }
        }

        foreach ($existing as $id => $variant) {
            if (!isset($submittedIds[$id])) {
                $this->variantModel->updateVariant($id, ['status' => 'inactive']);
            }
        }
    }

    private function makeSku(int $productId, string $size, string $color): string
    {
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $color);
        $colorCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $ascii ?: $color), 0, 20));
        return 'SV-' . $productId . '-' . strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $size)) . '-' . ($colorCode ?: 'COLOR') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }

    private function makeSlug(string $name): string
    {
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name);
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $ascii ?: $name), '-'));
        return ($slug ?: 'product') . '-' . bin2hex(random_bytes(4));
    }

    private function optionalId($value): ?int
    {
        return $value !== null && $value !== '' ? max(0, (int)$value) : null;
    }

    private function validatedImageUrl(string $url, string $route, array $params = []): string
    {
        if ($url === '') {
            return '';
        }
        if (filter_var($url, FILTER_VALIDATE_URL) === false || !in_array(strtolower((string)parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true)) {
            $this->fail('Đường dẫn ảnh phải là URL http hoặc https hợp lệ.', $route, $params);
        }
        return $url;
    }

    private function rollbackIfActive(): void
    {
        if ($this->productModel->inTransaction()) {
            $this->productModel->rollBack();
        }
    }

    private function deleteUploadedFiles(array $paths): void
    {
        foreach ($paths as $path) {
            ImageUploader::delete($path);
        }
    }

    private function fail(string $message, string $route, array $params = []): void
    {
        set_flash('error', $message, 'danger');
        $this->redirect(route_url($route, $params));
    }
}
