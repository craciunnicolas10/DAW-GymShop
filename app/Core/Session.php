<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Small wrapper around $_SESSION with support for one-time "flash" messages.
 */
final class Session
{
    private const FLASH_KEY = '_flashMessages';

    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Generates a new session id (prevents session fixation after login/logout).
     */
    public function regenerate(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    /**
     * Stores a message that is displayed only once, on the next page.
     *
     * @param string $type success | error | info
     */
    public function flash(string $type, string $message): void
    {
        $_SESSION[self::FLASH_KEY][] = ['type' => $type, 'message' => $message];
    }

    /**
     * Returns the flash messages and removes them from the session.
     *
     * @return array<int, array{type: string, message: string}>
     */
    public function pullFlashMessages(): array
    {
        $messages = $_SESSION[self::FLASH_KEY] ?? [];
        unset($_SESSION[self::FLASH_KEY]);

        return $messages;
    }
}
