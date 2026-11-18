<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;

/**
 * Base class for the administration area.
 * Every controller that extends it is automatically restricted to administrators.
 */
abstract class AdminController extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->requireAdmin();
    }

    /**
     * Renders an admin view and tells the navigation which section is active.
     */
    protected function renderAdmin(string $view, string $activeSection, array $data = [], int $statusCode = 200): void
    {
        $this->render($view, array_merge($data, ['activeSection' => $activeSection]), $statusCode);
    }
}
