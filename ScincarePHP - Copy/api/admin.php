<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../models/User.php';

requireAdmin();

$ordersStmt = $pdo->query('SELECT COUNT(*) AS total FROM orders');
$productsStmt = $pdo->query('SELECT COUNT(*) AS total FROM products');
$usersStmt = $pdo->query('SELECT COUNT(*) AS total FROM users');
$contactsStmt = $pdo->query('SELECT COUNT(*) AS total FROM contacts');
$bookingsStmt = $pdo->query('SELECT COUNT(*) AS total FROM bookings');
$subscriptionsStmt = $pdo->query('SELECT COUNT(*) AS total FROM subscriptions');

sendJson([
    'orders' => (int) $ordersStmt->fetchColumn(),
    'products' => (int) $productsStmt->fetchColumn(),
    'users' => (int) $usersStmt->fetchColumn(),
    'contacts' => (int) $contactsStmt->fetchColumn(),
    'bookings' => (int) $bookingsStmt->fetchColumn(),
    'subscriptions' => (int) $subscriptionsStmt->fetchColumn(),
]);
