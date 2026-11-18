<?php

declare(strict_types=1);

namespace App\Core;

use App\Exceptions\AuthorizationException;

/**
 * Base class for all controllers. Offers helpers for rendering views,
 * redirecting and checking the access rights of the current user.
 */
abstract class Controller
{
    protected Session $session;
    protected Auth $auth;

    public function __construct(protected Request $request)
    {
        $this->session = new Session();
        $this->auth = new Auth($this->session);
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

    /**
     * Authentication check: guests are sent to the login page.
     */
    protected function requireLogin(): void
    {
        if (!$this->auth->check()) {
            $this->session->set('intendedPath', $this->request->getPath());
            $this->session->flash('info', 'Te rugăm să te autentifici pentru a continua.');
            $this->redirect('/login');
        }
    }

    /**
     * Authorization check: only users with the "admin" role may continue.
     *
     * @throws AuthorizationException
     */
    protected function requireAdmin(): void
    {
        $this->requireLogin();

        if (!$this->auth->isAdmin()) {
            throw new AuthorizationException();
        }
    }
}
