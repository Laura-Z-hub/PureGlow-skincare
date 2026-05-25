<?php
declare(strict_types=1);

final class User
{
    public static function findByEmail(PDO $pdo, string $email): ?array
    {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function findById(PDO $pdo, int $id): ?array
    {
        $stmt = $pdo->prepare('SELECT id, name, email, email_verified_at, role, created_at, updated_at FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function create(PDO $pdo, string $name, string $email, string $password, string $role = 'customer'): int
    {
        return self::createWithPasswordHash($pdo, $name, $email, password_hash($password, PASSWORD_DEFAULT), $role);
    }

    public static function createWithPasswordHash(PDO $pdo, string $name, string $email, string $passwordHash, string $role = 'customer'): int
    {
        $stmt = $pdo->prepare(
            'INSERT INTO users (name, email, password, role, email_verified_at) VALUES (:name, :email, :password, :role, :email_verified_at)'
        );
        $stmt->execute([
            'name' => trim($name),
            'email' => trim($email),
            'password' => $passwordHash,
            'role' => $role,
            'email_verified_at' => date('Y-m-d H:i:s'),
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function isEmailVerified(array $user): bool
    {
        return ($user['role'] ?? '') === 'admin' || !empty($user['email_verified_at']);
    }
}
