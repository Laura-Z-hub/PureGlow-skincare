<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../models/User.php';

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST' && $action === 'login') {
    $data = parseJsonRequest();
    $email = getJsonBodyValue($data, 'email');
    $password = getJsonBodyValue($data, 'password');

    if ($email === '' || $password === '') {
        sendJson(['error' => 'Email and password are required'], 400);
    }

    $user = User::findByEmail($pdo, $email);
    if (!$user || !password_verify($password, $user['password'])) {
        sendJson(['error' => 'Invalid credentials'], 401);
    }

    requireSession();
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role'],
    ];

    sendJson(['message' => 'Logged in successfully', 'user' => $_SESSION['user']]);
}

if ($method === 'POST' && $action === 'register') {
    $data = parseJsonRequest();
    $name = getJsonBodyValue($data, 'name');
    $email = getJsonBodyValue($data, 'email');
    $password = getJsonBodyValue($data, 'password');

    if ($name === '' || $email === '' || $password === '') {
        sendJson(['error' => 'Name, email and password are required'], 400);
    }

    if (!validateEmail($email)) {
        sendJson(['error' => 'Invalid email address'], 400);
    }

    if (User::findByEmail($pdo, $email)) {
        sendJson(['error' => 'Email is already registered'], 409);
    }

    $userId = User::create($pdo, $name, $email, $password);
    sendJson(['message' => 'Registration successful', 'user_id' => $userId], 201);
}

if ($method === 'POST' && $action === 'logout') {
    requireSession();
    session_unset();
    session_destroy();
    sendJson(['message' => 'Logged out successfully']);
}

if ($method === 'GET' && $action === 'status') {
    requireSession();
    if (empty($_SESSION['user'])) {
        sendJson(['authenticated' => false]);
    }
    sendJson(['authenticated' => true, 'user' => $_SESSION['user']]);
}

sendJson(['error' => 'Action not found'], 404);
