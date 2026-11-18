<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\LoggerInterface;

/**
 * Writes user activity to a plain text file, one line per action:
 *
 * 2026-11-18 10:22:01 | INFO | client@gymshop.test (#2) | 127.0.0.1 | ORDER_PLACED | {"orderId":3,"total":219.99}
 */
final class FileLogger implements LoggerInterface
{
    private const SEPARATOR = ' | ';
    private const COLUMNS = ['date', 'level', 'user', 'ip', 'action', 'details'];

    public function __construct(private string $filePath)
    {
    }

    public function log(string $level, string $action, array $context = []): void
    {
        $user = (string) ($context['user'] ?? 'guest');
        $ipAddress = (string) ($context['ip'] ?? '-');
        unset($context['user'], $context['ip']);

        $details = $context === []
            ? '{}'
            : (string) json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);

        $line = implode(self::SEPARATOR, [
            date('Y-m-d H:i:s'),
            strtoupper($this->sanitize($level)),
            $this->sanitize($user),
            $this->sanitize($ipAddress),
            strtoupper($this->sanitize($action)),
            $details,
        ]);

        $directory = dirname($this->filePath);
        if (!is_dir($directory)) {
            @mkdir($directory, 0775, true);
        }

        // LOCK_EX prevents two requests from writing in the same time and mixing the lines.
        @file_put_contents($this->filePath, $line . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    public function getRecentEntries(int $limit = 200, string $levelFilter = '', string $search = ''): array
    {
        if (!is_file($this->filePath)) {
            return [];
        }

        $lines = file($this->filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
        $entries = [];

        foreach (array_reverse($lines) as $line) {
            $entry = $this->parseLine($line);

            if ($entry === null) {
                continue;
            }
            if ($levelFilter !== '' && $entry['level'] !== strtoupper($levelFilter)) {
                continue;
            }
            if ($search !== '' && mb_stripos($line, $search) === false) {
                continue;
            }

            $entries[] = $entry;
            if (count($entries) >= $limit) {
                break;
            }
        }

        return $entries;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    /**
     * @return array{date: string, level: string, user: string, ip: string, action: string, details: string}|null
     */
    private function parseLine(string $line): ?array
    {
        $parts = explode(self::SEPARATOR, $line, count(self::COLUMNS));

        if (count($parts) !== count(self::COLUMNS)) {
            return null;
        }

        return array_combine(self::COLUMNS, $parts);
    }

    /**
     * Removes new lines and separators so a user can not forge fake log lines (log injection).
     */
    private function sanitize(string $value): string
    {
        return str_replace(["\r", "\n", '|'], [' ', ' ', '/'], $value);
    }
}
