<?php
declare(strict_types=1);

final class Cart
{
    public static function getByUser(PDO $pdo, int $userId): array
    {
        $stmt = $pdo->prepare('SELECT items FROM carts WHERE user_id = :user_id LIMIT 1');
        $stmt->execute(['user_id' => $userId]);
        $row = $stmt->fetch();
        if (!$row) {
            return [];
        }

        $items = json_decode($row['items'], true);
        return is_array($items) ? $items : [];
    }

    public static function save(PDO $pdo, int $userId, array $items): bool
    {
        $json = json_encode(array_values($items), JSON_UNESCAPED_UNICODE);
        $stmt = $pdo->prepare('SELECT id FROM carts WHERE user_id = :user_id LIMIT 1');
        $stmt->execute(['user_id' => $userId]);
        $existing = $stmt->fetch();

        if ($existing) {
            $update = $pdo->prepare('UPDATE carts SET items = :items, updated_at = NOW() WHERE user_id = :user_id');
            return $update->execute(['items' => $json, 'user_id' => $userId]);
        }

        $insert = $pdo->prepare('INSERT INTO carts (user_id, items, updated_at) VALUES (:user_id, :items, NOW())');
        return $insert->execute(['user_id' => $userId, 'items' => $json]);
    }

    public static function clear(PDO $pdo, int $userId): bool
    {
        $stmt = $pdo->prepare('DELETE FROM carts WHERE user_id = :user_id');
        return $stmt->execute(['user_id' => $userId]);
    }
}
