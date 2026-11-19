<?php

namespace App\Models;

use App\Core\BaseModel;
use Exception;

class Order extends BaseModel
{
    /**
     * Tạo đơn hàng mới kèm chi tiết sản phẩm, trừ tồn kho và ghi nhận voucher (Dùng Transaction)
     */
    public function createOrder(array $orderData, array $cartItems): int
    {
        $this->beginTransaction();

        try {
            $variantModel = new ProductVariant();

            // 1. Kiểm tra tồn kho trước khi chốt đơn (Sử dụng FOR UPDATE để tránh race-condition)
            foreach ($cartItems as $item) {
                $variantId = (int)$item['variant_id'];
                $quantity = (int)$item['quantity'];

                $sqlCheck = "SELECT stock_quantity, status FROM product_variants WHERE id = :id FOR UPDATE";
                $variant = $this->fetch($sqlCheck, ['id' => $variantId]);

                if (!$variant || $variant['status'] !== 'active') {
                    throw new Exception("Sản phẩm '{$item['product_name']}' hiện không khả dụng!");
                }

                if ((int)$variant['stock_quantity'] < $quantity) {
                    throw new Exception("Sản phẩm '{$item['product_name']}' (Size {$item['size']}) chỉ còn {$variant['stock_quantity']} đôi trong kho!");
                }
            }

            // 2. Thêm bản ghi đơn hàng
            $orderId = $this->insert('orders', $orderData);

            // 3. Thêm chi tiết đơn hàng và trừ kho
            foreach ($cartItems as $item) {
                $lineTotal = (float)$item['price'] * (int)$item['quantity'];

                $this->insert('order_items', [
                    'order_id'     => $orderId,
                    'variant_id'   => $item['variant_id'],
                    'product_name' => $item['product_name'],
                    'sku'          => $item['sku'] ?? ('SKU-' . $item['variant_id']),
                    'size'         => $item['size'],
                    'color'        => $item['color'],
                    'unit_price'   => $item['price'],
                    'quantity'     => $item['quantity'],
                    'line_total'   => $lineTotal,
                ]);

                // Trừ tồn kho biến thể
                $decreased = $variantModel->decreaseStock((int)$item['variant_id'], (int)$item['quantity']);
                if (!$decreased) {
                    throw new Exception("Không thể trừ tồn kho sản phẩm '{$item['product_name']}'!");
                }
            }

            // 4. Ghi nhận lượt dùng voucher (nếu có)
            if (!empty($orderData['voucher_id'])) {
                $voucherModel = new Voucher();
                $voucherModel->recordUsage((int)$orderData['voucher_id']);
            }

            $this->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }

    public function getAllForAdmin(?string $status = null, ?string $keyword = null): array
    {
        $where = [];
        $params = [];

        if (!empty($status) && $status !== 'all') {
            $where[] = "o.order_status = :status";
            $params['status'] = $status;
        }

        if (!empty($keyword)) {
            $where[] = "(o.order_code LIKE :kw OR o.recipient_name LIKE :kw OR o.recipient_phone LIKE :kw)";
            $params['kw'] = '%' . $keyword . '%';
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "SELECT o.*, u.full_name AS user_name, u.email AS user_email,
                       (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) AS total_items
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                {$whereClause}
                ORDER BY o.id DESC";
        return $this->fetchAll($sql, $params);
    }

    public function getDetail(int $orderId): ?array
    {
        $sql = "SELECT o.*, u.full_name AS user_name, u.email AS user_email, v.code AS voucher_code
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                LEFT JOIN vouchers v ON o.voucher_id = v.id
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

    public function getOrderByCode(string $orderCode, ?string $phone = null): ?array
    {
        $sql = "SELECT id FROM orders WHERE order_code = :code";
        $params = ['code' => trim($orderCode)];
        if (!empty($phone)) {
            $sql .= " AND recipient_phone = :phone";
            $params['phone'] = trim($phone);
        }
        $order = $this->fetch($sql, $params);
        if (!$order) {
            return null;
        }
        return $this->getDetail((int)$order['id']);
    }

    public function getUserOrders(int $userId): array
    {
        $sql = "SELECT o.*, 
                       (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) AS total_items,
                       (SELECT SUM(quantity) FROM order_items WHERE order_id = o.id) AS total_quantity
                FROM orders o
                WHERE o.user_id = :uid
                ORDER BY o.id DESC";
        return $this->fetchAll($sql, ['uid' => $userId]);
    }

    public function getOrderDetailForUser(int $orderId, int $userId): ?array
    {
        $order = $this->getDetail($orderId);
        if (!$order || (int)$order['user_id'] !== $userId) {
            return null;
        }
        return $order;
    }

    public function updateStatus(int $orderId, string $status): bool
    {
        $order = $this->getDetail($orderId);
        if (!$order) {
            return false;
        }

        // Nếu trạng thái chuyển thành cancelled và trước đó chưa cancelled thì hoàn kho
        if ($status === 'cancelled' && $order['order_status'] !== 'cancelled') {
            return $this->cancelOrder($orderId)['success'];
        }

        $updateData = ['order_status' => $status];
        if ($status === 'completed' && $order['payment_status'] === 'unpaid') {
            $updateData['payment_status'] = 'paid';
        }

        return $this->update('orders', $updateData, 'id = :id', ['id' => $orderId]);
    }

    public function updatePaymentStatus(int $orderId, string $paymentStatus): bool
    {
        return $this->update('orders', ['payment_status' => $paymentStatus], 'id = :id', ['id' => $orderId]);
    }

    /**
     * Hủy đơn hàng và hoàn lại tồn kho cho các sản phẩm
     */
    public function cancelOrder(int $orderId, ?int $userId = null, string $reason = ''): array
    {
        $order = $this->getDetail($orderId);
        if (!$order) {
            return ['success' => false, 'message' => 'Đơn hàng không tồn tại!'];
        }

        if ($userId !== null && (int)$order['user_id'] !== $userId) {
            return ['success' => false, 'message' => 'Bạn không có quyền thao tác trên đơn hàng này!'];
        }

        // Chỉ cho phép hủy khi đơn còn ở trạng thái chờ xác nhận
        if (!in_array($order['order_status'], ['pending', 'confirmed'])) {
            return [
                'success' => false,
                'message' => 'Đơn hàng đã được xuất kho vận chuyển, không thể tự hủy! Vui lòng liên hệ CSKH.',
            ];
        }

        $this->beginTransaction();

        try {
            $note = $order['note'] ? ($order['note'] . " | Lý do hủy: " . $reason) : ("Lý do hủy: " . $reason);

            $this->update('orders', [
                'order_status' => 'cancelled',
                'note'         => trim($note),
            ], 'id = :id', ['id' => $orderId]);

            // Hoàn lại số lượng tồn kho
            $variantModel = new ProductVariant();
            foreach ($order['items'] as $item) {
                if (!empty($item['variant_id'])) {
                    $variantModel->increaseStock((int)$item['variant_id'], (int)$item['quantity']);
                }
            }

            // Hoàn lại lượt dùng voucher
            if (!empty($order['voucher_id'])) {
                $voucherModel = new Voucher();
                $voucherModel->refundUsage((int)$order['voucher_id']);
            }

            $this->commit();
            return [
                'success' => true,
                'message' => 'Đã hủy đơn hàng thành công và hoàn trả số lượng vào kho!',
            ];
        } catch (Exception $e) {
            $this->rollBack();
            return [
                'success' => false,
                'message' => 'Lỗi khi hủy đơn hàng: ' . $e->getMessage(),
            ];
        }
    }

    public function getDashboardStats(): array
    {
        $revenue = (float)$this->fetchColumn("SELECT SUM(total_amount) FROM orders WHERE order_status IN ('confirmed','shipping','completed')");
        $totalOrders = (int)$this->fetchColumn("SELECT COUNT(*) FROM orders");
        $totalProducts = (int)$this->fetchColumn("SELECT COUNT(*) FROM products WHERE status = 'active'");
        $totalUsers = (int)$this->fetchColumn("SELECT COUNT(*) FROM users");
        $pendingOrders = (int)$this->fetchColumn("SELECT COUNT(*) FROM orders WHERE order_status = 'pending'");

        $recentOrders = $this->fetchAll("SELECT * FROM orders ORDER BY id DESC LIMIT 5");

        return [
            'revenue'        => $revenue,
            'total_orders'   => $totalOrders,
            'total_products' => $totalProducts,
            'total_users'    => $totalUsers,
            'pending_orders' => $pendingOrders,
            'recent_orders'  => $recentOrders,
        ];
    }

    // ==========================================
    // THỐNG KÊ & BÁO CÁO DOANH THU CHO ADMIN
    // ==========================================

    /**
     * Doanh thu theo ngày trong N ngày gần nhất
     */
    public function getRevenueByDays(int $days = 15): array
    {
        $sql = "SELECT DATE(created_at) AS order_date, 
                       SUM(CASE WHEN order_status IN ('confirmed','shipping','completed') THEN total_amount ELSE 0 END) AS revenue,
                       COUNT(*) AS total_orders
                FROM orders
                WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                GROUP BY DATE(created_at)
                ORDER BY order_date ASC";
        return $this->fetchAll($sql, ['days' => $days]);
    }

    /**
     * Doanh thu theo tháng trong năm
     */
    public function getRevenueByMonth(int $year): array
    {
        $sql = "SELECT MONTH(created_at) AS month, 
                       SUM(CASE WHEN order_status IN ('confirmed','shipping','completed') THEN total_amount ELSE 0 END) AS revenue,
                       COUNT(*) AS total_orders
                FROM orders
                WHERE YEAR(created_at) = :year
                GROUP BY MONTH(created_at)
                ORDER BY month ASC";
        return $this->fetchAll($sql, ['year' => $year]);
    }

    /**
     * Tỷ lệ đơn hàng theo trạng thái
     */
    public function getOrderStatusDistribution(): array
    {
        $sql = "SELECT order_status, COUNT(*) AS count 
                FROM orders 
                GROUP BY order_status";
        return $this->fetchAll($sql);
    }

    /**
     * Top sản phẩm bán chạy nhất
     */
    public function getTopSellingProducts(int $limit = 8): array
    {
        $sql = "SELECT oi.product_name, oi.sku, 
                       MAX(pi.image_url) AS image_url,
                       SUM(oi.quantity) AS total_sold,
                       SUM(oi.line_total) AS total_revenue
                FROM order_items oi
                JOIN orders o ON oi.order_id = o.id
                LEFT JOIN product_variants pv ON oi.variant_id = pv.id
                LEFT JOIN product_images pi ON pv.product_id = pi.product_id AND pi.is_primary = 1
                WHERE o.order_status IN ('confirmed','shipping','completed')
                GROUP BY oi.product_name, oi.sku
                ORDER BY total_sold DESC
                LIMIT {$limit}";
        return $this->fetchAll($sql);
    }

    /**
     * Doanh số theo danh mục sản phẩm
     */
    public function getSalesByCategory(): array
    {
        $sql = "SELECT c.name AS category_name, 
                       SUM(oi.quantity) AS total_sold,
                       SUM(oi.line_total) AS total_revenue
                FROM order_items oi
                JOIN orders o ON oi.order_id = o.id
                LEFT JOIN product_variants pv ON oi.variant_id = pv.id
                LEFT JOIN product_categories pc ON pv.product_id = pc.product_id
                LEFT JOIN categories c ON pc.category_id = c.id
                WHERE o.order_status IN ('confirmed','shipping','completed') AND c.name IS NOT NULL
                GROUP BY c.id, c.name
                ORDER BY total_revenue DESC";
        return $this->fetchAll($sql);
    }
}
