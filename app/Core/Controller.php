<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Base class for all controllers. Offers helpers for rendering views and redirecting.
 */
abstract class Controller
{
    protected Session $session;

    public function __construct(protected Request $request)
    {
        $this->session = new Session();
    }

    protected function render(string $view, array $data = [], int $statusCode = 200): void
    {
        http_response_code($statusCode);
        echo View::render($view, $data);
    }

    protected function redirect(string $path): never
    {
        header('Location: ' . url($path));
        exit;
    }
}
