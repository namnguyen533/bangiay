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

    public function getRatingStats(int $productId): array
    {
        $sql = "SELECT 
                    COUNT(*) AS total_reviews,
                    COALESCE(AVG(rating), 5) AS avg_rating,
                    SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) AS star_5,
                    SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) AS star_4,
                    SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) AS star_3,
                    SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) AS star_2,
                    SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) AS star_1
                FROM product_reviews 
                WHERE product_id = :product_id AND status = 'visible'";
        $stats = $this->fetch($sql, ['product_id' => $productId]);
        if (!$stats) {
            return [
                'total_reviews' => 0,
                'avg_rating'    => 5.0,
                'star_5' => 0, 'star_4' => 0, 'star_3' => 0, 'star_2' => 0, 'star_1' => 0
            ];
        }
        $stats['total_reviews'] = (int)$stats['total_reviews'];
        $stats['avg_rating'] = round((float)$stats['avg_rating'], 1);
        return $stats;
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
