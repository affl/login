<?php
    require_once __DIR__ . '/../config/database.php';

    function getUserById(int $id): ?array
    {
        $conn = getConnection();

        $sql = "SELECT u.*, r.name AS role_name
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
