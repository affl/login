<?php
// app/auth.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/user.php';

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

/**
 * Verifica si el usuario actual tiene un rol específico.
 * $roles puede ser un string ('admin') o un array (['admin', 'coordinator'])
 */
function userHasRole(string|array $roles): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $user = currentUser();   // 👈 la sacamos de user.php
    if (!$user) {
        return false;
    }

    $userRole = $user['role_name'] ?? null;   // alias de la BD

    if ($userRole === null) {
        return false;
    }

    if (is_array($roles)) {
        return in_array($userRole, $roles, true);
    }

    return $userRole === $roles;
}

/**
 * Protege una página para que solo ciertos roles puedan entrar.
 * Ejemplo:
 *   requireRole('admin');
 *   requireRole(['admin', 'coordinator']);
 */
function requireRole(string|array $roles): void
{
    // Primero nos aseguramos de que haya sesión:
    authRequired();

    if (!userHasRole($roles)) {
        // Aquí decides qué hacer: redirigir, mostrar 403, etc.
        // header('HTTP/1.1 403 Forbidden');
        // echo 'No tienes permisos para acceder a esta sección.';
        // redirect('home.php'); // simple y elegante
        header('Location: /PHPMater/403.php');
        exit;
    }
}