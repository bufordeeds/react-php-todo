<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

require_once 'db_connection.php';

// CORS handling
function handleCors()
{
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');
    }

    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
            header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
        }
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
            header(
                "Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}",
            );
        }
        exit(0);
    }
}

// API response function
function sendJsonResponse($data, $statusCode = 200)
{
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

// API error handler
function handleApiError($e)
{
    $statusCode = $e instanceof PDOException ? 500 : 400;
    sendJsonResponse(['error' => $e->getMessage()], $statusCode);
}

// API route handlers
function getAllTasks($pdo)
{
    $stmt = $pdo->query('SELECT * FROM tasks ORDER BY id ASC');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function createTask($pdo, $data)
{
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

// Main execution
try {
    handleCors();

    $method = $_SERVER['REQUEST_METHOD'];
    $data = json_decode(file_get_contents('php://input'));

    switch ($method) {
        case 'GET':
            sendJsonResponse(getAllTasks($pdo));
            break;
        case 'POST':
            sendJsonResponse(createTask($pdo, $data));
            break;
        case 'PUT':
            sendJsonResponse(updateTask($pdo, $data));
            break;
        case 'DELETE':
            if (!isset($_GET['id'])) {
                throw new Exception('ID is required');
            }
            sendJsonResponse(deleteTask($pdo, $_GET['id']));
            break;
        default:
            throw new Exception('Method not allowed');
    }
} catch (Exception $e) {
    handleApiError($e);
}
