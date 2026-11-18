<?php

declare(strict_types=1);

namespace App\Core;

/**
 * String helpers.
 */
final class Str
{
    private const ROMANIAN_CHARACTERS = [
        'ă' => 'a', 'â' => 'a', 'î' => 'i', 'ș' => 's', 'ş' => 's', 'ț' => 't', 'ţ' => 't',
        'Ă' => 'a', 'Â' => 'a', 'Î' => 'i', 'Ș' => 's', 'Ş' => 's', 'Ț' => 't', 'Ţ' => 't',
    ];

    /**
     * Converts a text into a URL friendly "slug".
     * Example: "Creatină Monohidrat 300g" => "creatina-monohidrat-300g"
     */
    public static function slug(string $text): string
    {
        $text = strtr($text, self::ROMANIAN_CHARACTERS);
        $text = mb_strtolower($text, 'UTF-8');
        $text = (string) preg_replace('/[^a-z0-9]+/', '-', $text);

        return trim($text, '-');
    }
}
