<?php

namespace App\Models;

use App\Core\BaseModel;

class User extends BaseModel
{
    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT u.*, r.name AS role_name
                FROM users u
                LEFT JOIN user_roles ur ON u.id = ur.user_id
                LEFT JOIN roles r ON ur.role_id = r.id
                WHERE u.email = :email";
        return $this->fetch($sql, ['email' => $email]);
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT u.*, r.name AS role_name
                FROM users u
                LEFT JOIN user_roles ur ON u.id = ur.user_id
                LEFT JOIN roles r ON ur.role_id = r.id
                WHERE u.id = :id";
        return $this->fetch($sql, ['id' => $id]);
    }

    public function register(string $fullName, string $email, string $phone, string $password): int
    {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $userId = $this->insert('users', [
            'full_name'     => $fullName,
            'email'         => $email,
            'phone'         => $phone,
            'password_hash' => $hash,
            'status'        => 'active'
        ]);

        // Mặc định role là Customer (id: 4)
        $this->insert('user_roles', [
            'user_id' => $userId,
            'role_id' => 4
        ]);

        return $userId;
    }

    public function checkLogin(string $email, string $password): ?array
    {
        $user = $this->findByEmail($email);
        if (!$user) {
            return null;
        }

        if ($user['status'] !== 'active') {
            return null;
        }

        if (password_verify($password, $user['password_hash'])) {
            // Xác định quyền admin
            $user['is_admin'] = in_array(strtolower($user['role_name'] ?? ''), ['admin', 'super admin']);
            $user['role'] = $user['is_admin'] ? 'admin' : 'customer';
            return $user;
        }

        return null;
    }

    public function countUsers(): int
    {
        return (int)$this->fetchColumn("SELECT COUNT(*) FROM users");
    }
}
