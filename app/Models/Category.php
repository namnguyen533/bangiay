<?php

namespace App\Models;

use App\Core\BaseModel;

class Category extends BaseModel
{
    public function getAllActive(): array
    {
        $sql = "SELECT c.id, c.parent_id, c.name, c.slug, c.status, COUNT(pc.product_id) AS total_products 
                FROM categories c
                LEFT JOIN product_categories pc ON c.id = pc.category_id
                WHERE c.status = 'active'
                GROUP BY c.id, c.parent_id, c.name, c.slug, c.status
                ORDER BY c.name ASC";
        return $this->fetchAll($sql);
    }

    public function getAll(): array
    {
        $sql = "SELECT * FROM categories ORDER BY id DESC";
        return $this->fetchAll($sql);
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM categories WHERE id = :id";
        return $this->fetch($sql, ['id' => $id]);
    }

    public function create(array $data): int
    {
        return $this->insert('categories', $data);
    }

    public function updateCategory(int $id, array $data): bool
    {
        return $this->update('categories', $data, 'id = :id', ['id' => $id]);
    }

    public function deleteCategory(int $id): bool
    {
        return $this->delete('categories', 'id = :id', ['id' => $id]);
    }
}
