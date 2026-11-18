<?php

declare(strict_types=1);

namespace App\Core;

use App\Exceptions\AppException;
use PDOException;
use Throwable;

/**
 * Converts any uncaught exception into a friendly error page.
 */
final class ErrorHandler
{
    public static function handle(Throwable $exception): void
    {
        if ($exception instanceof AppException) {
            $statusCode = $exception->getStatusCode();
            $message = $exception->getMessage();
        } elseif ($exception instanceof PDOException) {
            $statusCode = 500;
            $message = 'A apărut o eroare la baza de date. Te rugăm să încerci din nou.';
        } else {
            $statusCode = 500;
            $message = 'A apărut o eroare neașteptată.';
        }

        $technicalDetails = Config::get('app.debug', false)
            ? get_class($exception) . ': ' . $exception->getMessage() . ' in ' . $exception->getFile() . ':' . $exception->getLine()
            : null;

        http_response_code($statusCode);

        echo View::render('errors/error', [
            'pageTitle' => 'Eroare ' . $statusCode,
            'statusCode' => $statusCode,
            'message' => $message,
            'technicalDetails' => $statusCode >= 500 ? $technicalDetails : null,
        ]);
    }
}
