<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Wraps low level PDO errors so that technical details are never shown to visitors.
 */
class DatabaseException extends AppException
{
    protected int $statusCode = 500;
}
