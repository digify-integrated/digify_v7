<?php
namespace Core;

use PDO;

abstract class Model {
    protected static string $table = '';
    protected array $attributes = [];

    // Magic methods to dynamically assign properties (e.g., $user->name = 'Lawrence')
    public function __set(string $key, mixed $value): void {
        $this->attributes[$key] = $value;
    }

    public function __get(string $key): mixed {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Fetch all records from the table.
     */
    public static function all(): array {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM " . static::$table);
        return $stmt->fetchAll(PDO::FETCH_CLASS, static::class);
    }

    /**
     * Find a single record by its ID.
     */
    public static function find(int $id): ?self {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM " . static::$table . " WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, static::class);
        $result = $stmt->fetch();
        
        return $result ?: null;
    }

    /**
     * Save the current model state to the database (Insert or Update).
     */
    public function save(): bool {
        $db = Database::getConnection();
        $columns = array_keys($this->attributes);
        
        if (isset($this->attributes['id'])) {
            // Update existing record
            $setClause = implode(', ', array_map(fn($col) => "$col = :$col", $columns));
            $sql = "UPDATE " . static::$table . " SET $setClause WHERE id = :id";
        } else {
            // Insert new record
            $placeholders = implode(', ', array_map(fn($col) => ":$col", $columns));
            $cols = implode(', ', $columns);
            $sql = "INSERT INTO " . static::$table . " ($cols) VALUES ($placeholders)";
        }

        $stmt = $db->prepare($sql);
        return $stmt->execute($this->attributes);
    }
}