<?php

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $requiredVars = ['DB_PASSWORD'];
            foreach ($requiredVars as $var) {
                if (getenv($var) === false || getenv($var) === '') {
                    throw new RuntimeException("Missing required environment variable: {$var}");
                }
            }

            $dbHost = getenv('DB_HOST') ?: 'db';
            $dbName = getenv('DB_NAME') ?: 'postgres';
            $dbUser = getenv('DB_USER') ?: 'postgres';
            $dbPassword = getenv('DB_PASSWORD');

            $dsn = "pgsql:host={$dbHost};port=5432;dbname={$dbName}";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            self::$instance = new PDO($dsn, $dbUser, $dbPassword, $options);
        }

        return self::$instance;
    }

    public static function testConnection(): bool
    {
        try {
            self::getConnection()->query('SELECT 1');
            return true;
        } catch (PDOException $_e) {
            return false;
        }
    }
}
