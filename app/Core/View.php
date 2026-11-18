<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

/**
 * Renders PHP templates from app/Views and wraps them in a layout.
 */
final class View
{
    public static function render(string $view, array $data = [], ?string $layout = 'layouts/main'): string
    {
        $content = self::renderFile($view, $data);

        if ($layout === null) {
            return $content;
        }

        return self::renderFile($layout, array_merge(self::getSharedData(), $data, ['content' => $content]));
    }

    /**
     * Data needed by the layout on every page (header, menu, flash messages).
     * It is computed at render time so flash messages are read only when they are displayed.
     */
    private static function getSharedData(): array
    {
        $session = new Session();

        return [
            'appName' => Config::get('app.name', 'GymShop'),
            'currentUser' => (new Auth($session))->user(),
            'flashMessages' => $session->pullFlashMessages(),
        ];
    }

    public static function renderFile(string $view, array $data = []): string
    {
        $file = BASE_PATH . '/app/Views/' . $view . '.php';

        if (!is_file($file)) {
            throw new RuntimeException("View \"{$view}\" does not exist.");
        }

        extract($data, EXTR_SKIP);
        ob_start();
        require $file;

        return (string) ob_get_clean();
    }
}
