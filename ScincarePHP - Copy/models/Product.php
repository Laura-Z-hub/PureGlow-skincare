<?php
declare(strict_types=1);

final class Product
{
    public static function findAll(PDO $pdo, ?string $category = null): array
    {
        if ($category !== null && $category !== 'all') {
            $stmt = $pdo->prepare('SELECT * FROM products WHERE category = :category ORDER BY name');
            $stmt->execute(['category' => $category]);
        } else {
            $stmt = $pdo->query('SELECT * FROM products ORDER BY name');
        }

        return array_map(fn(array $row) => self::hydrate($row), $stmt->fetchAll());
    }

    public static function findById(PDO $pdo, int $id): ?array
    {
        $stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ? self::hydrate($result) : null;
    }

    public static function create(PDO $pdo, array $data): int
    {
        $stmt = $pdo->prepare(
            'INSERT INTO products (name, slug, sku, category, brand, description, price, currency, stock, images, featured, created_at, updated_at)
            VALUES (:name, :slug, :sku, :category, :brand, :description, :price, :currency, :stock, :images, :featured, NOW(), NOW())'
        );

        $stmt->execute([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'sku' => $data['sku'],
            'category' => $data['category'],
            'brand' => $data['brand'],
            'description' => $data['description'],
            'price' => $data['price'],
            'currency' => $data['currency'],
            'stock' => $data['stock'],
            'images' => $data['images'],
            'featured' => $data['featured'],
        ]);

        return (int) $pdo->lastInsertId();
    }

    public static function update(PDO $pdo, int $id, array $data): bool
    {
        $stmt = $pdo->prepare(
            'UPDATE products SET name = :name, slug = :slug, sku = :sku, category = :category, brand = :brand,
            description = :description, price = :price, currency = :currency, stock = :stock, images = :images,
            featured = :featured, updated_at = NOW() WHERE id = :id'
        );

        return $stmt->execute([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'sku' => $data['sku'],
            'category' => $data['category'],
            'brand' => $data['brand'],
            'description' => $data['description'],
            'price' => $data['price'],
            'currency' => $data['currency'],
            'stock' => $data['stock'],
            'images' => $data['images'],
            'featured' => $data['featured'],
            'id' => $id,
        ]);
    }

    public static function delete(PDO $pdo, int $id): bool
    {
        $stmt = $pdo->prepare('DELETE FROM products WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    private static function hydrate(array $row): array
    {
        return [
            'id' => (int) $row['id'],
            'name' => $row['name'],
            'slug' => $row['slug'],
            'sku' => $row['sku'],
            'category' => $row['category'],
            'brand' => $row['brand'],
            'description' => $row['description'],
            'price' => (float) $row['price'],
            'currency' => $row['currency'],
            'stock' => (int) $row['stock'],
            'images' => json_decode($row['images'] ?? '[]', true) ?: [],
            'featured' => (bool) $row['featured'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
        ];
    }
}
