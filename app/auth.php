<?php
// app/auth.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/helpers.php';

function checkRememberMe(): void
{
    if (isset($_SESSION['user_id']) || empty($_COOKIE['remember_me'])) {
        return;
    }

    $conn = getConnection();

    [$userId, $token] = explode(':', $_COOKIE['remember_me']);

    $sql = "SELECT id, remember_token_hash, remember_token_expires_at
            FROM users
            WHERE id = :id
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $userId]);
    $user = $stmt->fetch();

    if (!$user) {
        setcookie('remember_me', '', time() - 3600, '/');
        return;
    }

    if ($user['remember_token_expires_at'] <= date('Y-m-d H:i:s')) {
        setcookie('remember_me', '', time() - 3600, '/');
        return;
    }

    $tokenHash = hash('sha256', $token);

    if (!hash_equals($user['remember_token_hash'], $tokenHash)) {
        setcookie('remember_me', '', time() - 3600, '/');
        return;
    }

    // Token válido: reconstruimos sesión
    $_SESSION['user_id'] = $user['id'];
}

function authRequired(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    checkRememberMe();

    if (!isset($_SESSION['user_id'])) {
        redirect('index.php');
    }
}
