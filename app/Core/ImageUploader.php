<?php

namespace App\Core;

use RuntimeException;

class ImageUploader
{
    private const MAX_IMAGE_COUNT = 8;
    private const MAX_IMAGE_SIZE = 5242880;
    private const MIME_EXTENSIONS = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    public static function storeMany(array $files): array
    {
        if (!isset($files['name']) || !is_array($files['name'])) {
            return [];
        }

        $paths = [];
        $count = count($files['name']);
        if ($count > self::MAX_IMAGE_COUNT) {
            throw new RuntimeException('Mỗi sản phẩm được tải lên tối đa 8 ảnh.');
        }

        $directory = ROOT_PATH . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'products';
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new RuntimeException('Không thể tạo thư mục lưu ảnh sản phẩm.');
        }

        try {
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            for ($index = 0; $index < $count; $index++) {
                $error = $files['error'][$index] ?? UPLOAD_ERR_NO_FILE;
                if ($error === UPLOAD_ERR_NO_FILE) {
                    continue;
                }
                if ($error !== UPLOAD_ERR_OK) {
                    throw new RuntimeException('Tải ảnh lên không thành công.');
                }

                $temporaryPath = $files['tmp_name'][$index] ?? '';
                $size = (int)($files['size'][$index] ?? 0);
                if ($size <= 0 || $size > self::MAX_IMAGE_SIZE || !is_uploaded_file($temporaryPath)) {
                    throw new RuntimeException('Ảnh không hợp lệ hoặc vượt quá 5 MB.');
                }

                $mime = $finfo->file($temporaryPath);
                if (!isset(self::MIME_EXTENSIONS[$mime]) || @getimagesize($temporaryPath) === false) {
                    throw new RuntimeException('Chỉ chấp nhận ảnh JPG, PNG hoặc WebP hợp lệ.');
                }

                $filename = bin2hex(random_bytes(16)) . '.' . self::MIME_EXTENSIONS[$mime];
                if (!move_uploaded_file($temporaryPath, $directory . DIRECTORY_SEPARATOR . $filename)) {
                    throw new RuntimeException('Không thể lưu ảnh sản phẩm.');
                }

                $paths[] = 'uploads/products/' . $filename;
            }
        } catch (RuntimeException $exception) {
            foreach ($paths as $path) {
                self::delete($path);
            }
            throw $exception;
        }

        return $paths;
    }

    public static function delete(string $relativePath): void
    {
        $prefix = 'uploads/products/';
        if (strpos($relativePath, $prefix) !== 0 || basename($relativePath) !== substr($relativePath, strlen($prefix))) {
            return;
        }

        $file = ROOT_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
        if (is_file($file)) {
            unlink($file);
        }
    }
}
