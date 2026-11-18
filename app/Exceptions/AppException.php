<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

/**
 * Base class for all application exceptions.
 * Every subclass knows which HTTP status code should be sent to the browser.
 */
abstract class AppException extends Exception
{
    protected int $statusCode = 500;

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
