<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

if (php_sapi_name() !== 'cli') {
    echo "Run this script from the command line: php database/seed_admin.php 'Admin Name' 'admin@example.com' 'YourPassword'\n";
    exit(1);
}

$argv = $_SERVER['argv'] ?? [];
if (count($argv) < 4) {
    echo "Usage: php database/seed_admin.php 'Admin Name' 'admin@example.com' 'YourPassword'\n";
    exit(1);
}

[$script, $name, $email, $password] = $argv;
if (!validateEmail($email)) {
    echo "Invalid email address.\n";
    exit(1);
}

$stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
$stmt->execute(['email' => $email]);
if ($stmt->fetch()) {
    echo "Admin user already exists for this email.\n";
    exit(1);
}

$insert = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)');
$insert->execute([
    'name' => trim($name),
    'email' => trim($email),
    'password' => password_hash($password, PASSWORD_DEFAULT),
    'role' => 'admin',
]);

echo "Admin account created successfully.\n";
