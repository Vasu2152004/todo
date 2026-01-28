-- Simple Todo App Database Schema

CREATE DATABASE IF NOT EXISTS todo_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE todo_app;

CREATE TABLE IF NOT EXISTS todos (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample data (optional)
INSERT INTO todos (title, description, priority, due_date, completed) VALUES
('Welcome to Todo App', 'This is your first todo item. You can edit or delete it!', 'high', DATE_ADD(NOW(), INTERVAL 1 DAY), 0),
('Complete the project', 'Finish building this amazing todo application', 'high', DATE_ADD(NOW(), INTERVAL 2 DAYS), 0),
('Learn PHP', 'Master PHP programming language', 'medium', DATE_ADD(NOW(), INTERVAL 7 DAYS), 0);
