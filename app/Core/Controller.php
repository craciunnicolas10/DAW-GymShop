<?php

declare(strict_types=1);

namespace App\Core;

use App\Exceptions\AuthorizationException;
use App\Interfaces\LoggerInterface;
use App\Services\FileLogger;

/**
 * Base class for all controllers. Offers helpers for rendering views,
 * redirecting, logging and checking the access rights of the current user.
 */
abstract class Controller
{
    protected Session $session;
    protected Auth $auth;
    protected LoggerInterface $logger;

    public function __construct(protected Request $request)
    {
        $this->session = new Session();
        $this->auth = new Auth($this->session);
        $this->logger = new FileLogger((string) Config::get('logging.file'));
    }

    /**
     * Writes a user action in the activity log, together with the user and the IP address.
     */
    protected function logActivity(string $action, array $details = [], string $level = LoggerInterface::LEVEL_INFO): void
    {
        $user = $this->auth->user();

        $this->logger->log($level, $action, [
            'user' => $user !== null ? "{$user['email']} (#{$user['id']})" : 'guest',
            'ip' => $this->request->getIpAddress(),
        ] + $details);
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
            $this->logActivity('ACCESS_DENIED', ['path' => $this->request->getPath()], LoggerInterface::LEVEL_WARNING);
            throw new AuthorizationException();
        }
    }
}
