<?php

namespace App\Models;

class Cart
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    public function getItems(): array
    {
        return $_SESSION['cart'] ?? [];
    }

    public function add(int $variantId, int $quantity = 1): bool
    {
        $variantModel = new ProductVariant();
        $variant = $variantModel->findById($variantId);

        if (!$variant) {
            return false;
        }

        $effectivePrice = ($variant['sale_price'] && $variant['sale_price'] > 0)
            ? (float)$variant['sale_price']
            : (float)$variant['price'];

        if (isset($_SESSION['cart'][$variantId])) {
            $_SESSION['cart'][$variantId]['quantity'] += $quantity;
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
                'stock'        => (int)$variant['stock_quantity'],
            ];
        }

        return true;
    }

    public function update(int $variantId, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->remove($variantId);
        } elseif (isset($_SESSION['cart'][$variantId])) {
            $_SESSION['cart'][$variantId]['quantity'] = $quantity;
        }
    }

    public function remove(int $variantId): void
    {
        unset($_SESSION['cart'][$variantId]);
    }

    public function clear(): void
    {
        $_SESSION['cart'] = [];
    }

    public function getTotalAmount(): float
    {
        $total = 0;
        foreach ($this->getItems() as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    public function getTotalQuantity(): int
    {
        $count = 0;
        foreach ($this->getItems() as $item) {
            $count += $item['quantity'];
        }
        return $count;
    }
}
