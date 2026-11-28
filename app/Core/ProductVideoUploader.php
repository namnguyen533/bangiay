<?php

namespace App\Core;

use RuntimeException;

class ProductVideoUploader
{
    private const MAX_VIDEO_SIZE = 52428800;

    public static function store(array $file): ?string
    {
        $error = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($error === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($error !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Không tải được video. Video MP4 không được vượt quá 50 MB.');
        }

        $temporaryPath = $file['tmp_name'] ?? '';
        $size = (int)($file['size'] ?? 0);
        if ($size <= 0 || $size > self::MAX_VIDEO_SIZE || !is_uploaded_file($temporaryPath)) {
            throw new RuntimeException('Video không hợp lệ hoặc vượt quá 50 MB.');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        if ($finfo->file($temporaryPath) !== 'video/mp4') {
            throw new RuntimeException('Chỉ chấp nhận video MP4 hợp lệ.');
        }

        $directory = ROOT_PATH . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR . 'videos';
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new RuntimeException('Không thể tạo thư mục lưu video sản phẩm.');
        }

        $filename = bin2hex(random_bytes(16)) . '.mp4';
        if (!move_uploaded_file($temporaryPath, $directory . DIRECTORY_SEPARATOR . $filename)) {
            throw new RuntimeException('Không thể lưu video sản phẩm.');
        }

        return 'uploads/products/videos/' . $filename;
    }

    public static function delete(string $relativePath): void
    {
        $prefix = 'uploads/products/videos/';
        if (strpos($relativePath, $prefix) !== 0 || basename($relativePath) !== substr($relativePath, strlen($prefix))) {
            return;
        }

        $file = ROOT_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
        if (is_file($file)) {
            unlink($file);
        }
    }
}
