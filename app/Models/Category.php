<?php

namespace App\Models;

use App\Core\BaseModel;

class Category extends BaseModel
{
    public function getAllActive(): array
    {
        $sql = "SELECT c.id, COUNT(pc.product_id) AS total_products
                FROM categories c
                LEFT JOIN product_categories pc ON c.id = pc.category_id
                WHERE c.status = 'active'
                GROUP BY c.id";
        $counts = [];
        foreach ($this->fetchAll($sql) as $row) {
            $counts[(int)$row['id']] = (int)$row['total_products'];
        }

        return array_map(static function (array $category) use ($counts): array {
            $category['total_products'] = $counts[(int)$category['id']] ?? 0;
            return $category;
        }, $this->getFlatList(true));
    }

    public function getAll(): array
    {
        $sql = "SELECT * FROM categories ORDER BY id DESC";
        return $this->fetchAll($sql);
    }

    public function getFlatList(bool $activeOnly = false): array
    {
        $where = $activeOnly ? "WHERE status = 'active'" : '';
        $categories = $this->fetchAll("SELECT id, parent_id, name, slug, status FROM categories {$where} ORDER BY name ASC");
        $byParent = [];
        foreach ($categories as $category) {
            $parentId = $category['parent_id'] === null ? 0 : (int)$category['parent_id'];
            $byParent[$parentId][] = $category;
        }

        $result = [];
        $visit = function (int $parentId, int $depth, array $ancestors) use (&$visit, &$result, $byParent): void {
            foreach ($byParent[$parentId] ?? [] as $category) {
                $id = (int)$category['id'];
                if (isset($ancestors[$id])) {
                    continue;
                }
                $category['depth'] = $depth;
                $result[] = $category;
                $nextAncestors = $ancestors;
                $nextAncestors[$id] = true;
                $visit($id, $depth + 1, $nextAncestors);
            }
        };
        $visit(0, 0, []);

        return $result;
    }

    public function getDescendantIds(int $categoryId, bool $activeOnly = false): array
    {
        $relations = $this->fetchAll('SELECT id, parent_id, status FROM categories');
        $children = [];
        $active = [];
        foreach ($relations as $row) {
            $active[(int)$row['id']] = $row['status'] === 'active';
            if ($row['parent_id'] !== null) {
                $children[(int)$row['parent_id']][] = (int)$row['id'];
            }
        }

        $ids = [];
        $pending = [$categoryId];
        while ($pending) {
            $current = array_pop($pending);
            if (in_array($current, $ids, true)) {
                continue;
            }
            if ($activeOnly && empty($active[$current])) {
                continue;
            }
            $ids[] = $current;
            foreach ($children[$current] ?? [] as $childId) {
                $pending[] = $childId;
            }
        }

        return $ids;
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

    public function isDescendant(int $categoryId, int $candidateParentId): bool
    {
        return in_array($candidateParentId, $this->getDescendantIds($categoryId), true);
    }
}
