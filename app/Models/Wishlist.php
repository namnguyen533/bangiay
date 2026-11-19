<?php

namespace App\Models;

use App\Core\BaseModel;

class Wishlist extends BaseModel
{
    public function add(int $userId, int $productId): bool
    {
        $sql = "INSERT IGNORE INTO wishlists (user_id, product_id) VALUES (:user_id, :product_id)";
        $stmt = $this->query($sql, ['user_id' => $userId, 'product_id' => $productId]);
        return $stmt->rowCount() > 0;
    }

    public function remove(int $userId, int $productId): bool
    {
        return $this->delete('wishlists', 'user_id = :user_id AND product_id = :product_id', [
            'user_id'    => $userId,
            'product_id' => $productId,
        ]);
    }

    public function toggle(int $userId, int $productId): array
    {
        if ($this->isWishlisted($userId, $productId)) {
            $this->remove($userId, $productId);
            $action = 'removed';
        } else {
            $this->add($userId, $productId);
            $action = 'added';
        }
        return [
            'action' => $action,
            'count'  => $this->countByUserId($userId),
        ];
    }

    public function isWishlisted(int $userId, int $productId): bool
    {
        $sql = "SELECT COUNT(*) FROM wishlists WHERE user_id = :user_id AND product_id = :product_id";
        return (int)$this->fetchColumn($sql, [
            'user_id'    => $userId,
            'product_id' => $productId,
        ]) > 0;
    }

    public function countByUserId(int $userId): int
    {
        $sql = "SELECT COUNT(*) FROM wishlists WHERE user_id = :user_id";
        return (int)$this->fetchColumn($sql, ['user_id' => $userId]);
    }

    public function getUserProductIds(int $userId): array
    {
        $sql = "SELECT product_id FROM wishlists WHERE user_id = :user_id";
        $rows = $this->fetchAll($sql, ['user_id' => $userId]);
        return array_column($rows, 'product_id');
    }

    public function getByUserId(int $userId): array
    {
        $sql = "SELECT 
                    p.id, p.name, p.slug, p.description, p.brand_id,
                    w.created_at AS wishlisted_at,
                    MAX(b.name) AS brand_name,
                    MAX(c.name) AS category_name,
                    MAX(pi.image_url) AS image_url,
                    MIN(pv.price) AS min_price,
                    MIN(pv.sale_price) AS min_sale_price,
                    SUM(pv.stock_quantity) AS total_stock
                FROM wishlists w
                JOIN products p ON w.product_id = p.id
                LEFT JOIN brands b ON p.brand_id = b.id
                LEFT JOIN product_categories pc ON p.id = pc.product_id
                LEFT JOIN categories c ON pc.category_id = c.id
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                LEFT JOIN product_variants pv ON p.id = pv.product_id AND pv.status = 'active'
                WHERE w.user_id = :user_id AND p.status = 'active'
                GROUP BY p.id, p.name, p.slug, p.description, p.brand_id, w.created_at
                ORDER BY w.created_at DESC";

        return $this->fetchAll($sql, ['user_id' => $userId]);
    }
}
