<?php

namespace App\Models;

use App\Core\BaseModel;
use DomainException;
use Exception;
use Throwable;

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
        $order = $this->fetch(
            'SELECT order_status, payment_status FROM orders WHERE id = :id',
            ['id' => $orderId]
        );
        if (!$order) {
            return false;
        }
        if (in_array($status, ['completed', 'cancelled', 'returned', 'return_requested'], true)) {
            return false;
        }
        $allowedNext = [
            'pending' => 'confirmed',
            'confirmed' => 'processing',
            'processing' => 'shipping',
        ];
        if (($allowedNext[$order['order_status']] ?? null) !== $status) {
            return false;
        }

        $updated = $this->query(
            'UPDATE orders SET order_status = :status WHERE id = :id',
            ['status' => $status, 'id' => $orderId]
        );
        return $updated->rowCount() > 0;
    }

    public function updatePaymentStatus(int $orderId, string $paymentStatus): bool
    {
        return $this->update('orders', ['payment_status' => $paymentStatus], 'id = :id', ['id' => $orderId]);
    }

    /**
     * Khách hủy: pending hủy trực tiếp; confirmed gửi yêu cầu chờ admin duyệt.
     */
    public function cancelOrderByUser(int $orderId, int $userId, string $reason): array
    {
        $reason = trim($reason);
        if ($reason === '' || mb_strlen($reason) > 500) {
            return ['success' => false, 'message' => 'Vui lòng nhập lý do hủy từ 1 đến 500 ký tự.'];
        }

        $this->beginTransaction();
        try {
            $order = $this->fetch(
                'SELECT * FROM orders WHERE id = :id FOR UPDATE',
                ['id' => $orderId]
            );
            if (!$order) {
                throw new DomainException('Đơn hàng không tồn tại!');
            }
            if ((int)$order['user_id'] !== $userId) {
                throw new DomainException('Bạn không có quyền thao tác đơn hàng này!');
            }
            if ($order['order_status'] === 'pending') {
                $this->cancelAndRestock($orderId, (int)$order['voucher_id'], $reason);
                $this->commit();
                return ['success' => true, 'message' => 'Đơn hàng đã được hủy và số lượng đã hoàn lại kho.', 'direct' => true];
            }
            if ($order['order_status'] !== 'confirmed') {
                throw new DomainException('Chỉ yêu cầu hủy khi đơn đang chờ xác nhận hoặc đã xác nhận.');
            }
            if (($order['cancellation_status'] ?? 'none') === 'pending') {
                throw new DomainException('Yêu cầu hủy đơn đã được gửi và đang chờ quản trị viên duyệt.');
            }

            $this->query(
                "UPDATE orders
                 SET cancellation_status = 'pending', cancellation_reason = :reason,
                     cancellation_rejection_reason = NULL, cancel_requested_at = NOW()
                 WHERE id = :id",
                ['reason' => $reason, 'id' => $orderId]
            );
            $this->commit();
            return ['success' => true, 'message' => 'Đã gửi yêu cầu hủy đơn. Quản trị viên sẽ xác nhận hoặc từ chối.', 'direct' => false];
        } catch (DomainException $exception) {
            if ($this->inTransaction()) $this->rollBack();
            return ['success' => false, 'message' => $exception->getMessage()];
        } catch (Throwable $exception) {
            if ($this->inTransaction()) $this->rollBack();
            error_log('Customer cancellation failed: ' . $exception->getMessage());
            return ['success' => false, 'message' => 'Không thể xử lý yêu cầu hủy lúc này.'];
        }
    }

    public function cancelOrderByAdmin(int $orderId, string $reason): array
    {
        $reason = trim($reason);
        if ($reason === '' || mb_strlen($reason) > 500) {
            return ['success' => false, 'message' => 'Vui lòng nhập lý do hủy từ 1 đến 500 ký tự.'];
        }
        $this->beginTransaction();
        try {
            $order = $this->fetch('SELECT * FROM orders WHERE id = :id FOR UPDATE', ['id' => $orderId]);
            if (!$order) throw new DomainException('Đơn hàng không tồn tại!');
            if (!in_array($order['order_status'], ['pending', 'confirmed', 'processing'], true)) {
                throw new DomainException('Không thể hủy đơn đã giao hàng hoặc đã hủy.');
            }
            $this->cancelAndRestock($orderId, (int)$order['voucher_id'], $reason);
            $this->query(
                "UPDATE orders SET cancellation_status = 'approved', cancellation_rejection_reason = NULL WHERE id = :id",
                ['id' => $orderId]
            );
            $this->commit();
            return ['success' => true, 'message' => 'Đã hủy đơn hàng và hoàn trả số lượng vào kho!'];
        } catch (DomainException $exception) {
            if ($this->inTransaction()) $this->rollBack();
            return ['success' => false, 'message' => $exception->getMessage()];
        } catch (Throwable $exception) {
            if ($this->inTransaction()) $this->rollBack();
            error_log('Admin cancellation failed: ' . $exception->getMessage());
            return ['success' => false, 'message' => 'Không thể hủy đơn hàng lúc này.'];
        }
    }

    public function approveCancellation(int $orderId): array
    {
        $order = $this->fetch(
            "SELECT cancellation_reason FROM orders WHERE id = :id AND cancellation_status = 'pending'",
            ['id' => $orderId]
        );
        $reason = trim((string)($order['cancellation_reason'] ?? ''));
        if ($reason === '') {
            $reason = 'Quản trị viên đồng ý yêu cầu hủy đơn';
        }
        return $this->cancelOrderByAdmin($orderId, $reason);
    }

    public function rejectCancellation(int $orderId, string $reason): array
    {
        $reason = trim($reason);
        if ($reason === '') return ['success' => false, 'message' => 'Vui lòng nhập lý do từ chối hủy.'];
        $updated = $this->query(
            "UPDATE orders
             SET cancellation_status = 'rejected', cancellation_rejection_reason = :reason
             WHERE id = :id AND cancellation_status = 'pending'",
            ['reason' => $reason, 'id' => $orderId]
        );
        return $updated->rowCount() === 1
            ? ['success' => true, 'message' => 'Đã từ chối yêu cầu hủy đơn.']
            : ['success' => false, 'message' => 'Yêu cầu hủy không tồn tại hoặc đã được xử lý.'];
    }

    private function cancelAndRestock(int $orderId, int $voucherId, string $reason): void
    {
        $updated = $this->query(
            "UPDATE orders
             SET order_status = 'cancelled', cancellation_reason = :reason,
                 cancellation_status = 'approved'
             WHERE id = :id AND order_status IN ('pending','confirmed','processing')",
            ['reason' => $reason, 'id' => $orderId]
        );
        if ($updated->rowCount() !== 1) {
            throw new DomainException('Trạng thái đơn hàng đã thay đổi. Vui lòng tải lại trang.');
        }
        $items = $this->fetchAll(
            'SELECT variant_id, SUM(quantity) AS quantity FROM order_items WHERE order_id = :order_id GROUP BY variant_id',
            ['order_id' => $orderId]
        );
        $variantModel = new ProductVariant();
        foreach ($items as $item) {
            if (!empty($item['variant_id'])) {
                $variantModel->increaseStock((int)$item['variant_id'], (int)$item['quantity']);
            }
        }
        if ($voucherId > 0) (new Voucher())->refundUsage($voucherId);
    }

    public function confirmReceived(int $orderId, int $userId): array
    {
        $order = $this->fetch('SELECT * FROM orders WHERE id = :id', ['id' => $orderId]);
        if (!$order) return ['success' => false, 'message' => 'Đơn hàng không tồn tại.'];
        if ((int)$order['user_id'] !== $userId) return ['success' => false, 'message' => 'Bạn không có quyền thao tác đơn hàng này.'];
        if ($order['order_status'] !== 'shipping') return ['success' => false, 'message' => 'Chỉ xác nhận nhận hàng khi đơn đang giao.'];
        $updated = $this->query(
            "UPDATE orders
             SET order_status = 'completed', payment_status = CASE WHEN payment_status = 'unpaid' THEN 'paid' ELSE payment_status END,
                 completed_at = NOW()
             WHERE id = :id AND order_status = 'shipping'",
            ['id' => $orderId]
        );
        return $updated->rowCount() === 1
            ? ['success' => true, 'message' => 'Xác nhận đã nhận hàng thành công. Đơn đã được ghi nhận doanh thu.']
            : ['success' => false, 'message' => 'Trạng thái đơn hàng đã thay đổi.'];
    }

    public function requestReturn(int $orderId, int $userId, string $reason, array $imagePaths): array
    {
        $reason = trim($reason);
        if ($reason === '' || mb_strlen($reason) > 500) {
            return ['success' => false, 'message' => 'Vui lòng nhập lý do đổi/trả từ 1 đến 500 ký tự.'];
        }
        if (empty($imagePaths)) {
            return ['success' => false, 'message' => 'Vui lòng tải lên ít nhất một hình ảnh minh họa.'];
        }
        $order = $this->fetch('SELECT * FROM orders WHERE id = :id', ['id' => $orderId]);
        if (!$order) return ['success' => false, 'message' => 'Đơn hàng không tồn tại.'];
        if ((int)$order['user_id'] !== $userId) return ['success' => false, 'message' => 'Bạn không có quyền thao tác đơn hàng này.'];
        if ($order['order_status'] !== 'shipping') return ['success' => false, 'message' => 'Chỉ yêu cầu đổi/trả khi đơn đang ở trạng thái giao hàng.'];
        $updated = $this->query(
            "UPDATE orders
             SET order_status = 'return_requested', return_status = 'pending',
                 return_reason = :reason, return_images = :images, return_rejection_reason = NULL
             WHERE id = :id AND order_status = 'shipping'",
            ['reason' => $reason, 'images' => json_encode($imagePaths, JSON_UNESCAPED_UNICODE), 'id' => $orderId]
        );
        return $updated->rowCount() === 1
            ? ['success' => true, 'message' => 'Đã gửi yêu cầu đổi/trả. Quản trị viên sẽ xem xét thông tin bạn cung cấp.']
            : ['success' => false, 'message' => 'Không thể gửi yêu cầu đổi/trả lúc này.'];
    }

    public function approveReturn(int $orderId): array
    {
        $updated = $this->query(
            "UPDATE orders SET order_status = 'returned', return_status = 'approved'
             WHERE id = :id AND order_status = 'return_requested' AND return_status = 'pending'",
            ['id' => $orderId]
        );
        return $updated->rowCount() === 1
            ? ['success' => true, 'message' => 'Đã đồng ý đổi/trả. Vui lòng chờ xác nhận đã nhận hàng trả lại.']
            : ['success' => false, 'message' => 'Yêu cầu đổi/trả không tồn tại hoặc đã xử lý.'];
    }

    public function rejectReturn(int $orderId, string $reason): array
    {
        $reason = trim($reason);
        if ($reason === '') return ['success' => false, 'message' => 'Vui lòng nhập lý do từ chối đổi/trả.'];
        $updated = $this->query(
            "UPDATE orders
             SET order_status = 'shipping', return_status = 'rejected', return_rejection_reason = :reason
             WHERE id = :id AND order_status = 'return_requested' AND return_status = 'pending'",
            ['reason' => $reason, 'id' => $orderId]
        );
        return $updated->rowCount() === 1
            ? ['success' => true, 'message' => 'Đã từ chối yêu cầu đổi/trả.']
            : ['success' => false, 'message' => 'Yêu cầu đổi/trả không tồn tại hoặc đã xử lý.'];
    }

    public function confirmReturnReceived(int $orderId): array
    {
        $updated = $this->query(
            "UPDATE orders
             SET return_status = 'received', payment_status = 'refunded', return_received_at = NOW()
             WHERE id = :id AND order_status = 'returned' AND return_status = 'approved'",
            ['id' => $orderId]
        );
        if ($updated->rowCount() === 1) return ['success' => true, 'message' => 'Đã xác nhận nhận hàng trả lại.'];
        return ['success' => false, 'message' => 'Chỉ xác nhận sau khi đã đồng ý yêu cầu đổi/trả.'];
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
        $revenue = (float)$this->fetchColumn("SELECT SUM(total_amount) FROM orders WHERE order_status = 'completed'");
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
                       SUM(CASE WHEN order_status = 'completed' THEN total_amount ELSE 0 END) AS revenue,
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
                       SUM(CASE WHEN order_status = 'completed' THEN total_amount ELSE 0 END) AS revenue,
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
                WHERE o.order_status = 'completed'
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
                WHERE o.order_status = 'completed' AND c.name IS NOT NULL
                GROUP BY c.id, c.name
                ORDER BY total_revenue DESC";
        return $this->fetchAll($sql);
    }
}
