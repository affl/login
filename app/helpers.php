<?php
    function fullName(array $user): string
    {
        return implode(' ', array_filter([
            $user['first_name'] ?? '',
            $user['last_name'] ?? '',
            $user['middle_name'] ?? '',
        ]));
    }

    function redirect(string $url): never
    {
        header("Location: $url");
        exit;
    }
