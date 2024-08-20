<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400'); // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}

header("Content-Type: application/json");

// Include database connection
require_once 'db_connection.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            // Fetch all tasks
            $stmt = $pdo->query("SELECT * FROM tasks ORDER BY id DESC");
            $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($tasks);
            break;

        case 'POST':
            // Create a new task
            $data = json_decode(file_get_contents("php://input"));
            if (!isset($data->title) || trim($data->title) === '') {
                throw new Exception("Title is required");
            }
            $completed = isset($data->completed) ? ($data->completed ? 1 : 0) : 0;  // Default to 0 if not set
            $stmt = $pdo->prepare("INSERT INTO tasks (title, completed) VALUES (?, ?)");
            $stmt->execute([$data->title, $completed]);
            $taskId = $pdo->lastInsertId();

            $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
            $stmt->execute([$taskId]);
            $newTask = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($newTask);
            break;

        case 'PUT':
            // Update a task
            $data = json_decode(file_get_contents("php://input"));
            if (!isset($data->id) || !isset($data->completed)) {
                throw new Exception("ID and completed status are required");
            }
            $completed = $data->completed ? 1 : 0;  // Convert to 1 or 0 for MySQL tinyint
            $stmt = $pdo->prepare("UPDATE tasks SET completed = ? WHERE id = ?");
            $stmt->execute([$completed, $data->id]);

            $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
            $stmt->execute([$data->id]);
            $updatedTask = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$updatedTask) {
                throw new Exception("Task not found");
            }
            echo json_encode($updatedTask);
            break;

        case 'DELETE':
            // Delete a task
            if (!isset($_GET['id'])) {
                throw new Exception("ID is required");
            }
            $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            echo json_encode(["message" => "Task deleted successfully"]);
            break;

        default:
            throw new Exception("Method not allowed");
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}