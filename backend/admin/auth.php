<?php
declare(strict_types=1);
require_once __DIR__ . '/../db.php';

ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
session_name('namhong_admin');
session_set_cookie_params(['lifetime' => SESSION_LIFETIME, 'httponly' => true, 'samesite' => 'Lax']);
session_start();

/** Redirects to login.php unless an admin is currently logged in. */
function require_login(): void
{
    if (empty($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit;
    }
    // idle timeout in addition to the cookie lifetime
    if (!empty($_SESSION['last_active']) && (time() - $_SESSION['last_active']) > SESSION_LIFETIME) {
        session_unset();
        session_destroy();
        header('Location: login.php?expired=1');
        exit;
    }
    $_SESSION['last_active'] = time();
}

/** Returns (and creates if needed) a per-session CSRF token. */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

/** Verifies a submitted CSRF token using a timing-safe comparison. */
function csrf_check(string $submitted): bool
{
    return !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $submitted);
}

function h(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
