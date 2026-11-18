<?php

declare(strict_types=1);

namespace App\Core;

use App\Exceptions\DatabaseException;
use PDO;
use PDOException;

/**
 * Creates a single PDO connection for the whole request (Singleton pattern).
 * Prepared statements are used everywhere, so user input is never concatenated into SQL.
 */
final class Database
{
    private static ?PDO $connection = null;

    private function __construct()
    {
    }

    /**
     * @throws DatabaseException when the connection cannot be opened
     */
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                Config::get('database.host'),
                (int) Config::get('database.port', 3306),
                Config::get('database.name'),
                Config::get('database.charset', 'utf8mb4')
            );

            try {
                self::$connection = new PDO($dsn, Config::get('database.user'), Config::get('database.password'), [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $exception) {
                throw new DatabaseException('Nu s-a putut realiza conexiunea la baza de date.', 0, $exception);
            }
        }

        return self::$connection;
    }
}
