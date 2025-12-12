<?php
    session_start();

    require_once __DIR__ . '/config/database.php';
    require_once __DIR__ . '/app/helpers.php'; 

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect('index.php');
    }

    $conn     = getConnection();
    $user     = $_POST['user'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = !empty($_POST['remember']);

    // Buscar usuario
    $sql = "SELECT id, email, password, status, remember_token_hash, remember_token_expires_at
            FROM users
            WHERE email = :user
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->execute([':user' => $user]);
    $userDB = $stmt->fetch();

    if (!$userDB) {
        $_SESSION['error'] = 'El correo no está registrado.';
        $_SESSION['old_user'] = $user;
        redirect('index.php');
    }

    if (!password_verify($password, $userDB['password'])) {
        $_SESSION['error'] = 'Contraseña incorrecta.';
        $_SESSION['old_user'] = $user;
        redirect('index.php');
    }

    if ($userDB['status'] === 'inactive') {
        $_SESSION['error'] = 'Tu cuenta está dada de baja. Contacta al administrador.';
        $_SESSION['old_user'] = $user;
        redirect('index.php');
    }
    
    // Login correcto
    $_SESSION['user_id'] = $userDB['id'];

    // Remember me
    if ($remember) {
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', time() + 60*60*24*30);

        $sql = "UPDATE users
                SET remember_token_hash = :token_hash,
                    remember_token_expires_at = :expires_at
                WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':token_hash' => $tokenHash,
            ':expires_at' => $expiresAt,
            ':id' => $userDB['id'],
        ]);

        $cookieValue = $userDB['id'] . ':' . $token;

        setcookie('remember_me', $cookieValue, [
            'expires'  => time() + 60*60*24*30,
            'path'     => '/',
            'secure'   => false,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    redirect('home.php');