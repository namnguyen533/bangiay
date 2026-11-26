<?php

namespace App\Models;

use App\Core\BaseModel;

class Voucher extends BaseModel
{
    public function getAll(): array
    {
        $sql = "SELECT * FROM vouchers ORDER BY id DESC";
        return $this->fetchAll($sql);
    }

    public function getActiveVouchers(): array
    {
        $now = date('Y-m-d H:i:s');
        $sql = "SELECT * FROM vouchers 
                WHERE status = 'active' 
                  AND (starts_at IS NULL OR starts_at <= :now1) 
                  AND (ends_at IS NULL OR ends_at >= :now2)
                  AND (usage_limit IS NULL OR used_count < usage_limit)
                ORDER BY id DESC";
        return $this->fetchAll($sql, ['now1' => $now, 'now2' => $now]);
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM vouchers WHERE id = :id";
        return $this->fetch($sql, ['id' => $id]);
    }

    public function findByCode(string $code): ?array
    {
        $sql = "SELECT * FROM vouchers WHERE UPPER(TRIM(code)) = UPPER(TRIM(:code))";
        return $this->fetch($sql, ['code' => $code]);
    }

    public function validateVoucher(string $code, float $orderAmount, ?int $userId = null): array
    {
        $voucher = $this->findByCode($code);
        if (!$voucher) {
            return [
                'valid'    => false,
                'discount' => 0,
                'message'  => 'Mã giảm giá không tồn tại!',
                'voucher'  => null,
            ];
        }

        if ($voucher['status'] !== 'active') {
            return [
                'valid'    => false,
                'discount' => 0,
                'message'  => 'Mã giảm giá hiện đang bị tạm khóa!',
                'voucher'  => null,
            ];
        }

        $now = time();
        if (!empty($voucher['starts_at']) && strtotime($voucher['starts_at']) > $now) {
            return [
                'valid'    => false,
                'discount' => 0,
                'message'  => 'Mã giảm giá chưa đến ngày áp dụng (bắt đầu từ: ' . date('d/m/Y', strtotime($voucher['starts_at'])) . ')!',
                'voucher'  => null,
            ];
        }

        if (!empty($voucher['ends_at']) && strtotime($voucher['ends_at']) < $now) {
            return [
                'valid'    => false,
                'discount' => 0,
                'message'  => 'Mã giảm giá đã hết hạn sử dụng!',
                'voucher'  => null,
            ];
        }

        if ($voucher['usage_limit'] !== null && (int)$voucher['used_count'] >= (int)$voucher['usage_limit']) {
            return [
                'valid'    => false,
                'discount' => 0,
                'message'  => 'Mã giảm giá đã hết lượt sử dụng!',
                'voucher'  => null,
            ];
        }

        $minOrder = (float)$voucher['min_order_value'];
        if ($orderAmount < $minOrder) {
            return [
                'valid'    => false,
                'discount' => 0,
                'message'  => 'Đơn hàng cần tối thiểu ' . number_format($minOrder, 0, ',', '.') . ' ₫ để dùng mã này!',
                'voucher'  => null,
            ];
        }

        $discount = 0.0;
        if ($voucher['discount_type'] === 'percent') {
            $discount = $orderAmount * ((float)$voucher['discount_value'] / 100);
            if (!empty($voucher['max_discount']) && $discount > (float)$voucher['max_discount']) {
                $discount = (float)$voucher['max_discount'];
            }
        } else {
            $discount = min((float)$voucher['discount_value'], $orderAmount);
        }

        return [
            'valid'    => true,
            'discount' => $discount,
            'message'  => 'Áp dụng mã giảm giá thành công! Tiết kiệm ' . number_format($discount, 0, ',', '.') . ' ₫',
            'voucher'  => $voucher,
        ];
    }

    public function recordUsage(int $voucherId): void
    {
        $sql = "UPDATE vouchers SET used_count = used_count + 1 WHERE id = :id";
        $this->query($sql, ['id' => $voucherId]);
    }

    public function refundUsage(int $voucherId): void
    {
        $sql = "UPDATE vouchers SET used_count = GREATEST(0, used_count - 1) WHERE id = :id";
        $this->query($sql, ['id' => $voucherId]);
    }

    public function createVoucher(array $data): int
    {
        return $this->insert('vouchers', $data);
    }

    public function updateVoucher(int $id, array $data): bool
    {
        return $this->update('vouchers', $data, 'id = :id', ['id' => $id]);
    }

    public function deleteVoucher(int $id): bool
    {
        return $this->delete('vouchers', 'id = :id', ['id' => $id]);
    }
}
