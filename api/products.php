<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/User.php';

$method = $_SERVER['REQUEST_METHOD'];
$productId = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : null;
$category = isset($_GET['category']) ? trim($_GET['category']) : null;

if ($method === 'GET') {

    if ($productId !== false && $productId !== null) {
        $stmt = $pdo->prepare("
            SELECT 
                p.*,
                pr.discount_percent
            FROM products p
            LEFT JOIN promotions pr 
                ON pr.product_id = p.id 
                AND pr.active = 1
            WHERE p.id = ?
            LIMIT 1
        ");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();

        if (!$product) {
            sendJson(['error' => 'Product not found'], 404);
        }

        sendJson($product);
    }

    $sql = "
        SELECT 
            p.*,
            pr.discount_percent
        FROM products p
        LEFT JOIN promotions pr 
            ON pr.product_id = p.id 
            AND pr.active = 1
    ";

    $params = [];

    if ($category && $category !== 'all') {
        if ($category === 'promotions') {
            $sql .= " WHERE pr.discount_percent IS NOT NULL ";
        } else {
            $sql .= " WHERE p.category = ? ";
            $params[] = $category;
        }
    }

    $sql .= " ORDER BY p.id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $products = $stmt->fetchAll();

    sendJson(['products' => $products]);
}

if ($method === 'POST') {
    $user = requireAdmin();
    $data = parseJsonRequest();
    $payload = [
        'name' => getJsonBodyValue($data, 'name'),
        'slug' => getJsonBodyValue($data, 'slug'),
        'sku' => getJsonBodyValue($data, 'sku'),
        'category' => getJsonBodyValue($data, 'category'),
        'brand' => getJsonBodyValue($data, 'brand'),
        'description' => getJsonBodyValue($data, 'description'),
        'price' => isset($data['price']) ? (float) $data['price'] : 0.0,
        'currency' => getJsonBodyValue($data, 'currency', 'EUR'),
        'stock' => isset($data['stock']) ? (int) $data['stock'] : 0,
        'images' => buildProductImageArray($data['images'] ?? []),
        'featured' => booleanValue($data['featured'] ?? false) ? 1 : 0,
    ];

    if ($payload['name'] === '' || $payload['slug'] === '') {
        sendJson(['error' => 'Product name and slug are required'], 400);
    }

    $productId = Product::create($pdo, $payload);
    sendJson(['message' => 'Product created', 'product_id' => $productId], 201);
}

if (($method === 'PUT' || $method === 'PATCH') && $productId !== false && $productId !== null) {
    requireAdmin();
    $data = parseJsonRequest();
    $payload = [
        'name' => getJsonBodyValue($data, 'name'),
        'slug' => getJsonBodyValue($data, 'slug'),
        'sku' => getJsonBodyValue($data, 'sku'),
        'category' => getJsonBodyValue($data, 'category'),
        'brand' => getJsonBodyValue($data, 'brand'),
        'description' => getJsonBodyValue($data, 'description'),
        'price' => isset($data['price']) ? (float) $data['price'] : 0.0,
        'currency' => getJsonBodyValue($data, 'currency', 'EUR'),
        'stock' => isset($data['stock']) ? (int) $data['stock'] : 0,
        'images' => buildProductImageArray($data['images'] ?? []),
        'featured' => booleanValue($data['featured'] ?? false) ? 1 : 0,
    ];

    Product::update($pdo, $productId, $payload);
    sendJson(['message' => 'Product updated']);
}

if ($method === 'DELETE' && $productId !== false && $productId !== null) {
    requireAdmin();
    Product::delete($pdo, $productId);
    sendJson(['message' => 'Product deleted']);
}

sendJson(['error' => 'Method not allowed or missing product id'], 405);
