<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

$data = parseJsonRequest();

$firstName = getJsonBodyValue($data, 'first_name');
$lastName = getJsonBodyValue($data, 'last_name');
$email = getJsonBodyValue($data, 'email');
$message = getJsonBodyValue($data, 'message');

$name = trim($firstName . ' ' . $lastName);

if ($name === '' || $email === '' || $message === '') {
    sendJson(['error' => 'Name, email and message are required'], 400);
}

if (!validateEmail($email)) {
    sendJson(['error' => 'Invalid email address'], 400);
}

$stmt = $pdo->prepare("
    INSERT INTO contacts
    (name, email, message, created_at)
    VALUES
    (:name, :email, :message, NOW())
");

$stmt->execute([
    'name' => $name,
    'email' => $email,
    'message' => $message
]);

sendJson([
    'message' => 'Message sent successfully ✅'
], 201);