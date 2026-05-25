<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

$user = requireLogin();

if (($user['role'] ?? '') !== 'admin') {
    sendJson([
        'error' => 'Admin privileges required'
    ], 403);
}

$customers = $pdo->query("
    SELECT
        id,
        name,
        email,
        role,
        created_at
    FROM users
    WHERE role = 'customer'
    ORDER BY id DESC
")->fetchAll(PDO::FETCH_ASSOC);

sendJson([
    'customers' => $customers
]);