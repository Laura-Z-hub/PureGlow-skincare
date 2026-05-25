<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/InvoiceMailer.php';

requireAdmin();

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$status = $_POST['status'] ?? 'waiting';
$invoiceStatus = '';

if ($id > 0 && in_array($status, ['waiting', 'confirmed'], true)) {
    $order = Order::findById($pdo, $id);
    $wasConfirmed = ($order['status'] ?? '') === 'confirmed';

    Order::updateStatus($pdo, $id, $status);

    if ($status === 'confirmed' && !$wasConfirmed) {
        $invoiceStatus = InvoiceMailer::sendForOrder($pdo, $id) ? 'sent' : 'failed';
    }
}

$redirect = 'orders.php';

if ($invoiceStatus !== '') {
    $redirect .= '?invoice=' . urlencode($invoiceStatus);
}

header('Location: ' . $redirect);
exit;
