<?php
namespace App\Core;

use PDO;
use Exception;

/**
 * Base Model Class
 * Provides common database interactions for all inherited models.
 */
abstract class Model {
    protected PDO $db;
    protected string $table;

    public function __construct() {
        // Automatically grab the shared PDO connection
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Finds a single record by its ID.
     */
    public function find(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Finds a record by a specific column value.
     * Useful for checking emails, usernames, etc.
     */
    public function findBy(string $column, $value): ?array {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} = :val LIMIT 1");
        $stmt->execute(['val' => $value]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Basic Query Wrapper
     * Simplifies prepared statements across the application.
     */
    protected function query(string $sql, array $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Create a new record.
     * @param array $data ['column' => 'value']
     * @return int The last inserted ID
     */
    public function create(array $data): int {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        
        $this->query($sql, $data);
        return (int) $this->db->lastInsertId();
    }
}