<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

require_once 'api_config.php';
require_once 'db_connection.php';

function getAllUsers($pdo)
{
    $stmt = $pdo->query('SELECT * FROM users ORDER BY id ASC');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Main execution
try {
    handleCors();

    $method = $_SERVER['REQUEST_METHOD'];
    $data = json_decode(file_get_contents('php://input'));

    switch ($method) {
        case 'GET':
            $users = getAllUsers($pdo);
            sendJsonResponse($users);

            break;
        case 'POST':
            break;
        case 'PUT':
            break;
        case 'DELETE':
            break;
        default:
            throw new Exception('Method not allowed');
    }
} catch (Exception $e) {
    handleApiError($e);
}
