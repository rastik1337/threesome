<?php

declare(strict_types=1);

namespace App\Services;

use Imagick;
use InvalidArgumentException;
use RuntimeException;

class ImageService
{
    private const ALLOWED_FORMATS = ['JPEG', 'JPG', 'PNG', 'GIF', 'BMP', 'TIFF'];
    private const TARGET_WIDTH = 800;
    private const JPEG_QUALITY = 90;

    public static function validateAndConvert(array $file, string $subfolder): string
    {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            throw new InvalidArgumentException('Chyba při nahrávání souboru.');
        }

        if (!isset($file['tmp_name']) || (!is_uploaded_file($file['tmp_name']) && php_sapi_name() !== 'cli')) {
            throw new InvalidArgumentException('Neplatný nahraný soubor.');
        }

        try {
            $image = new Imagick($file['tmp_name']);
        } catch (\Exception $_e) {
            throw new InvalidArgumentException('Nahraný soubor není platný obrázek.');
        }

        $format = strtoupper($image->getImageFormat());
        if (!in_array($format, self::ALLOWED_FORMATS, true)) {
            $image->clear();
            $image->destroy();
            throw new InvalidArgumentException('Nepovolený formát. Podporovány jsou pouze JPEG, PNG, GIF, BMP a TIFF.');
        }

        $image->setIteratorIndex(0);
        $image->scaleImage(self::TARGET_WIDTH, 0);
        $image->stripImage();
        $image->setImageFormat('jpeg');
        $image->setImageCompressionQuality(self::JPEG_QUALITY);

        $uploadDir = __DIR__ . '/../../public/uploads/' . trim($subfolder, '/');
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
            $image->clear();
            $image->destroy();
            throw new RuntimeException('Nepodařilo se vytvořit cílový adresář pro nahrání.');
        }

        $filename = bin2hex(random_bytes(16)) . '.jpg';
        $fullPath = $uploadDir . '/' . $filename;

        if (!$image->writeImage($fullPath)) {
            $image->clear();
            $image->destroy();
            throw new RuntimeException('Nepodařilo se uložit zpracovaný obrázek.');
        }

        $image->clear();
        $image->destroy();

        return trim($subfolder, '/') . '/' . $filename;
    }

    public static function deleteFile(string $relativePath): void
    {
        $baseDir = realpath(__DIR__ . '/../../public/uploads');
        if ($baseDir === false) {
            return;
        }

        $fullPath = realpath($baseDir . '/' . $relativePath);
        if ($fullPath !== false && str_starts_with($fullPath, $baseDir) && is_file($fullPath)) {
            unlink($fullPath);
        }
    }
}
