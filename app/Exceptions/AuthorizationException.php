<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Thrown when the logged in user does not have the rights to access a resource.
 */
class AuthorizationException extends AppException
{
    protected int $statusCode = 403;

    public function __construct(string $message = 'Nu ai drepturi pentru a accesa această pagină.')
    {
        parent::__construct($message);
    }
}
