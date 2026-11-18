<?php

namespace App\Models;

use App\Core\BaseModel;

class ProductReview extends BaseModel
{
    public function getVisibleForProduct(int $productId): array
    {
        return $this->fetchAll(
            "SELECT r.id, r.rating, r.content, r.admin_response, r.created_at, u.full_name
             FROM product_reviews r
             JOIN users u ON u.id = r.user_id
             WHERE r.product_id = :product_id AND r.status = 'visible'
             ORDER BY r.created_at DESC, r.id DESC",
            ['product_id' => $productId]
        );
    }

    public function findForUser(int $productId, int $userId): ?array
    {
        return $this->fetch(
            'SELECT * FROM product_reviews WHERE product_id = :product_id AND user_id = :user_id',
            ['product_id' => $productId, 'user_id' => $userId]
        );
    }

    public function create(int $productId, int $userId, int $rating, string $content): int
    {
        return $this->insert('product_reviews', [
            'product_id' => $productId,
            'user_id' => $userId,
            'rating' => $rating,
            'content' => $content,
            'status' => 'pending',
        ]);
    }

    public function getAllForAdmin(string $status = ''): array
    {
        $params = [];
        $where = '';
        if (in_array($status, ['pending', 'visible', 'hidden'], true)) {
            $where = 'WHERE r.status = :status';
            $params['status'] = $status;
        }

        return $this->fetchAll(
            "SELECT r.*, p.name AS product_name, u.full_name, u.email,
                    admin.full_name AS responder_name
             FROM product_reviews r
             JOIN products p ON p.id = r.product_id
             JOIN users u ON u.id = r.user_id
             LEFT JOIN users admin ON admin.id = r.responded_by
             {$where}
             ORDER BY FIELD(r.status, 'pending', 'visible', 'hidden'), r.created_at DESC",
            $params
        );
    }

    public function moderate(int $id, string $status, string $response, int $adminId): bool
    {
        return $this->update('product_reviews', [
            'status' => $status,
            'admin_response' => $response !== '' ? $response : null,
            'responded_by' => $response !== '' ? $adminId : null,
            'responded_at' => $response !== '' ? date('Y-m-d H:i:s') : null,
        ], 'id = :id', ['id' => $id]);
    }
}
