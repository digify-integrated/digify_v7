<?php
namespace Database\Seeds;

use Core\Database;
use App\Models\User; // Assuming you have a User model

class DatabaseSeeder {
    /**
     * Run the database seeds.
     * Use this to populate your tables with default or test data.
     */
    public function run(): void {
        echo "🌱 Seeding started...\n";

        $db = Database::getConnection();

        // 1. Example: Truncate tables to start fresh (Optional)
        // $db->exec("SET FOREIGN_KEY_CHECKS = 0;");
        // $db->exec("TRUNCATE TABLE users;");
        // $db->exec("SET FOREIGN_KEY_CHECKS = 1;");

        // 2. Example: Create a default Admin User using the Model
        $this->seedAdminUser();

        // 3. Example: Direct SQL Insert for Settings
        $this->seedSettings($db);

        echo "✅ Seeding completed successfully!\n";
    }

    /**
     * Example of seeding using a Model.
     */
    private function seedAdminUser(): void {
        // Checking if we have a User model first
        if (class_exists('App\Models\User')) {
            $user = new \App\Models\User();
            $user->username = 'admin';
            $user->email = 'admin@digify.local';
            // Always hash passwords!
            $user->password = password_hash('password123', PASSWORD_BCRYPT);
            
            if ($user->save()) {
                echo " - Admin user created.\n";
            }
        }
    }

    /**
     * Example of seeding using raw PDO for speed/simple tables.
     */
    private function seedSettings($db): void {
        try {
            // Check if settings table exists before seeding
            $db->exec("CREATE TABLE IF NOT EXISTS settings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                key_name VARCHAR(50) UNIQUE,
                value TEXT
            )");

            $stmt = $db->prepare("INSERT IGNORE INTO settings (key_name, value) VALUES (?, ?)");
            $stmt->execute(['site_name', 'Digify v7 Framework']);
            $stmt->execute(['maintenance_mode', 'off']);
            
            echo " - System settings seeded.\n";
        } catch (\Exception $e) {
            echo " - Skipping settings seed: " . $e->getMessage() . "\n";
        }
    }
}