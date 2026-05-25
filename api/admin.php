<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../models/User.php';

requireAdmin();

sendJson([
    'products' => (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn(),
    'promotions' => (int) $pdo->query('SELECT COUNT(*) FROM promotions')->fetchColumn(),
    'orders' => (int) $pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn(),
    'customers' => (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn(),
    'contacts' => (int) $pdo->query('SELECT COUNT(*) FROM contacts')->fetchColumn(),
    'bookings' => (int) $pdo->query('SELECT COUNT(*) FROM bookings')->fetchColumn(),
    'subscriptions' => (int) $pdo->query('SELECT COUNT(*) FROM subscriptions')->fetchColumn(),
    'money' => (float) $pdo->query("
        SELECT COALESCE(SUM(total_amount), 0)
        FROM orders
        WHERE status IN ('confirmed','completed')
    ")->fetchColumn(),
]);
