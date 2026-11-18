<?php

declare(strict_types=1);

use App\Core\Config;

/*
 * Global helper functions used mostly inside the views.
 */

if (!function_exists('e')) {
    /**
     * Escapes text before it is printed in HTML (prevents XSS).
     */
    function e(string|int|float|null $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('url')) {
    /**
     * Builds an application URL, e.g. url('/products') => "/gymshop/products".
     */
    function url(string $path = '/'): string
    {
        $baseUrl = rtrim((string) Config::get('app.baseUrl', ''), '/');

        return $baseUrl . '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    /**
     * Builds the URL of a public file (css, js, images).
     */
    function asset(string $path): string
    {
        return url($path);
    }
}

if (!function_exists('formatPrice')) {
    function formatPrice(float $amount): string
    {
        return number_format($amount, 2, ',', '.') . ' lei';
    }
}
