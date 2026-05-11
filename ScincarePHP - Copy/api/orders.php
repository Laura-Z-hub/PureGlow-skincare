<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Product.php';

$user = requireLogin();
$isAdmin = ($user['role'] ?? '') === 'admin';
$method = $_SERVER['REQUEST_METHOD'];
$data = parseJsonRequest();
$orderId = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : null;

if ($method === 'GET') {
    if ($orderId !== false && $orderId !== null) {
        $order = Order::findById($pdo, $orderId);
        if (!$order) {
            sendJson(['error' => 'Order not found'], 404);
        }
        if (!$isAdmin && $order['user_id'] !== (int) $user['id']) {
            sendJson(['error' => 'Access denied'], 403);
        }
        sendJson($order);
    }

    $orders = $isAdmin ? Order::findAll($pdo) : Order::findAll($pdo, (int) $user['id']);
    sendJson(['orders' => $orders]);
}

if ($method === 'POST') {
    $items = Cart::getByUser($pdo, (int) $user['id']);
    if (empty($items)) {
        sendJson(['error' => 'Cart is empty'], 400);
    }

    $address = [
        'street' => getJsonBodyValue($data, 'street'),
        'city' => getJsonBodyValue($data, 'city'),
        'postal_code' => getJsonBodyValue($data, 'postal_code'),
        'country' => getJsonBodyValue($data, 'country'),
    ];

    if ($address['street'] === '' || $address['city'] === '' || $address['postal_code'] === '' || $address['country'] === '') {
        sendJson(['error' => 'Shipping address is required'], 400);
    }

    $total = 0.0;
    foreach ($items as &$item) {
        $product = Product::findById($pdo, $item['product_id']);
        if (!$product) {
            sendJson(['error' => 'Product not found in cart'], 400);
        }
        $item['quantity'] = min((int)$item['quantity'], $product['stock']);
        $item['unit_price'] = $product['price'];
        $item['total_price'] = $item['quantity'] * $item['unit_price'];
        $total += $item['total_price'];
    }
    unset($item);

    $payload = [
        'total_amount' => $total,
        'currency' => 'EUR',
        'payment_method' => getJsonBodyValue($data, 'payment_method', 'cash_on_delivery'),
        'shipping_address' => $address,
        'notes' => getJsonBodyValue($data, 'notes'),
    ];

    $orderId = Order::create($pdo, (int) $user['id'], $payload, $items);
    Cart::clear($pdo, (int) $user['id']);
    sendJson(['message' => 'Order created successfully', 'order_id' => $orderId], 201);
}

if ($method === 'PUT' && $orderId !== false && $orderId !== null) {
    if (!$isAdmin) {
        sendJson(['error' => 'Admin privileges required'], 403);
    }
    $status = getJsonBodyValue($data, 'status');
    if ($status === '') {
        sendJson(['error' => 'Status is required'], 400);
    }
    Order::updateStatus($pdo, $orderId, $status);
    sendJson(['message' => 'Order status updated']);
}

sendJson(['error' => 'Method not allowed'], 405);
