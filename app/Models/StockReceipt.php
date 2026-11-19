<?php

namespace App\Models;

use App\Core\BaseModel;
use InvalidArgumentException;
use RuntimeException;

class StockReceipt extends BaseModel
{
    public function getRecentWithItems(int $limit = 30): array
    {
        $rows = $this->fetchAll(
            "SELECT sr.id, sr.receipt_code, sr.supplier, sr.note, sr.received_at,
                    u.full_name AS created_by_name,
                    sri.quantity, sri.unit_cost, pv.sku, pv.size, pv.color, p.name AS product_name
             FROM (
                SELECT id FROM stock_receipts
                ORDER BY received_at DESC, id DESC
                LIMIT " . max(1, min($limit, 100)) . "
             ) recent
             JOIN stock_receipts sr ON sr.id = recent.id
             JOIN users u ON u.id = sr.created_by
             LEFT JOIN stock_receipt_items sri ON sri.receipt_id = sr.id
             LEFT JOIN product_variants pv ON pv.id = sri.variant_id
             LEFT JOIN products p ON p.id = pv.product_id
             ORDER BY sr.received_at DESC, sr.id DESC, sri.id"
        );

        $receipts = [];
        foreach ($rows as $row) {
            $id = (int)$row['id'];
            if (!isset($receipts[$id])) {
                $receipts[$id] = [
                    'id' => $id,
                    'receipt_code' => $row['receipt_code'],
                    'supplier' => $row['supplier'],
                    'note' => $row['note'],
                    'received_at' => $row['received_at'],
                    'created_by_name' => $row['created_by_name'],
                    'items' => [],
                ];
            }
            if ($row['sku'] !== null) {
                $receipts[$id]['items'][] = $row;
            }
        }

        return array_slice(array_values($receipts), 0, $limit);
    }

    public function receive(int $userId, ?string $supplier, ?string $note, array $items): string
    {
        $normalized = [];
        foreach ($items as $item) {
            $variantId = (int)($item['variant_id'] ?? 0);
            $quantity = filter_var($item['quantity'] ?? null, FILTER_VALIDATE_INT);
            $unitCost = filter_var($item['unit_cost'] ?? 0, FILTER_VALIDATE_FLOAT);
            if ($variantId <= 0 || $quantity === false || $quantity <= 0 || $unitCost === false || $unitCost < 0) {
                throw new InvalidArgumentException('Mỗi dòng nhập cần chọn biến thể, số lượng lớn hơn 0 và giá nhập hợp lệ.');
            }
            $normalized[] = [
                'variant_id' => $variantId,
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
            ];
        }
        if (!$normalized) {
            throw new InvalidArgumentException('Phiếu nhập cần ít nhất một biến thể.');
        }

        $this->beginTransaction();
        try {
            $code = 'PN' . date('ymdHis') . strtoupper(bin2hex(random_bytes(2)));
            $receiptId = $this->insert('stock_receipts', [
                'receipt_code' => $code,
                'created_by' => $userId,
                'supplier' => $supplier ?: null,
                'note' => $note ?: null,
                'received_at' => date('Y-m-d H:i:s'),
            ]);
            $variantModel = new ProductVariant();
            foreach ($normalized as $item) {
                $variant = $this->fetch(
                    "SELECT pv.id FROM product_variants pv
                     JOIN products p ON p.id = pv.product_id
                     WHERE pv.id = :id AND pv.status = 'active' AND p.status = 'active'
                     FOR UPDATE",
                    ['id' => $item['variant_id']]
                );
                if (!$variant) {
                    throw new RuntimeException('Không tìm thấy biến thể đang bán trong phiếu nhập.');
                }

                $this->insert('stock_receipt_items', [
                    'receipt_id' => $receiptId,
                    'variant_id' => $item['variant_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                ]);
                $variantModel->increaseStock($item['variant_id'], $item['quantity']);
            }
            $this->commit();
            return $code;
        } catch (\Throwable $exception) {
            if ($this->inTransaction()) {
                $this->rollBack();
            }
            throw $exception;
        }
    }
}
