<?php
namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Class Database
 * Fully locked-down Singleton for PDO management.
 */
class Database {
    private static ?Database $instance = null;
    private PDO $connection;

    /**
     * The constructor is private to prevent direct instantiation.
     */
    private function __construct() {
        // Typically, you'd pull these from a config file or ENV variables
        $host     = DB_HOST; 
        $dbname   = DB_NAME;
        $username = DB_USER;
        $password = DB_PASS;

        $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

        $options = [
            PDO::ATTR_EMULATE_PREPARES   => false, // Use real prepared statements
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Return arrays by default
            // Persistent connections are disabled by default for better stability
            // PDO::ATTR_PERSISTENT      => true 
        ];

        try {
            $this->connection = new PDO($dsn, $username, $password, $options);
            
            // Explicitly set names/collation if your MySQL version is older
            $this->connection->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
        } catch (PDOException $e) {
            // Log the real error for dev, throw a generic one for security
            error_log('Database Connection Error: ' . $e->getMessage());
            throw new RuntimeException('Could not connect to the database.');
        }
    }

    /**
     * Gets the single instance of the class.
     */
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Gets the PDO connection object.
     */
    public function getConnection(): PDO {
        return $this->connection;
    }

    /**
     * Prevent cloning of the instance.
     */
    private function __clone() {}

    /**
     * Prevent unserializing of the instance.
     */
    public function __wakeup() {
        throw new RuntimeException("Cannot unserialize a singleton.");
    }
}