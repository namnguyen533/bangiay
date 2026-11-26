<?php

namespace App\Models;

use App\Core\BaseModel;
use DomainException;
use Exception;
use Throwable;

class Order extends BaseModel
{
    /**
     * Tạo đơn hàng mới kèm chi tiết sản phẩm và trừ tồn kho (Dùng Transaction)
     */
    public function createOrder(array $orderData, array $cartItems): int
    {
        $this->beginTransaction();

        try {
            // 1. Thêm bản ghi đơn hàng
            $orderId = $this->insert('orders', $orderData);

            $variantModel = new ProductVariant();

            // 2. Thêm từng item vào order_items và trừ kho
            foreach ($cartItems as $item) {
                $lineTotal = $item['price'] * $item['quantity'];

                $this->insert('order_items', [
                    'order_id'     => $orderId,
                    'variant_id'   => $item['variant_id'],
                    'product_name' => $item['product_name'],
                    'sku'          => $item['sku'] ?? 'SKU-' . $item['variant_id'],
                    'size'         => $item['size'],
                    'color'        => $item['color'],
                    'unit_price'   => $item['price'],
                    'quantity'     => $item['quantity'],
                    'line_total'   => $lineTotal,
                ]);

                // Trừ tồn kho biến thể
                $variantModel->decreaseStock($item['variant_id'], $item['quantity']);
            }

            $this->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }

    public function getAllForAdmin(): array
    {
        $sql = "SELECT o.*, u.full_name AS user_name, u.email AS user_email
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                ORDER BY o.id DESC";
        return $this->fetchAll($sql);
    }

    public function getAllForUser(int $userId): array
    {
        return $this->fetchAll(
            'SELECT id, order_code, created_at, order_status, payment_status, payment_method,
                    recipient_name, shipping_address, total_amount, cancellation_reason
             FROM orders
             WHERE user_id = :user_id
             ORDER BY id DESC',
            ['user_id' => $userId]
        );
    }

    public function cancelForUser(int $orderId, int $userId, string $reason): void
    {
        $this->beginTransaction();

        try {
            $order = $this->fetch(
                'SELECT id, order_status FROM orders WHERE id = :id AND user_id = :user_id FOR UPDATE',
                ['id' => $orderId, 'user_id' => $userId]
            );
            if (!$order) {
                throw new DomainException('Không tìm thấy đơn hàng của bạn.');
            }
            if (!in_array($order['order_status'], ['pending', 'confirmed'], true)) {
                throw new DomainException('Chỉ có thể hủy đơn đang chờ xử lý hoặc đã xác nhận.');
            }

            $updated = $this->query(
                "UPDATE orders
                 SET order_status = 'cancelled', cancellation_reason = :reason
                 WHERE id = :id AND user_id = :user_id
                   AND order_status IN ('pending', 'confirmed')",
                ['reason' => $reason, 'id' => $orderId, 'user_id' => $userId]
            );
            if ($updated->rowCount() !== 1) {
                throw new DomainException('Trạng thái đơn hàng đã thay đổi. Vui lòng tải lại trang.');
            }

            $items = $this->fetchAll(
                'SELECT variant_id, SUM(quantity) AS quantity
                 FROM order_items
                 WHERE order_id = :order_id
                 GROUP BY variant_id',
                ['order_id' => $orderId]
            );
            $variantModel = new ProductVariant();
            foreach ($items as $item) {
                $variantModel->increaseStock((int)$item['variant_id'], (int)$item['quantity']);
            }

            $this->commit();
        } catch (Throwable $exception) {
            if ($this->inTransaction()) {
                $this->rollBack();
            }
            throw $exception;
        }
    }

    public function getDetail(int $orderId): ?array
    {
        $sql = "SELECT o.*, u.full_name AS user_name, u.email AS user_email
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                WHERE o.id = :id";
        $order = $this->fetch($sql, ['id' => $orderId]);
        if (!$order) {
            return null;
        }

        $sqlItems = "SELECT oi.*, pi.image_url 
                     FROM order_items oi
                     LEFT JOIN product_variants pv ON oi.variant_id = pv.id
                     LEFT JOIN product_images pi ON pv.product_id = pi.product_id AND pi.is_primary = 1
                     WHERE oi.order_id = :order_id";
        $order['items'] = $this->fetchAll($sqlItems, ['order_id' => $orderId]);

        return $order;
    }

    public function updateStatus(int $orderId, string $status): bool
    {
        $updated = $this->query(
            "UPDATE orders SET order_status = :status
             WHERE id = :id AND order_status <> 'cancelled'",
            ['status' => $status, 'id' => $orderId]
        );
        if ($updated->rowCount() > 0) {
            return true;
        }

        $order = $this->fetch('SELECT order_status FROM orders WHERE id = :id', ['id' => $orderId]);
        return $order !== null && $order['order_status'] === $status;
    }

    public function hasUserPurchasedProduct(int $userId, int $productId): bool
    {
        $sql = "SELECT COUNT(*) 
                FROM orders o
                JOIN order_items oi ON o.id = oi.order_id
                JOIN product_variants pv ON oi.variant_id = pv.id
                WHERE o.user_id = :user_id 
                  AND pv.product_id = :product_id 
                  AND o.order_status IN ('completed', 'delivered', 'confirmed', 'shipping', 'pending')";
        return ((int)$this->fetchColumn($sql, [
            'user_id'    => $userId,
            'product_id' => $productId
        ])) > 0;
    }

    public function getDashboardStats(): array
    {
        $revenue = (float)$this->fetchColumn("SELECT SUM(total_amount) FROM orders WHERE order_status IN ('confirmed','shipping','completed')");
        $totalOrders = (int)$this->fetchColumn("SELECT COUNT(*) FROM orders");
        $totalProducts = (int)$this->fetchColumn("SELECT COUNT(*) FROM products WHERE status = 'active'");
        $totalUsers = (int)$this->fetchColumn("SELECT COUNT(*) FROM users");

        $recentOrders = $this->fetchAll("SELECT * FROM orders ORDER BY id DESC LIMIT 5");

        return [
            'revenue'       => $revenue,
            'total_orders'   => $totalOrders,
            'total_products' => $totalProducts,
            'total_users'    => $totalUsers,
            'recent_orders'  => $recentOrders,
        ];
    }
}
