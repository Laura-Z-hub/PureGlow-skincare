<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

$data = parseJsonRequest();
$email = getJsonBodyValue($data, 'email');

if ($email === '') {
    sendJson(['error' => 'Email is required'], 400);
}

if (!validateEmail($email)) {
    sendJson(['error' => 'Invalid email address'], 400);
}

$stmt = $pdo->prepare('SELECT id FROM subscriptions WHERE email = :email LIMIT 1');
$stmt->execute(['email' => $email]);
if ($stmt->fetch()) {
    sendJson(['message' => 'You are already subscribed'], 200);
}

$insert = $pdo->prepare('INSERT INTO subscriptions (email, subscribed_at) VALUES (:email, NOW())');
$insert->execute(['email' => $email]);
sendJson(['message' => 'Subscription successful']);
