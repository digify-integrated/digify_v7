<?php
namespace Core;

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;

    /**
     * Returns a single database connection instance (Singleton Pattern).
     */
    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $driver = $_ENV['DB_DRIVER'] ?? 'mysql';
            $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
            $port = $_ENV['DB_PORT'] ?? '3306';
            $dbname = $_ENV['DB_DATABASE'] ?? 'digify_db';
            
            $dsn = "$driver:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
            
            try {
                self::$instance = new PDO($dsn, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            } catch (PDOException $e) {
                // We THROW the error instead of using die() so the CLI can catch it
                throw $e;
            }
        }
        return self::$instance;
    }

    /**
     * Creates the database defined in .env if it doesn't exist.
     */
    public static function createDatabase(string $dbName): void {
        try {
            $driver = $_ENV['DB_DRIVER'] ?? 'mysql';
            $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
            $port = $_ENV['DB_PORT'] ?? '3306';
            $user = $_ENV['DB_USERNAME'];
            $pass = $_ENV['DB_PASSWORD'];

            // Connect to the server without specifying a database name
            $pdo = new PDO("$driver:host=$host;port=$port", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
        } catch (PDOException $e) {
            throw new \Exception("Error creating database: " . $e->getMessage());
        }
    }
}