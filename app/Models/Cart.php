<?php

namespace App\Models;

class Cart
{
    private ProductVariant $variantModel;
    private Voucher $voucherModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        $this->variantModel = new ProductVariant();
        $this->voucherModel = new Voucher();
    }

    public function getItems(): array
    {
        return $_SESSION['cart'] ?? [];
    }

    /**
     * Lấy các mặt hàng trong giỏ kèm danh sách biến thể cùng sản phẩm để đổi size/màu
     */
    public function getItemsWithSiblings(): array
    {
        $items = $this->getItems();
        foreach ($items as &$item) {
            $siblings = $this->variantModel->getByProductId((int)$item['product_id']);
            $item['siblings'] = $siblings;
        }
        unset($item);
        return $items;
    }

    public function add(int $variantId, int $quantity = 1): array
    {
        $variant = $this->variantModel->findById($variantId);

        if (!$variant || $variant['status'] !== 'active') {
            return [
                'success' => false,
                'message' => 'Phiên bản giày này hiện không khả dụng!',
            ];
        }

        $stock = (int)$variant['stock_quantity'];
        if ($stock <= 0) {
            return [
                'success' => false,
                'message' => 'Phiên bản giày này hiện đã hết hàng!',
            ];
        }

        $currentQty = $_SESSION['cart'][$variantId]['quantity'] ?? 0;
        $newQty = $currentQty + $quantity;

        if ($newQty > $stock) {
            return [
                'success' => false,
                'message' => "Trong kho chỉ còn {$stock} đôi. Bạn đã có {$currentQty} đôi trong giỏ hàng!",
            ];
        }

        $effectivePrice = ($variant['sale_price'] && $variant['sale_price'] > 0)
            ? (float)$variant['sale_price']
            : (float)$variant['price'];

        if (isset($_SESSION['cart'][$variantId])) {
            $_SESSION['cart'][$variantId]['quantity'] = $newQty;
        } else {
            $_SESSION['cart'][$variantId] = [
                'variant_id'   => $variant['id'],
                'product_id'   => $variant['product_id'],
                'product_name' => $variant['product_name'],
                'image_url'    => $variant['image_url'] ?? '',
                'sku'          => $variant['sku'],
                'size'         => $variant['size'],
                'color'        => $variant['color'],
                'price'        => $effectivePrice,
                'quantity'     => $quantity,
                'stock'        => $stock,
            ];
        }

        $this->refreshVoucher();

        return [
            'success' => true,
            'message' => 'Đã thêm vào giỏ hàng thành công!',
        ];
    }

    public function update(int $variantId, int $quantity): array
    {
        if ($quantity <= 0) {
            $this->remove($variantId);
            return ['success' => true, 'message' => 'Đã xóa sản phẩm khỏi giỏ!'];
        }

        if (!isset($_SESSION['cart'][$variantId])) {
            return ['success' => false, 'message' => 'Sản phẩm không có trong giỏ!'];
        }

        $variant = $this->variantModel->findById($variantId);
        if (!$variant) {
            $this->remove($variantId);
            return ['success' => false, 'message' => 'Sản phẩm không còn tồn tại!'];
        }

        $stock = (int)$variant['stock_quantity'];
        if ($quantity > $stock) {
            $_SESSION['cart'][$variantId]['quantity'] = $stock;
            $this->refreshVoucher();
            return [
                'success' => false,
                'message' => "Kho chỉ còn {$stock} đôi. Đã điều chỉnh số lượng tối đa hiện có!",
            ];
        }

        $_SESSION['cart'][$variantId]['quantity'] = $quantity;
        $_SESSION['cart'][$variantId]['stock'] = $stock;
        $this->refreshVoucher();

        return ['success' => true, 'message' => 'Đã cập nhật số lượng!'];
    }

    /**
     * Đổi size / màu sắc sản phẩm trực tiếp trong giỏ hàng
     */
    public function changeVariant(int $oldVariantId, int $newVariantId): array
    {
        if ($oldVariantId === $newVariantId) {
            return ['success' => true, 'message' => 'Không thay đổi'];
        }

        if (!isset($_SESSION['cart'][$oldVariantId])) {
            return ['success' => false, 'message' => 'Sản phẩm cũ không có trong giỏ!'];
        }

        $newVariant = $this->variantModel->findById($newVariantId);
        if (!$newVariant || $newVariant['status'] !== 'active') {
            return ['success' => false, 'message' => 'Phiên bản mới không hợp lệ!'];
        }

        $oldItem = $_SESSION['cart'][$oldVariantId];
        $quantity = $oldItem['quantity'];

        $stock = (int)$newVariant['stock_quantity'];
        if ($stock < $quantity) {
            return [
                'success' => false,
                'message' => "Phiên bản mới chỉ còn {$stock} đôi trong kho, không đủ đáp ứng số lượng bạn đang chọn ({$quantity} đôi)!",
            ];
        }

        $effectivePrice = ($newVariant['sale_price'] && $newVariant['sale_price'] > 0)
            ? (float)$newVariant['sale_price']
            : (float)$newVariant['price'];

        // Xóa biến thể cũ
        unset($_SESSION['cart'][$oldVariantId]);

        // Thêm hoặc gộp vào biến thể mới
        if (isset($_SESSION['cart'][$newVariantId])) {
            $_SESSION['cart'][$newVariantId]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$newVariantId] = [
                'variant_id'   => $newVariant['id'],
                'product_id'   => $newVariant['product_id'],
                'product_name' => $newVariant['product_name'],
                'image_url'    => $newVariant['image_url'] ?? '',
                'sku'          => $newVariant['sku'],
                'size'         => $newVariant['size'],
                'color'        => $newVariant['color'],
                'price'        => $effectivePrice,
                'quantity'     => $quantity,
                'stock'        => $stock,
            ];
        }

        $this->refreshVoucher();

        return [
            'success' => true,
            'message' => "Đã đổi sang Size {$newVariant['size']} - Màu {$newVariant['color']} thành công!",
        ];
    }

    public function remove(int $variantId): void
    {
        unset($_SESSION['cart'][$variantId]);
        $this->refreshVoucher();
    }

    public function clear(): void
    {
        $_SESSION['cart'] = [];
        unset($_SESSION['cart_voucher']);
    }

    public function getTotalAmount(): float
    {
        $total = 0.0;
        foreach ($this->getItems() as $item) {
            $total += (float)$item['price'] * (int)$item['quantity'];
        }
        return $total;
    }

    public function getTotalQuantity(): int
    {
        $count = 0;
        foreach ($this->getItems() as $item) {
            $count += (int)$item['quantity'];
        }
        return $count;
    }

    public function getShippingFee(): float
    {
        $subtotal = $this->getTotalAmount();
        if ($subtotal <= 0 || $subtotal >= 2000000) {
            return 0.0;
        }
        return 30000.0;
    }

    /**
     * Áp dụng mã giảm giá
     */
    public function applyVoucher(string $code, ?int $userId = null): array
    {
        $subtotal = $this->getTotalAmount();
        if ($subtotal <= 0) {
            return ['success' => false, 'message' => 'Giỏ hàng đang trống, không thể áp dụng mã!'];
        }

        $result = $this->voucherModel->validateVoucher($code, $subtotal, $userId);
        if (!$result['valid']) {
            unset($_SESSION['cart_voucher']);
            return ['success' => false, 'message' => $result['message']];
        }

        $_SESSION['cart_voucher'] = [
            'id'             => $result['voucher']['id'],
            'code'           => $result['voucher']['code'],
            'description'    => $result['voucher']['description'] ?? '',
            'discount_type'  => $result['voucher']['discount_type'],
            'discount_value' => $result['voucher']['discount_value'],
            'discount'       => $result['discount'],
        ];

        return [
            'success'  => true,
            'message'  => $result['message'],
            'discount' => $result['discount'],
        ];
    }

    public function removeVoucher(): void
    {
        unset($_SESSION['cart_voucher']);
    }

    public function getVoucher(): ?array
    {
        $this->refreshVoucher();
        return $_SESSION['cart_voucher'] ?? null;
    }

    public function getDiscountAmount(): float
    {
        $voucher = $this->getVoucher();
        return $voucher ? (float)$voucher['discount'] : 0.0;
    }

    public function getFinalTotal(): float
    {
        $subtotal = $this->getTotalAmount();
        $shipping = $this->getShippingFee();
        $discount = $this->getDiscountAmount();
        return max(0.0, ($subtotal + $shipping) - $discount);
    }

    /**
     * Tự động tính lại giá trị giảm giá khi số lượng/sản phẩm trong giỏ thay đổi
     */
    private function refreshVoucher(): void
    {
        if (empty($_SESSION['cart_voucher'])) {
            return;
        }
        $code = $_SESSION['cart_voucher']['code'] ?? '';
        $subtotal = $this->getTotalAmount();
        if ($subtotal <= 0) {
            unset($_SESSION['cart_voucher']);
            return;
        }
        $res = $this->voucherModel->validateVoucher($code, $subtotal);
        if ($res['valid']) {
            $_SESSION['cart_voucher']['discount'] = $res['discount'];
        } else {
            unset($_SESSION['cart_voucher']);
        }
    }

    /**
     * Kiểm tra tồn kho toàn bộ giỏ hàng đối chiếu dữ liệu thực tế
     */
    public function validateInventory(): array
    {
        $errors = [];
        foreach ($this->getItems() as $item) {
            $variant = $this->variantModel->findById((int)$item['variant_id']);
            if (!$variant || $variant['status'] !== 'active') {
                $errors[] = "Sản phẩm '{$item['product_name']}' (Size {$item['size']}) hiện không còn kinh doanh.";
                continue;
            }
            if ((int)$variant['stock_quantity'] < (int)$item['quantity']) {
                $errors[] = "Sản phẩm '{$item['product_name']}' (Size {$item['size']}) chỉ còn {$variant['stock_quantity']} đôi trong kho, không đủ số lượng bạn đặt ({$item['quantity']} đôi).";
            }
        }
        return $errors;
    }
}
