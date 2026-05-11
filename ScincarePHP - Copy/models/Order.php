<?php
declare(strict_types=1);

final class Order
{
    public static function create(PDO $pdo, int $userId, array $payload, array $items): int
    {
        $insert = $pdo->prepare(
            'INSERT INTO orders (user_id, order_number, status, total_amount, currency, payment_method, shipping_address, notes, created_at, updated_at)
            VALUES (:user_id, :order_number, :status, :total_amount, :currency, :payment_method, :shipping_address, :notes, NOW(), NOW())'
        );
        $orderNumber = sprintf('PG-%s-%s', date('Ymd'), bin2hex(random_bytes(4)));
        $insert->execute([
            'user_id' => $userId,
            'order_number' => $orderNumber,
            'status' => 'pending',
            'total_amount' => $payload['total_amount'],
            'currency' => $payload['currency'],
            'payment_method' => $payload['payment_method'],
            'shipping_address' => json_encode($payload['shipping_address'], JSON_UNESCAPED_UNICODE),
            'notes' => $payload['notes'] ?? '',
        ]);

        $orderId = (int) $pdo->lastInsertId();
        foreach ($items as $item) {
            $insertItem = $pdo->prepare(
                'INSERT INTO order_items (order_id, product_id, quantity, unit_price, total_price)
                VALUES (:order_id, :product_id, :quantity, :unit_price, :total_price)'
            );
            $insertItem->execute([
                'order_id' => $orderId,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['total_price'],
            ]);
        }

        return $orderId;
    }

    public static function findAll(PDO $pdo, ?int $userId = null): array
    {
        if ($userId !== null) {
            $stmt = $pdo->prepare('SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC');
            $stmt->execute(['user_id' => $userId]);
        } else {
            $stmt = $pdo->query('SELECT * FROM orders ORDER BY created_at DESC');
        }

        return array_map(fn(array $row) => self::hydrate($row), $stmt->fetchAll());
    }

    public static function findById(PDO $pdo, int $id): ?array
    {
        $stmt = $pdo->prepare('SELECT * FROM orders WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $order = $stmt->fetch();
        if (!$order) {
            return null;
        }

        $itemsStmt = $pdo->prepare('SELECT * FROM order_items WHERE order_id = :order_id');
        $itemsStmt->execute(['order_id' => $id]);
        $items = $itemsStmt->fetchAll();

        $order['shipping_address'] = json_decode($order['shipping_address'] ?? '{}', true) ?: [];
        $order['items'] = $items;
        return self::hydrate($order);
    }

    public static function updateStatus(PDO $pdo, int $orderId, string $status): bool
    {
        $stmt = $pdo->prepare('UPDATE orders SET status = :status, updated_at = NOW() WHERE id = :id');
        return $stmt->execute(['id' => $orderId, 'status' => $status]);
    }

    private static function hydrate(array $row): array
    {
        return [
            'id' => (int) $row['id'],
            'order_number' => $row['order_number'],
            'user_id' => (int) $row['user_id'],
            'status' => $row['status'],
            'total_amount' => (float) $row['total_amount'],
            'currency' => $row['currency'],
            'payment_method' => $row['payment_method'],
            'shipping_address' => $row['shipping_address'] ?? [],
            'notes' => $row['notes'] ?? '',
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
            'items' => $row['items'] ?? [],
        ];
    }
}
