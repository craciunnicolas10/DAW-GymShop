<?php

declare(strict_types=1);

namespace App\Interfaces;

/**
 * Contract for recording user activity. The application only depends on this interface,
 * so the file logger could be replaced (e.g. with a database logger) without changing controllers.
 */
interface LoggerInterface
{
    public const LEVEL_INFO = 'INFO';
    public const LEVEL_WARNING = 'WARNING';
    public const LEVEL_ERROR = 'ERROR';

    /**
     * @param array<string, mixed> $context extra details; the keys "user" and "ip" are stored in their own columns
     */
    public function log(string $level, string $action, array $context = []): void;

    /**
     * Returns the newest log entries first.
     *
     * @return array<int, array{date: string, level: string, user: string, ip: string, action: string, details: string}>
     */
    public function getRecentEntries(int $limit = 200, string $levelFilter = '', string $search = ''): array;
}
