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
        $stmt->execute([
            'qty' => $quantity,
            'id' => $variantId,
            'check_qty' => $quantity
        ]);
        return $stmt->rowCount() > 0;
    }

    public function increaseStock(int $variantId, int $quantity): bool
    {
        $sql = "UPDATE product_variants 
                SET stock_quantity = stock_quantity + :qty 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'qty' => $quantity,
            'id' => $variantId
        ]);
    }

    public function checkStock(int $variantId, int $quantity): bool
    {
        $sql = "SELECT stock_quantity FROM product_variants WHERE id = :id AND status = 'active'";
        $stock = (int)$this->fetchColumn($sql, ['id' => $variantId]);
        return $stock >= $quantity;
    }

    /**
     * Lấy các biến thể cùng sản phẩm với một biến thể cho trước
     */
    public function getSiblingVariants(int $variantId): array
    {
        $variant = $this->findById($variantId);
        if (!$variant) {
            return [];
        }
        return $this->getByProductId((int)$variant['product_id']);
    }
}
