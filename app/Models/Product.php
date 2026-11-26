<?php

namespace App\Models;

use App\Core\BaseModel;

class Product extends BaseModel
{
    /**
     * Lấy danh sách sản phẩm hiển thị ra trang chủ / cửa hàng
     */
    public function getList(array $filters = [], int $limit = 12, int $offset = 0): array
    {
        $where = ["p.status = 'active'"];
        $params = [];

        if (!empty($filters['category_id'])) {
            $this->appendCategoryFilter($where, $params, (int)$filters['category_id']);
        }

        if (!empty($filters['brand_id'])) {
            $where[] = "p.brand_id = :brand_id";
            $params['brand_id'] = $filters['brand_id'];
        }

        if (!empty($filters['keyword'])) {
            $where[] = "(p.name LIKE :kw OR b.name LIKE :kw OR EXISTS (SELECT 1 FROM product_variants pv_sku WHERE pv_sku.product_id = p.id AND pv_sku.sku LIKE :kw_sku))";
            $params['kw'] = '%' . $filters['keyword'] . '%';
            $params['kw_sku'] = '%' . $filters['keyword'] . '%';
        }

        if (isset($filters['min_price']) && is_numeric($filters['min_price']) && (float)$filters['min_price'] > 0) {
            $where[] = "EXISTS (SELECT 1 FROM product_variants pv_min WHERE pv_min.product_id = p.id AND pv_min.status = 'active' AND COALESCE(pv_min.sale_price, pv_min.price) >= :min_price)";
            $params['min_price'] = (float)$filters['min_price'];
        }

        if (isset($filters['max_price']) && is_numeric($filters['max_price']) && (float)$filters['max_price'] > 0) {
            $where[] = "EXISTS (SELECT 1 FROM product_variants pv_max WHERE pv_max.product_id = p.id AND pv_max.status = 'active' AND COALESCE(pv_max.sale_price, pv_max.price) <= :max_price)";
            $params['max_price'] = (float)$filters['max_price'];
        }

        if (!empty($filters['size'])) {
            $where[] = "EXISTS (SELECT 1 FROM product_variants pv_sz WHERE pv_sz.product_id = p.id AND pv_sz.status = 'active' AND pv_sz.size = :filter_size)";
            $params['filter_size'] = $filters['size'];
        }

        $orderBy = "p.id DESC";
        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'price_asc':
                    $orderBy = "effective_price ASC, p.id DESC";
                    break;
                case 'price_desc':
                    $orderBy = "effective_price DESC, p.id DESC";
                    break;
                case 'name_asc':
                    $orderBy = "p.name ASC";
                    break;
                case 'popular':
                case 'bestseller':
                    $orderBy = "total_stock DESC, p.id DESC";
                    break;
                default:
                    $orderBy = "p.id DESC";
            }
        }

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT 
                    p.id, p.name, p.slug, p.description, p.brand_id,
                    MAX(b.name) AS brand_name,
                    MAX(c.name) AS category_name,
                    MAX(pi.image_url) AS image_url,
                    MIN(pv.price) AS min_price,
                    MIN(pv.sale_price) AS min_sale_price,
                    MIN(COALESCE(pv.sale_price, pv.price)) AS effective_price,
                    SUM(pv.stock_quantity) AS total_stock
                FROM products p
                LEFT JOIN brands b ON p.brand_id = b.id
                LEFT JOIN product_categories pc ON p.id = pc.product_id
                LEFT JOIN categories c ON pc.category_id = c.id
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                LEFT JOIN product_variants pv ON p.id = pv.product_id AND pv.status = 'active'
                WHERE {$whereClause}
                GROUP BY p.id, p.name, p.slug, p.description, p.brand_id
                ORDER BY {$orderBy}
                LIMIT {$limit} OFFSET {$offset}";

        return $this->fetchAll($sql, $params);
    }

    public function countFiltered(array $filters = []): int
    {
        $where = ["p.status = 'active'"];
        $params = [];

        if (!empty($filters['category_id'])) {
            $this->appendCategoryFilter($where, $params, (int)$filters['category_id']);
        }

        if (!empty($filters['brand_id'])) {
            $where[] = "p.brand_id = :brand_id";
            $params['brand_id'] = $filters['brand_id'];
        }

        if (!empty($filters['keyword'])) {
            $where[] = "(p.name LIKE :kw OR b.name LIKE :kw OR EXISTS (SELECT 1 FROM product_variants pv_sku WHERE pv_sku.product_id = p.id AND pv_sku.sku LIKE :kw_sku))";
            $params['kw'] = '%' . $filters['keyword'] . '%';
            $params['kw_sku'] = '%' . $filters['keyword'] . '%';
        }

        if (isset($filters['min_price']) && is_numeric($filters['min_price']) && (float)$filters['min_price'] > 0) {
            $where[] = "EXISTS (SELECT 1 FROM product_variants pv_min WHERE pv_min.product_id = p.id AND pv_min.status = 'active' AND COALESCE(pv_min.sale_price, pv_min.price) >= :min_price)";
            $params['min_price'] = (float)$filters['min_price'];
        }

        if (isset($filters['max_price']) && is_numeric($filters['max_price']) && (float)$filters['max_price'] > 0) {
            $where[] = "EXISTS (SELECT 1 FROM product_variants pv_max WHERE pv_max.product_id = p.id AND pv_max.status = 'active' AND COALESCE(pv_max.sale_price, pv_max.price) <= :max_price)";
            $params['max_price'] = (float)$filters['max_price'];
        }

        if (!empty($filters['size'])) {
            $where[] = "EXISTS (SELECT 1 FROM product_variants pv_sz WHERE pv_sz.product_id = p.id AND pv_sz.status = 'active' AND pv_sz.size = :filter_size)";
            $params['filter_size'] = $filters['size'];
        }

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT COUNT(DISTINCT p.id)
                FROM products p
                LEFT JOIN brands b ON p.brand_id = b.id
                LEFT JOIN product_categories pc ON p.id = pc.product_id
                WHERE {$whereClause}";

        return (int)$this->fetchColumn($sql, $params);
    }

    public function getSearchSuggestions(string $keyword, int $limit = 6): array
    {
        $sql = "SELECT p.id, p.name, p.slug, b.name AS brand_name,
                       (SELECT pi.image_url FROM product_images pi WHERE pi.product_id = p.id AND pi.is_primary = 1 ORDER BY pi.sort_order, pi.id LIMIT 1) AS image_url,
                       (SELECT pv.sku FROM product_variants pv WHERE pv.product_id = p.id AND pv.sku LIKE :kw_sku LIMIT 1) AS matched_sku,
                       (SELECT MIN(pv.price) FROM product_variants pv WHERE pv.product_id = p.id AND pv.status = 'active') AS min_price,
                       (SELECT MIN(pv.sale_price) FROM product_variants pv WHERE pv.product_id = p.id AND pv.status = 'active') AS min_sale_price
                FROM products p
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE p.status = 'active'
                  AND (p.name LIKE :kw
                       OR b.name LIKE :kw
                       OR EXISTS (SELECT 1 FROM product_variants pv2 WHERE pv2.product_id = p.id AND pv2.sku LIKE :kw_sku2))
                ORDER BY p.id DESC
                LIMIT {$limit}";

        return $this->fetchAll($sql, [
            'kw' => '%' . $keyword . '%',
            'kw_sku' => '%' . $keyword . '%',
            'kw_sku2' => '%' . $keyword . '%'
        ]);
    }

    /**
     * Lấy chi tiết sản phẩm kèm thông tin hãng, ảnh, biến thể
     */
    public function getDetail(int $id): ?array
    {
        $sql = "SELECT p.*, b.name AS brand_name,
                    (SELECT c.id FROM product_categories pc
                     JOIN categories c ON c.id = pc.category_id
                     WHERE pc.product_id = p.id ORDER BY c.name LIMIT 1) AS category_id,
                    (SELECT c.name FROM product_categories pc
                     JOIN categories c ON c.id = pc.category_id
                     WHERE pc.product_id = p.id ORDER BY c.name LIMIT 1) AS category_name,
                    (SELECT pi.image_url FROM product_images pi
                     WHERE pi.product_id = p.id AND pi.is_primary = 1 ORDER BY pi.sort_order, pi.id LIMIT 1) AS image_url,
                    (SELECT pi.alt_text FROM product_images pi
                     WHERE pi.product_id = p.id AND pi.is_primary = 1 ORDER BY pi.sort_order, pi.id LIMIT 1) AS image_alt_text
                FROM products p
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE p.id = :id";

        $product = $this->fetch($sql, ['id' => $id]);
        if (!$product) {
            return null;
        }

        // Lấy danh sách ảnh phụ
        $sqlImages = "SELECT * FROM product_images WHERE product_id = :product_id ORDER BY is_primary DESC, sort_order ASC, id ASC";
        $product['images'] = $this->fetchAll($sqlImages, ['product_id' => $id]);

        // Lấy các biến thể (size, color, price, sale_price, stock)
        $sqlVariants = "SELECT * FROM product_variants WHERE product_id = :product_id AND status = 'active' ORDER BY size ASC";
        $product['variants'] = $this->fetchAll($sqlVariants, ['product_id' => $id]);

        return $product;
    }

    /**
     * Sản phẩm liên quan cùng danh mục
     */
    public function getRelatedProducts(int $productId, ?int $categoryId, int $limit = 4): array
    {
        if (!$categoryId) {
            return [];
        }

        $sql = "SELECT 
                    p.id, p.name, p.slug,
                    MAX(b.name) AS brand_name,
                    MAX(pi.image_url) AS image_url,
                    MIN(pv.price) AS min_price,
                    MIN(pv.sale_price) AS min_sale_price
                FROM products p
                LEFT JOIN brands b ON p.brand_id = b.id
                JOIN product_categories pc ON p.id = pc.product_id AND pc.category_id = :cat_id
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                LEFT JOIN product_variants pv ON p.id = pv.product_id AND pv.status = 'active'
                WHERE p.id != :prod_id AND p.status = 'active'
                GROUP BY p.id, p.name, p.slug
                LIMIT {$limit}";

        return $this->fetchAll($sql, ['cat_id' => $categoryId, 'prod_id' => $productId]);
    }

    /**
     * Quản trị: Lấy tất cả sản phẩm
     */
    public function getAllForAdmin(): array
    {
        $sql = "SELECT 
                    p.id, p.brand_id, p.name, p.slug, p.description, p.status, p.created_at, p.updated_at,
                    b.name AS brand_name,
                    (SELECT GROUP_CONCAT(DISTINCT c.name ORDER BY c.name SEPARATOR ', ')
                     FROM product_categories pc JOIN categories c ON c.id = pc.category_id
                     WHERE pc.product_id = p.id) AS category_name,
                    (SELECT pi.image_url FROM product_images pi
                     WHERE pi.product_id = p.id AND pi.is_primary = 1 ORDER BY pi.sort_order, pi.id LIMIT 1) AS image_url,
                    COALESCE((SELECT MIN(pv.price) FROM product_variants pv WHERE pv.product_id = p.id AND pv.status = 'active'), 0) AS min_price,
                    COALESCE((SELECT MAX(pv.price) FROM product_variants pv WHERE pv.product_id = p.id AND pv.status = 'active'), 0) AS max_price,
                    COALESCE((SELECT SUM(pv.stock_quantity) FROM product_variants pv WHERE pv.product_id = p.id AND pv.status = 'active'), 0) AS total_stock,
                    (SELECT COUNT(*) FROM product_variants pv WHERE pv.product_id = p.id AND pv.status = 'active') AS variant_count
                FROM products p
                LEFT JOIN brands b ON p.brand_id = b.id
                ORDER BY p.id DESC";

        return $this->fetchAll($sql);
    }

    public function createProduct(array $data, ?int $categoryId = null, $imageUrls = null): int
    {
        $productId = $this->insert('products', $data);

        if ($categoryId) {
            $this->insert('product_categories', [
                'product_id' => $productId,
                'category_id' => $categoryId
            ]);
        }

        if (is_string($imageUrls)) {
            $imageUrls = $imageUrls !== '' ? [$imageUrls] : [];
        }

        if (!is_array($imageUrls) || empty($imageUrls)) {
            $imageUrls = ['https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600'];
        }

        foreach (array_values(array_unique($imageUrls)) as $index => $imageUrl) {
            $this->insert('product_images', [
                'product_id' => $productId,
                'image_url' => $imageUrl,
                'alt_text' => $data['name'],
                'sort_order' => $index + 1,
                'is_primary' => $index === 0 ? 1 : 0
            ]);
        }

        return $productId;
    }

    public function addImages(int $productId, array $imagePaths): void
    {
        foreach ($imagePaths as $path) {
            $this->insert('product_images', [
                'product_id' => $productId,
                'image_url' => $path,
                'alt_text' => null,
                'sort_order' => 0,
                'is_primary' => 0,
            ]);
        }

        if ($imagePaths) {
            $this->query(
                'UPDATE product_images SET is_primary = 0 WHERE product_id = :product_id',
                ['product_id' => $productId]
            );
            $this->query(
                'UPDATE product_images SET is_primary = 1
                 WHERE product_id = :product_id ORDER BY id DESC LIMIT 1',
                ['product_id' => $productId]
            );
        }
    }

    public function removeImage(int $productId, int $imageId): ?string
    {
        $image = $this->fetch(
            'SELECT image_url FROM product_images WHERE id = :id AND product_id = :product_id',
            ['id' => $imageId, 'product_id' => $productId]
        );
        if (!$image) {
            return null;
        }

        $this->delete('product_images', 'id = :id AND product_id = :product_id', [
            'id' => $imageId,
            'product_id' => $productId,
        ]);
        $this->query(
            'UPDATE product_images SET is_primary = 1
             WHERE product_id = :product_id ORDER BY sort_order, id LIMIT 1',
            ['product_id' => $productId]
        );

        return $image['image_url'];
    }

    public function updateProduct(int $id, array $data, ?int $categoryId = null): bool
    {
        $this->update('products', $data, 'id = :id', ['id' => $id]);

        $this->delete('product_categories', 'product_id = :id', ['id' => $id]);
        if ($categoryId !== null) {
            $this->insert('product_categories', [
                'product_id' => $id,
                'category_id' => $categoryId
            ]);
        }

        return true;
    }

    public function deleteProduct(int $id): bool
    {
        $this->update('product_variants', ['status' => 'inactive'], 'product_id = :id', ['id' => $id]);
        return $this->update('products', ['status' => 'inactive'], 'id = :id', ['id' => $id]);
    }

    private function appendCategoryFilter(array &$where, array &$params, int $categoryId): void
    {
        $categoryIds = (new Category())->getDescendantIds($categoryId, true);
        $placeholders = [];
        foreach ($categoryIds as $index => $id) {
            $key = 'category_' . $index;
            $placeholders[] = ':' . $key;
            $params[$key] = $id;
        }
        if ($placeholders) {
            $where[] = 'pc.category_id IN (' . implode(', ', $placeholders) . ')';
        }
    }
}
