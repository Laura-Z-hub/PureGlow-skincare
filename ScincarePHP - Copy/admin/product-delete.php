<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

requireAdmin();

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
$stmt->execute([$id]);

header('Location: products.php');
exit;