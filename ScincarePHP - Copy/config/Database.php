<?php
declare(strict_types=1);

final class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(array $credentials): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $host = $credentials['host'] ?? '127.0.0.1';
        $port = $credentials['port'] ?? 3306;
        $dbname = $credentials['dbname'] ?? 'pureglow';
        $charset = $credentials['charset'] ?? 'utf8mb4';
        $user = $credentials['user'] ?? 'root';
        $pass = $credentials['pass'] ?? '';

        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $host, $port, $dbname, $charset);

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        self::$connection = new PDO($dsn, $user, $pass, $options);
        return self::$connection;
    }
}
