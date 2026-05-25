<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Product.php';

$user = requireCustomer();
$userId = (int) $user['id'];
$method = $_SERVER['REQUEST_METHOD'];
$data = parseJsonRequest();

if ($method === 'GET') {
    $items = Cart::getByUser($pdo, $userId);
    sendJson(['items' => $items]);
}

if ($method === 'POST') {
    $productId = isset($data['product_id']) ? (int) $data['product_id'] : 0;
    $quantity = max(1, isset($data['quantity']) ? (int) $data['quantity'] : 1);
    $product = Product::findById($pdo, $productId);

    if (!$product) {
        sendJson(['error' => 'Product not found'], 404);
    }

    $items = Cart::getByUser($pdo, $userId);
    $found = false;
    foreach ($items as &$item) {
        if ($item['product_id'] === $productId) {
            $item['quantity'] += $quantity;
            $item['quantity'] = min($item['quantity'], $product['stock']);
            $found = true;
            break;
        }
    }
    unset($item);

    if (!$found) {
        $items[] = [
            'product_id' => $productId,
            'quantity' => min($quantity, $product['stock']),
            'unit_price' => $product['price'],
        ];
    }

    Cart::save($pdo, $userId, $items);
    sendJson(['message' => 'Cart updated', 'items' => $items]);
}

if ($method === 'PUT') {
    $productId = isset($data['product_id']) ? (int) $data['product_id'] : 0;
    $quantity = max(0, isset($data['quantity']) ? (int) $data['quantity'] : 0);
    $items = Cart::getByUser($pdo, $userId);
    $updated = [];

    foreach ($items as $item) {
        if ($item['product_id'] === $productId) {
            if ($quantity <= 0) {
                continue;
            }
            $item['quantity'] = $quantity;
        }
        $updated[] = $item;
    }

    Cart::save($pdo, $userId, $updated);
    sendJson(['message' => 'Cart updated', 'items' => $updated]);
}

if ($method === 'DELETE') {
    $productId = isset($data['product_id']) ? (int) $data['product_id'] : 0;
    $items = Cart::getByUser($pdo, $userId);
    $items = array_filter($items, fn($item) => $item['product_id'] !== $productId);
    Cart::save($pdo, $userId, array_values($items));
    sendJson(['message' => 'Item removed', 'items' => array_values($items)]);
}

sendJson(['error' => 'Method not allowed'], 405);
