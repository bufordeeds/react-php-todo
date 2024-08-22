<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

require_once 'db_connection.php';
require_once 'api_config.php';
require_once 'session.php';
startSession();

// API route handlers
function getAllTasks($pdo)
{
    var_dump($_SESSION);
    $stmt = $pdo->query('SELECT * FROM tasks ORDER BY id ASC');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function createTask($pdo, $data)
{
    print_r($_SESSION);
    if (!isset($data->title) || trim($data->title) === '') {
        throw new Exception('Title is required');
    }
    $completed = isset($data->completed) ? ($data->completed ? 1 : 0) : 0;
    $stmt = $pdo->prepare('INSERT INTO tasks (title, completed) VALUES (?, ?)');
    $stmt->execute([$data->title, $completed]);
    $taskId = $pdo->lastInsertId();
    $stmt = $pdo->prepare('SELECT * FROM tasks WHERE id = ?');
    $stmt->execute([$taskId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateTask($pdo, $data)
{
    if (!isset($data->id) || !isset($data->completed)) {
        throw new Exception('ID and completed status are required');
    }
    $completed = $data->completed ? 1 : 0;
    $stmt = $pdo->prepare('UPDATE tasks SET completed = ? WHERE id = ?');
    $stmt->execute([$completed, $data->id]);

    $stmt = $pdo->prepare('SELECT * FROM tasks WHERE id = ?');
    $stmt->execute([$data->id]);
    $updatedTask = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$updatedTask) {
        throw new Exception('Task not found');
    }
    return $updatedTask;
}

function deleteTask($pdo, $id)
{
    $stmt = $pdo->prepare('DELETE FROM tasks WHERE id = ?');
    $stmt->execute([$id]);
    return ['message' => 'Task deleted successfully'];
}

function saveTasks($tasks)
{
    $json = json_encode($tasks, JSON_PRETTY_PRINT);
    $file = 'tasks.json';
    if (file_put_contents($file, $json)) {
        return true;
    }
    return false;
}

// Main execution
try {
    handleCors();

    $method = $_SERVER['REQUEST_METHOD'];
    $data = json_decode(file_get_contents('php://input'));

    switch ($method) {
        case 'GET':
            $tasks = getAllTasks($pdo);
            saveTasks($tasks); // Save tasks to JSON file
            sendJsonResponse($tasks);
            break;
        case 'POST':
            $newTask = createTask($pdo, $data);
            $tasks = getAllTasks($pdo);
            if (saveTasks($tasks)) {
                sendJsonResponse([
                    'message' => 'Task added successfully and data saved to JSON file',
                    'task' => $newTask,
                ]);
            } else {
                sendJsonResponse([
                    'message' => 'Task added successfully but failed to save to JSON file',
                    'task' => $newTask,
                ]);
            }
            break;
        case 'PUT':
            $updatedTask = updateTask($pdo, $data);
            $tasks = getAllTasks($pdo);
            if (saveTasks($tasks)) {
                sendJsonResponse([
                    'message' => 'Task updated successfully and data saved to JSON file',
                    'task' => $updatedTask,
                ]);
            } else {
                sendJsonResponse([
                    'message' => 'Task updated successfully but failed to save to JSON file',
                    'task' => $updatedTask,
                ]);
            }
            break;
        case 'DELETE':
            if (!isset($_GET['id'])) {
                throw new Exception('ID is required');
            }
            deleteTask($pdo, $_GET['id']);
            $tasks = getAllTasks($pdo);
            if (saveTasks($tasks)) {
                sendJsonResponse([
                    'message' => 'Task deleted successfully and data saved to JSON file',
                ]);
            } else {
                sendJsonResponse([
                    'message' => 'Task deleted successfully but failed to save to JSON file',
                ]);
            }
            break;
        default:
            throw new Exception('Method not allowed');
    }
} catch (Exception $e) {
    handleApiError($e);
}
