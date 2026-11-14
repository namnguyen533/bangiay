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

    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM brands WHERE id = :id";
        return $this->fetch($sql, ['id' => $id]);
    }
}
