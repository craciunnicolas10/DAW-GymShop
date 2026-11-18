<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Thrown when a page, product or order does not exist.
 */
class NotFoundException extends AppException
{
    protected int $statusCode = 404;

    public function __construct(string $message = 'Pagina căutată nu există.')
    {
        parent::__construct($message);
    }
}
