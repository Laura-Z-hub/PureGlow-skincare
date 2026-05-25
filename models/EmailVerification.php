<?php
declare(strict_types=1);

require_once __DIR__ . '/EmailSender.php';
require_once __DIR__ . '/User.php';

final class EmailVerification
{
    public static function hasPending(PDO $pdo, string $email): bool
    {
        $stmt = $pdo->prepare('SELECT id FROM pending_registrations WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => trim($email)]);

        return (bool)$stmt->fetch();
    }

    public static function startRegistration(PDO $pdo, string $name, string $email, string $password): bool
    {
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $stmt = $pdo->prepare(
            'INSERT INTO pending_registrations (name, email, password_hash, verification_token, expires_at)
            VALUES (:name, :email, :password_hash, :verification_token, DATE_ADD(NOW(), INTERVAL 1 DAY))
            ON DUPLICATE KEY UPDATE
                name = VALUES(name),
                password_hash = VALUES(password_hash),
                verification_token = VALUES(verification_token),
                created_at = NOW(),
                expires_at = VALUES(expires_at)'
        );
        $stmt->execute([
            'name' => trim($name),
            'email' => trim($email),
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'verification_token' => $tokenHash,
        ]);

        $baseUrl = self::baseUrl();
        $link = $baseUrl . '/verify-email.php?token=' . urlencode($token);
        $message = implode("\r\n", [
            'Hi ' . $name . ',',
            '',
            'Please verify your PureGlow account by opening this link:',
            $link,
            '',
            'If you did not create this account, you can ignore this email.',
            '',
            'PureGlow',
        ]);

        return EmailSender::send($email, 'Verify your PureGlow email', $message);
    }

    public static function verify(PDO $pdo, string $token): bool
    {
        if ($token === '') {
            return false;
        }

        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare(
                'SELECT *
                FROM pending_registrations
                WHERE verification_token = :token
                  AND expires_at >= NOW()
                LIMIT 1'
            );
            $stmt->execute(['token' => hash('sha256', $token)]);
            $pending = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$pending || User::findByEmail($pdo, $pending['email'])) {
                $pdo->rollBack();
                return false;
            }

            User::createWithPasswordHash(
                $pdo,
                $pending['name'],
                $pending['email'],
                $pending['password_hash']
            );

            $delete = $pdo->prepare('DELETE FROM pending_registrations WHERE id = :id');
            $delete->execute(['id' => $pending['id']]);
            $pdo->commit();

            return true;
        } catch (Throwable $error) {
            $pdo->rollBack();
            throw $error;
        }
    }

    private static function baseUrl(): string
    {
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

        return $scheme . '://' . $host . '/skincare';
    }
}
