<?php
session_start();
require_once 'db_connection.php';
require_once 'api_config.php';
handleCors();

function registerUser($username, $password)
{
    global $pdo;

    // Check if username already exists
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'Username already exists'];
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
    if ($stmt->execute([$username, $hashedPassword])) {
        return ['success' => true, 'message' => 'User registered successfully'];
    } else {
        return ['success' => false, 'message' => 'Registration failed'];
    }
}

function loginUser($username, $password)
{
    print_r($_SERVER);
    echo 'SOMETHING DUMB';
    global $pdo;

    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && $user['password'] === $password) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        return [
            'success' => true,
            'message' => 'Login successful',
            'user' => $user['username'],
        ];
    } else {
        return ['success' => false, 'message' => 'Invalid username or password'];
    }
}

function logoutUser()
{
    session_unset();
    session_destroy();
    return ['success' => true, 'message' => 'Logged out successfully'];
}

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function getCurrentUser()
{
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
        ];
    }
    return null;
}

// Handle POST requests for login and registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';

    switch ($action) {
        case 'register':
            echo json_encode(registerUser($username, $password));
            break;
        case 'login':
            sendJsonResponse(loginUser($username, $password));
            break;
        case 'logout':
            sendJsonResponse(logoutUser($username, $password));
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
}

// Handle GET requests for checking login status
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action']) && $_GET['action'] === 'check_login') {
        echo json_encode([
            'loggedIn' => isLoggedIn(),
            'user' => getCurrentUser(),
        ]);
    }
}
?>
