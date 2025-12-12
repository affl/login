<?php
    require_once __DIR__ . '/../config/database.php';

    function getUserById(int $id): ?array
    {
        $conn = getConnection();

        $sql = "SELECT u.*, r.name AS role_name, r.description AS role_description
                FROM users u
                LEFT JOIN roles r ON u.role_id = r.id
                WHERE u.id = :id
                LIMIT 1";

        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $id]);

        $user = $stmt->fetch();
        return $user ?: null;
    }

    function getAllUsers(): array
    {
        $conn = getConnection();

        $sql = "SELECT u.*, r.name AS role_name
                FROM users u
                LEFT JOIN roles r ON u.role_id = r.id
                ORDER BY u.first_name ASC";

        $stmt = $conn->query($sql);
        return $stmt->fetchAll();
    }
    
    function currentUser(): ?array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['user_id'])) {
            return null;
        }

        return getUserById((int) $_SESSION['user_id']);
    }