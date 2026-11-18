<?php

declare(strict_types=1);

/*
 * Front controller: every request (except static files) arrives here
 * thanks to the rewrite rules from .htaccess.
 */

use App\Core\Config;
use App\Core\ErrorHandler;
use App\Core\Request;
use App\Core\Router;

require dirname(__DIR__) . '/app/bootstrap.php';

$router = new Router();
require BASE_PATH . '/app/routes.php';

$request = Request::fromGlobals((string) Config::get('app.baseUrl', ''));

try {
    $router->dispatch($request);
} catch (Throwable $exception) {
    ErrorHandler::handle($exception, $request);
}
