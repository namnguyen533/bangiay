<?php

namespace App\Models;

use App\Core\BaseModel;

class ProductVariant extends BaseModel
{
    public function getByProductId(int $productId): array
    {
        $sql = "SELECT * FROM product_variants 
                WHERE product_id = :product_id AND status = 'active'
                ORDER BY size ASC, color ASC";
        return $this->fetchAll($sql, ['product_id' => $productId]);
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT pv.*, p.name AS product_name, pi.image_url
                FROM product_variants pv
                JOIN products p ON pv.product_id = p.id
                LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_primary = 1
                WHERE pv.id = :id";
        return $this->fetch($sql, ['id' => $id]);
    }

    public function create(array $data): int
    {
        return $this->insert('product_variants', $data);
    }

    public function updateVariant(int $id, array $data): bool
    {
        return $this->update('product_variants', $data, 'id = :id', ['id' => $id]);
    }

    public function deleteByProductId(int $productId): bool
    {
        return $this->delete('product_variants', 'product_id = :product_id', ['product_id' => $productId]);
    }

    public function decreaseStock(int $variantId, int $quantity): bool
    {
        $sql = "UPDATE product_variants 
                SET stock_quantity = stock_quantity - :qty 
                WHERE id = :id AND stock_quantity >= :check_qty";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'qty' => $quantity,
            'id' => $variantId,
            'check_qty' => $quantity
        ]);
    }
}
