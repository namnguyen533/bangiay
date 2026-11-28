<?php

namespace App\Core;

class OrderMailer
{
    public static function send(string $to, string $subject, string $html): bool
    {
        $to = trim($to);
        if ($to === '' || filter_var($to, FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }

        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'From: Apple Meo Meo <no-reply@applemeomeo.vn>',
            'X-Mailer: PHP/' . PHP_VERSION,
        ];

        return @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $html, implode("\r\n", $headers));
    }

    public static function sendToUser(array $order, string $subject, string $message, ?string $reason = null): bool
    {
        $email = trim((string)($order['user_email'] ?? ''));
        $reasonHtml = $reason !== null && trim($reason) !== ''
            ? '<p><strong>Lý do:</strong> ' . nl2br(htmlspecialchars(trim($reason), ENT_QUOTES, 'UTF-8')) . '</p>'
            : '';

        return self::send($email, $subject, self::wrap(
            htmlspecialchars($order['recipient_name'] ?? 'Khách hàng', ENT_QUOTES, 'UTF-8'),
            $order,
            $message,
            $reasonHtml
        ));
    }

    public static function sendToAdmins(array $order, string $subject, string $message, ?string $reason = null): void
    {
        $db = Database::getConnection();
        $emails = $db->query(
            "SELECT DISTINCT u.email
             FROM users u
             JOIN user_roles ur ON ur.user_id = u.id
             JOIN roles r ON r.id = ur.role_id
             WHERE r.name IN ('Admin', 'Super Admin') AND u.status = 'active'"
        )->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($emails as $email) {
            self::send((string)$email, $subject, self::wrap('Quản trị viên', $order, $message,
                $reason !== null && trim($reason) !== ''
                    ? '<p><strong>Lý do:</strong> ' . nl2br(htmlspecialchars(trim($reason), ENT_QUOTES, 'UTF-8')) . '</p>'
                    : ''
            ));
        }
    }

    private static function wrap(string $name, array $order, string $message, string $extra): string
    {
        $code = htmlspecialchars((string)($order['order_code'] ?? ''), ENT_QUOTES, 'UTF-8');
        $status = htmlspecialchars(order_status_label((string)($order['order_status'] ?? 'pending')), ENT_QUOTES, 'UTF-8');

        return '<div style="font-family:Arial,sans-serif;color:#222">'
            . '<h2>Xin chào ' . $name . '</h2>'
            . '<p>' . $message . '</p>'
            . '<p><strong>Mã đơn hàng:</strong> ' . $code . '</p>'
            . '<p><strong>Trạng thái:</strong> ' . $status . '</p>'
            . $extra
            . '<hr><p><small>Apple Meo Meo Shoes Store</small></p>'
            . '</div>';
    }
}
