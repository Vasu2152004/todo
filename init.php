<?php

declare(strict_types=1);

/**
 * Database initialization script
 * Run this once to set up the database
 */

require_once __DIR__ . '/config/database.php';

function loadEnv(): void {
    $envFile = __DIR__ . '/.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            if (strpos($line, '=') === false) continue;
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            $_ENV[$name] = $value;
        }
    }
}

loadEnv();

try {
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $port = $_ENV['DB_PORT'] ?? '3306';
    $database = $_ENV['DB_DATABASE'] ?? 'todo_app';
    $username = $_ENV['DB_USERNAME'] ?? 'root';
    $password = $_ENV['DB_PASSWORD'] ?? '';
    
    // Connect without database first to create it
    $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$database`");
    
    // Create table
    $sql = "CREATE TABLE IF NOT EXISTS todos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        completed TINYINT(1) DEFAULT 0,
        priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
        due_date DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_completed (completed),
        INDEX idx_priority (priority),
        INDEX idx_due_date (due_date)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    
    // Check if table is empty and insert sample data
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM todos");
    $count = $stmt->fetch()['count'];
    
    if ($count == 0) {
        $pdo->exec("INSERT INTO todos (title, description, priority, due_date, completed) VALUES
            ('Welcome to Todo App', 'This is your first todo item. You can edit or delete it!', 'high', DATE_ADD(NOW(), INTERVAL 1 DAY), 0),
            ('Complete the project', 'Finish building this amazing todo application', 'high', DATE_ADD(NOW(), INTERVAL 2 DAYS), 0),
            ('Learn PHP', 'Master PHP programming language', 'medium', DATE_ADD(NOW(), INTERVAL 7 DAYS), 0)");
    }
    
    echo "✅ Database initialized successfully!\n";
    echo "Database: $database\n";
    echo "Table: todos\n";
    echo "Sample data inserted.\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
