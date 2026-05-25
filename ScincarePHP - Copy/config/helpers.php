<?php
declare(strict_types=1);

function sendJson(array $payload, int $statusCode = 200): void
{
    header('Content-Type: application/json; charset=utf-8');
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function parseJsonRequest(): array
{
    $body = file_get_contents('php://input');
    if ($body === false || $body === '') {
        return [];
    }

    $data = json_decode($body, true);
    return is_array($data) ? $data : [];
}

function requireSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function requireLogin(): array
{
    requireSession();

    if (empty($_SESSION['user'])) {
        sendJson(['error' => 'Authentication required'], 401);
    }

    return $_SESSION['user'];
}

function requireAdmin(): array
{
    $user = requireLogin();
    if (($user['role'] ?? '') !== 'admin') {
        sendJson(['error' => 'Admin privileges required'], 403);
    }
    return $user;
}

function getRequestParam(string $key, $default = null)
{
    return $_GET[$key] ?? $default;
}

function normalizeText(string $value): string
{
    return trim(filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS));
}

function validateEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function buildProductImageArray(array $images): string
{
    return json_encode(array_values(array_filter($images, fn($item) => is_string($item) && $item !== '')));
}

function getJsonBodyValue(array $data, string $key, string $default = ''): string
{
    return isset($data[$key]) && is_string($data[$key]) ? trim($data[$key]) : $default;
}

function booleanValue($value): bool
{
    if (is_bool($value)) {
        return $value;
    }
    if (is_string($value)) {
        return in_array(strtolower($value), ['1', 'true', 'yes', 'on'], true);
    }
    return (bool) $value;
}
