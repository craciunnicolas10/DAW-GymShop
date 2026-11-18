<?php

declare(strict_types=1);

namespace App\Core;

use App\Exceptions\NotFoundException;

/**
 * Maps URLs to controller actions.
 * Example: $router->get('/products/{slug}', [ProductController::class, 'show']);
 */
final class Router
{
    /** @var array<int, array{method: string, path: string, pattern: string, handler: array}> */
    private array $routes = [];

    public function get(string $path, array $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, array $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function addRoute(string $method, string $path, array $handler): void
    {
        // "{slug}" becomes a named regex group that matches one URL segment.
        $pattern = preg_replace('#\{([a-zA-Z]+)\}#', '(?P<$1>[^/]+)', $path);

        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'pattern' => '#^' . $pattern . '$#',
            'handler' => $handler,
        ];
    }

    /**
     * Finds the route for the given method and path.
     *
     * @return array{handler: array, params: array<string, string>}
     * @throws NotFoundException when no route matches
     */
    public function match(string $method, string $path): array
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== strtoupper($method)) {
                continue;
            }

            if (preg_match($route['pattern'], $path, $matches) === 1) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                return ['handler' => $route['handler'], 'params' => array_map('urldecode', $params)];
            }
        }

        throw new NotFoundException();
    }

    /**
     * Runs the controller action that matches the current request.
     */
    public function dispatch(Request $request): void
    {
        $route = $this->match($request->getMethod(), $request->getPath());
        [$controllerClass, $action] = $route['handler'];

        $controller = new $controllerClass($request);
        $controller->$action(...array_values($route['params']));
    }
}
