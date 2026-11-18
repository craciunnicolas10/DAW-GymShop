<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Wraps the data of the current HTTP request (method, path, GET/POST data, files).
 */
final class Request
{
    public function __construct(
        private string $method,
        private string $path,
        private array $query = [],
        private array $body = [],
        private array $files = [],
        private string $ipAddress = 'unknown'
    ) {
    }

    public static function fromGlobals(string $basePath): self
    {
        $uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

        if ($basePath !== '' && str_starts_with($uriPath, $basePath)) {
            $uriPath = substr($uriPath, strlen($basePath));
        }

        return new self(
            strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET'),
            self::normalizePath($uriPath),
            $_GET,
            $_POST,
            $_FILES,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        );
    }

    public static function normalizePath(string $path): string
    {
        $path = '/' . trim($path, '/');

        // Requests like /index.php/products are treated as /products.
        if (str_starts_with($path, '/index.php')) {
            $path = '/' . ltrim(substr($path, strlen('/index.php')), '/');
        }

        return $path;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    /**
     * Returns a value from the query string (?key=value), trimmed.
     */
    public function query(string $key, string $default = ''): string
    {
        $value = $this->query[$key] ?? $default;

        return is_string($value) ? trim($value) : $default;
    }

    /**
     * Returns a value sent through a POST form, trimmed.
     */
    public function input(string $key, string $default = ''): string
    {
        $value = $this->body[$key] ?? $default;

        return is_string($value) ? trim($value) : $default;
    }

    /**
     * Returns all POST values that are plain strings.
     *
     * @return array<string, string>
     */
    public function all(): array
    {
        $values = [];
        foreach ($this->body as $key => $value) {
            if (is_string($value)) {
                $values[$key] = trim($value);
            }
        }

        return $values;
    }

    /**
     * Returns the uploaded file information, or null when no file was sent.
     */
    public function file(string $key): ?array
    {
        $file = $this->files[$key] ?? null;

        if (!is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        return $file;
    }
}
