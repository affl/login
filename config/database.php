<?php
function getConnection(): PDO {
    static $conn = null;

    if ($conn === null) {
        $dsn  = 'mysql:host=localhost;dbname=demoPHP;charset=utf8mb4';
        $user = 'root';
        $pass = 'root'; // o lo que uses

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        $conn = new PDO($dsn, $user, $pass, $options);
    }

    return $conn;
}