<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

requireAdmin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id > 0) {
    $stmt = $pdo->prepare("
        DELETE FROM promotions
        WHERE id = ?
    ");

    $stmt->execute([$id]);
}

header('Location: promotions.php');
exit;