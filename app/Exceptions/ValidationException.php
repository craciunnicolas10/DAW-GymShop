<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Thrown when user input is invalid. Keeps the list of errors per field
 * so the form can be displayed again with the messages.
 */
class ValidationException extends AppException
{
    protected int $statusCode = 422;

    /**
     * @param array<string, string> $errors field name => error message
     */
    public function __construct(private array $errors, string $message = 'Datele introduse nu sunt valide.')
    {
        parent::__construct($message);
    }

    /**
     * @return array<string, string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
