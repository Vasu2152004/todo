<?php

declare(strict_types=1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/database.php';

function loadEnv(): void {
    $envFile = __DIR__ . '/../.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            $_ENV[$name] = $value;
        }
    }
}

loadEnv();

try {
    $db = Database::getConnection();
    $method = $_SERVER['REQUEST_METHOD'];
    $path = $_GET['action'] ?? '';
    
    switch ($method) {
        case 'GET':
            if ($path === 'list') {
                $filter = $_GET['filter'] ?? 'all';
                $query = "SELECT * FROM todos";
                
                if ($filter === 'active') {
                    $query .= " WHERE completed = 0";
                } elseif ($filter === 'completed') {
                    $query .= " WHERE completed = 1";
                }
                
                $query .= " ORDER BY created_at DESC";
                
                $stmt = $db->query($query);
                $todos = $stmt->fetchAll();
                
                echo json_encode(['success' => true, 'data' => $todos]);
            } elseif ($path === 'get' && isset($_GET['id'])) {
                $stmt = $db->prepare("SELECT * FROM todos WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                $todo = $stmt->fetch();
                
                if ($todo) {
                    echo json_encode(['success' => true, 'data' => $todo]);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Todo not found']);
                }
            }
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if ($path === 'create') {
                $title = $data['title'] ?? '';
                $description = $data['description'] ?? '';
                $priority = $data['priority'] ?? 'medium';
                $dueDate = $data['due_date'] ?? null;
                
                if (empty($title)) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Title is required']);
                    exit;
                }
                
                $stmt = $db->prepare("
                    INSERT INTO todos (title, description, priority, due_date, completed, created_at)
                    VALUES (?, ?, ?, ?, 0, NOW())
                ");
                
                $stmt->execute([$title, $description, $priority, $dueDate]);
                $id = $db->lastInsertId();
                
                $stmt = $db->prepare("SELECT * FROM todos WHERE id = ?");
                $stmt->execute([$id]);
                $todo = $stmt->fetch();
                
                echo json_encode(['success' => true, 'data' => $todo]);
            } elseif ($path === 'toggle' && isset($data['id'])) {
                $stmt = $db->prepare("UPDATE todos SET completed = NOT completed WHERE id = ?");
                $stmt->execute([$data['id']]);
                
                $stmt = $db->prepare("SELECT * FROM todos WHERE id = ?");
                $stmt->execute([$data['id']]);
                $todo = $stmt->fetch();
                
                echo json_encode(['success' => true, 'data' => $todo]);
            }
            break;
            
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if ($path === 'update' && isset($data['id'])) {
                $title = $data['title'] ?? '';
                $description = $data['description'] ?? '';
                $priority = $data['priority'] ?? 'medium';
                $dueDate = $data['due_date'] ?? null;
                
                if (empty($title)) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Title is required']);
                    exit;
                }
                
                $stmt = $db->prepare("
                    UPDATE todos 
                    SET title = ?, description = ?, priority = ?, due_date = ?
                    WHERE id = ?
                ");
                
                $stmt->execute([$title, $description, $priority, $dueDate, $data['id']]);
                
                $stmt = $db->prepare("SELECT * FROM todos WHERE id = ?");
                $stmt->execute([$data['id']]);
                $todo = $stmt->fetch();
                
                echo json_encode(['success' => true, 'data' => $todo]);
            }
            break;
            
        case 'DELETE':
            if ($path === 'delete' && isset($_GET['id'])) {
                $stmt = $db->prepare("DELETE FROM todos WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                
                echo json_encode(['success' => true, 'message' => 'Todo deleted']);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
