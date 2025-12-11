<?php
    session_start();

    require_once __DIR__ . '/config/database.php';
    $conn = getConnection();

    // 1. Si había un usuario logueado, borrar su token persistente en BD
    if (isset($_SESSION['user_id'])) {

        $sql = "UPDATE users
                SET remember_token_hash = NULL,
                    remember_token_expires_at = NULL
                WHERE id = :id";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':id' => $_SESSION['user_id']
        ]);
    }

    // 2. Borrar cookie remember_me (si existe)
    if (isset($_COOKIE['remember_me'])) {
        setcookie('remember_me', '', time() - 3600, '/'); // expirarla
    }

    // 3. Borrar variables de sesión
    session_unset();
    session_destroy();

    // 4. Redirigir al login
    header("Location: index.php");
    exit;
