<?php

namespace Tests\Integration\ProdLike\Support;

use PDO;
use RuntimeException;

class MySqlLock
{
    public static function lockRow(string $table, int $id): PDO
    {
        $connection = config('database.connections.mysql');

        if (! is_array($connection)) {
            throw new RuntimeException('MySQL connection is not configured.');
        }

        $host = (string) ($connection['host'] ?? '127.0.0.1');
        $port = (int) ($connection['port'] ?? 3306);
        $database = (string) ($connection['database'] ?? '');
        $username = (string) ($connection['username'] ?? '');
        $password = (string) ($connection['password'] ?? '');

        $pdo = new PDO(
            sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $host, $port, $database),
            $username,
            $password,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
        );

        $pdo->beginTransaction();

        $statement = $pdo->prepare(sprintf('SELECT id FROM `%s` WHERE id = ? FOR UPDATE', $table));
        $statement->execute([$id]);

        return $pdo;
    }
}
