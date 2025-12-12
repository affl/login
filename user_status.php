<?php
    require_once __DIR__ . '/app/auth.php';
    require_once __DIR__ . '/app/helpers.php';

    requireRole('admin');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect('admin_users.php');
    }

    $id     = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $action = $_POST['action'] ?? '';

    if ($id <= 0 || !in_array($action, ['deactivate', 'activate'], true)) {
        redirect('admin_users.php');
    }

    // No permitir que el admin se dé de baja a sí mismo
    if ($id === $_SESSION['user_id']) {
        $_SESSION['error'] = "No puedes darte de baja a ti mismo.";
        redirect('admin_users.php');
    }

    $status = $action === 'deactivate' ? 'inactive' : 'active';

    $conn = getConnection();

    $sql = "UPDATE users SET status = :status WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':status' => $status,
        ':id'     => $id,
    ]);

    redirect('admin_users.php');