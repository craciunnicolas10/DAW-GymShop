<?php

declare(strict_types=1);

namespace App\Core;

use App\Exceptions\AppException;
use App\Interfaces\LoggerInterface;
use App\Services\FileLogger;
use PDOException;
use Throwable;

/**
 * Converts any uncaught exception into a friendly error page and writes it in the log.
 */
final class ErrorHandler
{
    public static function handle(Throwable $exception, ?Request $request = null): void
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

        self::log($exception, $statusCode, $request);

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

    private static function log(Throwable $exception, int $statusCode, ?Request $request): void
    {
        $user = (new Auth(new Session()))->user();
        $context = [
            'user' => $user !== null ? "{$user['email']} (#{$user['id']})" : 'guest',
            'ip' => $request?->getIpAddress() ?? '-',
            'method' => $request?->getMethod(),
            'path' => $request?->getPath(),
        ];

        if ($statusCode >= 500) {
            $context['exception'] = get_class($exception);
            $context['message'] = $exception->getMessage();
        }

        (new FileLogger((string) Config::get('logging.file')))->log(
            $statusCode >= 500 ? LoggerInterface::LEVEL_ERROR : LoggerInterface::LEVEL_WARNING,
            'HTTP_' . $statusCode,
            $context
        );
    }
}
