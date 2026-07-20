<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

/**
 * Returns a shared PDO connection (created once per request).
 */
function get_db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false, // real prepared statements
        ]);
    }
    return $pdo;
}

/**
 * Very small IP-based rate limiter shared by both public form handlers.
 * Returns true if the request should be BLOCKED (too many recent attempts).
 */
function is_rate_limited(PDO $pdo, string $ip, string $formName): bool
{
    $stmt = $pdo->prepare(
        'SELECT COUNT(*) AS n FROM submission_log
         WHERE ip_address = :ip AND form_name = :form
           AND created_at >= (NOW() - INTERVAL :minutes MINUTE)'
    );
    $stmt->bindValue(':ip', $ip);
    $stmt->bindValue(':form', $formName);
    $stmt->bindValue(':minutes', RATE_LIMIT_MINUTES, PDO::PARAM_INT);
    $stmt->execute();
    $count = (int) $stmt->fetch()['n'];

    $log = $pdo->prepare('INSERT INTO submission_log (ip_address, form_name) VALUES (:ip, :form)');
    $log->execute([':ip' => $ip, ':form' => $formName]);

    return $count >= RATE_LIMIT_MAX;
}

/** Sends a JSON response and stops execution. */
function json_respond(bool $ok, string $message = '', array $extra = []): void
{
    header('Content-Type: application/json; charset=utf-8');
    http_response_code($ok ? 200 : 400);
    echo json_encode(array_merge(['ok' => $ok, 'message' => $message], $extra), JSON_UNESCAPED_UNICODE);
    exit;
}
