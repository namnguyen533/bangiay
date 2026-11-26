<?php

namespace App\Models;

use App\Core\BaseModel;

class UserAddress extends BaseModel
{
    public function getByUserId(int $userId): array
    {
        $sql = "SELECT * FROM user_addresses WHERE user_id = :user_id ORDER BY is_default DESC, id DESC";
        return $this->fetchAll($sql, ['user_id' => $userId]);
    }

    public function getDefault(int $userId): ?array
    {
        $sql = "SELECT * FROM user_addresses WHERE user_id = :user_id AND is_default = 1 LIMIT 1";
        $address = $this->fetch($sql, ['user_id' => $userId]);
        if (!$address) {
            $sqlFirst = "SELECT * FROM user_addresses WHERE user_id = :user_id ORDER BY id DESC LIMIT 1";
            $address = $this->fetch($sqlFirst, ['user_id' => $userId]);
        }
        return $address;
    }

    public function findById(int $id, int $userId): ?array
    {
        $sql = "SELECT * FROM user_addresses WHERE id = :id AND user_id = :user_id";
        return $this->fetch($sql, ['id' => $id, 'user_id' => $userId]);
    }

    public function createAddress(array $data): int
    {
        if (!empty($data['is_default'])) {
            $this->resetDefault((int)$data['user_id']);
        } else {
            // Nếu là địa chỉ đầu tiên của user thì tự động đặt làm mặc định
            $count = (int)$this->fetchColumn("SELECT COUNT(*) FROM user_addresses WHERE user_id = :uid", ['uid' => $data['user_id']]);
            if ($count === 0) {
                $data['is_default'] = 1;
            }
        }
        return $this->insert('user_addresses', $data);
    }

    public function resetDefault(int $userId): void
    {
        $this->update('user_addresses', ['is_default' => 0], 'user_id = :uid', ['uid' => $userId]);
    }

    public function setDefault(int $userId, int $addressId): bool
    {
        $this->resetDefault($userId);
        return $this->update('user_addresses', ['is_default' => 1], 'id = :id AND user_id = :uid', [
            'id'  => $addressId,
            'uid' => $userId,
        ]);
    }

    public function deleteAddress(int $id, int $userId): bool
    {
        return $this->delete('user_addresses', 'id = :id AND user_id = :uid', [
            'id'  => $id,
            'uid' => $userId,
        ]);
    }
}
