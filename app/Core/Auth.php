<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\User;

/**
 * Keeps track of the logged in user. Only the id, name, email and role
 * are kept in the session, never the password hash.
 */
final class Auth
{
    private const SESSION_KEY = 'authUser';

    public function __construct(private Session $session)
    {
    }

    public function login(User $user): void
    {
        // A new session id after login prevents session fixation attacks.
        $this->session->regenerate();
        $this->session->set(self::SESSION_KEY, [
            'id' => $user->getId(),
            'name' => $user->getFullName(),
            'email' => $user->getEmail(),
            'role' => $user->getRole(),
        ]);
    }

    public function logout(): void
    {
        $this->session->remove(self::SESSION_KEY);
        $this->session->regenerate();
    }

    public function check(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array{id: int, name: string, email: string, role: string}|null
     */
    public function user(): ?array
    {
        $user = $this->session->get(self::SESSION_KEY);

        return is_array($user) ? $user : null;
    }

    public function id(): ?int
    {
        return $this->user()['id'] ?? null;
    }

    public function hasRole(string $role): bool
    {
        return ($this->user()['role'] ?? null) === $role;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(User::ROLE_ADMIN);
    }
}
