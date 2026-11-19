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
            $where[] = "pc.category_id = :category_id";
            $params['category_id'] = $filters['category_id'];
        }

        if (!empty($filters['brand_id'])) {
            $where[] = "p.brand_id = :brand_id";
            $params['brand_id'] = $filters['brand_id'];
        }

        if (!empty($filters['keyword'])) {
            $where[] = "(p.name LIKE :kw OR b.name LIKE :kw)";
            $params['kw'] = '%' . $filters['keyword'] . '%';
        }

        $orderBy = "p.id DESC";
        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'price_asc':
                    $orderBy = "min_price ASC";
                    break;
                case 'price_desc':
                    $orderBy = "min_price DESC";
                    break;
                case 'name_asc':
                    $orderBy = "p.name ASC";
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
            $where[] = "pc.category_id = :category_id";
            $params['category_id'] = $filters['category_id'];
        }

        if (!empty($filters['brand_id'])) {
            $where[] = "p.brand_id = :brand_id";
            $params['brand_id'] = $filters['brand_id'];
        }

        if (!empty($filters['keyword'])) {
            $where[] = "(p.name LIKE :kw OR b.name LIKE :kw)";
            $params['kw'] = '%' . $filters['keyword'] . '%';
        }

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT COUNT(DISTINCT p.id)
                FROM products p
                LEFT JOIN brands b ON p.brand_id = b.id
                LEFT JOIN product_categories pc ON p.id = pc.product_id
                WHERE {$whereClause}";

        return (int)$this->fetchColumn($sql, $params);
    }

    /**
     * Lấy chi tiết sản phẩm kèm thông tin hãng, ảnh, biến thể
     */
    public function getDetail(int $id): ?array
    {
        $sql = "SELECT 
                    p.*,
                    b.name AS brand_name,
                    c.id AS category_id,
                    c.name AS category_name,
                    pi.image_url
                FROM products p
                LEFT JOIN brands b ON p.brand_id = b.id
                LEFT JOIN product_categories pc ON p.id = pc.product_id
                LEFT JOIN categories c ON pc.category_id = c.id
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                WHERE p.id = :id";

        $product = $this->fetch($sql, ['id' => $id]);
        if (!$product) {
            return null;
        }

        // Lấy danh sách ảnh phụ
        $sqlImages = "SELECT * FROM product_images WHERE product_id = :product_id ORDER BY is_primary DESC, sort_order ASC";
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
                    MAX(b.name) AS brand_name,
                    MAX(c.name) AS category_name,
                    MAX(pi.image_url) AS image_url,
                    MIN(pv.price) AS min_price,
                    MAX(pv.price) AS max_price,
                    SUM(pv.stock_quantity) AS total_stock,
                    COUNT(pv.id) AS variant_count
                FROM products p
                LEFT JOIN brands b ON p.brand_id = b.id
                LEFT JOIN product_categories pc ON p.id = pc.product_id
                LEFT JOIN categories c ON pc.category_id = c.id
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                LEFT JOIN product_variants pv ON p.id = pv.product_id
                GROUP BY p.id, p.brand_id, p.name, p.slug, p.description, p.status, p.created_at, p.updated_at
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

    public function updateProduct(int $id, array $data, ?int $categoryId = null, $imageUrls = null): bool
    {
        $this->update('products', $data, 'id = :id', ['id' => $id]);

        if ($categoryId !== null) {
            $this->delete('product_categories', 'product_id = :id', ['id' => $id]);
            $this->insert('product_categories', [
                'product_id' => $id,
                'category_id' => $categoryId
            ]);
        }

        if (is_string($imageUrls)) {
            $imageUrls = $imageUrls !== '' ? [$imageUrls] : [];
        }

        if (is_array($imageUrls)) {
            $this->delete('product_images', 'product_id = :id', ['id' => $id]);

            foreach (array_values(array_unique($imageUrls)) as $index => $imageUrl) {
                $this->insert('product_images', [
                    'product_id' => $id,
                    'image_url' => $imageUrl,
                    'alt_text' => $data['name'] ?? 'Giày',
                    'sort_order' => $index + 1,
                    'is_primary' => $index === 0 ? 1 : 0
                ]);
            }
        }

        return true;
    }

    public function deleteProduct(int $id): bool
    {
        $this->delete('product_categories', 'product_id = :id', ['id' => $id]);
        $this->delete('product_images', 'product_id = :id', ['id' => $id]);
        $this->delete('product_variants', 'product_id = :id', ['id' => $id]);
        return $this->delete('products', 'id = :id', ['id' => $id]);
    }
}
