<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Product.php';

$user = requireCustomer();
$data = parseJsonRequest();

$cardNumber = preg_replace('/\s+/', '', $data['card_number'] ?? '');
$expiry = trim($data['expiry'] ?? '');
$cvv = trim($data['cvv'] ?? '');

if ($cardNumber !== '4242424242424242' || $expiry === '' || $cvv === '') {
    sendJson(['error' => 'Fake payment declined'], 400);
}

$items = Cart::getByUser($pdo, (int)$user['id']);

if (empty($items)) {
    sendJson(['error' => 'Cart is empty'], 400);
}

$address = [
    'street' => getJsonBodyValue($data, 'street', 'Demo Street'),
    'city' => getJsonBodyValue($data, 'city', 'Tirana'),
    'postal_code' => getJsonBodyValue($data, 'postal_code', '1001'),
    'country' => getJsonBodyValue($data, 'country', 'Albania'),
];

$total = 0.0;

foreach ($items as &$item) {
    $product = Product::findById($pdo, (int)$item['product_id']);

    if (!$product) {
        sendJson(['error' => 'Product not found'], 400);
    }

    $requestedQuantity = max(1, (int)$item['quantity']);

    if ((int)$product['stock'] < $requestedQuantity) {
        sendJson(['error' => 'Not enough stock for ' . $product['name']], 400);
    }

    $item['quantity'] = $requestedQuantity;
    $item['unit_price'] = (float)$product['price'];
    $item['total_price'] = $item['quantity'] * $item['unit_price'];
    $total += $item['total_price'];
}
unset($item);

$payload = [
    'total_amount' => $total,
    'currency' => 'EUR',
    'payment_method' => 'fake_card',
    'shipping_address' => $address,
    'notes' => 'Fake demo card payment',
    'invoice_email_requested' => booleanValue($data['invoice_email_requested'] ?? true),
];

try {
    $orderId = Order::create($pdo, (int)$user['id'], $payload, $items);
    Cart::clear($pdo, (int)$user['id']);
} catch (RuntimeException $error) {
    sendJson(['error' => $error->getMessage()], 400);
}

sendJson([
    'message' => 'Fake payment successful',
    'order_id' => $orderId,
    'total' => $total
]);
