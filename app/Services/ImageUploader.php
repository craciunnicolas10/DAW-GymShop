<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Config;
use App\Exceptions\ValidationException;
use finfo;

/**
 * Validates and saves product images uploaded by the administrator.
 * Checks: upload errors, maximum size, allowed extension, real MIME type and image content.
 */
final class ImageUploader
{
    private const MIME_TYPES = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'webp' => 'image/webp',
    ];

    /**
     * @param string[] $allowedExtensions
     */
    public function __construct(
        private string $directory,
        private string $publicPath,
        private int $maxSizeBytes,
        private array $allowedExtensions
    ) {
    }

    public static function fromConfig(): self
    {
        return new self(
            (string) Config::get('uploads.directory'),
            (string) Config::get('uploads.publicPath'),
            (int) Config::get('uploads.maxSizeBytes'),
            (array) Config::get('uploads.allowedExtensions')
        );
    }

    /**
     * Saves the uploaded file under a random name and returns its public path.
     *
     * @throws ValidationException
     */
    public function upload(array $file): string
    {
        $extension = $this->validate($file);

        if (!is_dir($this->directory) && !mkdir($this->directory, 0755, true) && !is_dir($this->directory)) {
            throw new ValidationException(['image' => 'Directorul pentru imagini nu poate fi creat.']);
        }

        $fileName = bin2hex(random_bytes(16)) . '.' . $extension;

        if (!move_uploaded_file($file['tmp_name'], $this->directory . '/' . $fileName)) {
            throw new ValidationException(['image' => 'Imaginea nu a putut fi salvată.']);
        }

        return $this->publicPath . '/' . $fileName;
    }

    /**
     * @return string the normalized file extension
     * @throws ValidationException
     */
    public function validate(array $file): string
    {
        $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;

        if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
            throw new ValidationException(['image' => $this->getTooLargeMessage()]);
        }

        if ($error !== UPLOAD_ERR_OK) {
            throw new ValidationException(['image' => 'Încărcarea imaginii a eșuat. Încearcă din nou.']);
        }

        if ((int) $file['size'] > $this->maxSizeBytes) {
            throw new ValidationException(['image' => $this->getTooLargeMessage()]);
        }

        $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $this->allowedExtensions, true) || !isset(self::MIME_TYPES[$extension])) {
            throw new ValidationException([
                'image' => 'Sunt acceptate doar imagini de tip: ' . implode(', ', $this->allowedExtensions) . '.',
            ]);
        }

        $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);

        if ($mimeType !== self::MIME_TYPES[$extension] || @getimagesize($file['tmp_name']) === false) {
            throw new ValidationException(['image' => 'Fișierul încărcat nu este o imagine validă.']);
        }

        return $extension;
    }

    /**
     * Deletes an image previously uploaded (images shipped with the site are never deleted).
     */
    public function delete(?string $publicPath): void
    {
        if ($publicPath === null || !str_starts_with($publicPath, $this->publicPath . '/')) {
            return;
        }

        $file = $this->directory . '/' . basename($publicPath);
        if (is_file($file)) {
            unlink($file);
        }
    }

    private function getTooLargeMessage(): string
    {
        return 'Imaginea depășește dimensiunea maximă de ' . round($this->maxSizeBytes / 1024 / 1024) . ' MB.';
    }
}
