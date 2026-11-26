<?php

namespace App\Models;

use App\Core\BaseModel;

class Brand extends BaseModel
{
    public function getAllActive(): array
    {
        $sql = "SELECT * FROM brands WHERE status = 'active' ORDER BY name ASC";
        return $this->fetchAll($sql);
    }

    public function getSearchSuggestions(string $keyword, int $limit = 4): array
    {
        $sql = "SELECT b.id, b.name, COUNT(p.id) AS product_count
                FROM brands b
                JOIN products p ON p.brand_id = b.id AND p.status = 'active'
                WHERE b.status = 'active' AND b.name LIKE :keyword
                GROUP BY b.id, b.name
                ORDER BY b.name ASC
                LIMIT {$limit}";

        return $this->fetchAll($sql, ['keyword' => '%' . $keyword . '%']);
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM brands WHERE id = :id";
        return $this->fetch($sql, ['id' => $id]);
    }

    public function getAll(): array
    {
        return $this->fetchAll(
            'SELECT b.*, COUNT(p.id) AS product_count
             FROM brands b LEFT JOIN products p ON p.brand_id = b.id
             GROUP BY b.id, b.name, b.slug, b.status
             ORDER BY b.name ASC'
        );
    }

    public function create(array $data): int
    {
        return $this->insert('brands', $data);
    }

    public function updateBrand(int $id, array $data): bool
    {
        return $this->update('brands', $data, 'id = :id', ['id' => $id]);
    }

    public function deleteBrand(int $id): bool
    {
        return $this->delete('brands', 'id = :id', ['id' => $id]);
    }

    public function nameExists(string $name, ?int $exceptId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM brands WHERE name = :name';
        $params = ['name' => $name];
        if ($exceptId !== null) {
            $sql .= ' AND id != :id';
            $params['id'] = $exceptId;
        }
        return (int)$this->fetchColumn($sql, $params) > 0;
    }
}
