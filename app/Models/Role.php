<?php

declare(strict_types=1);

namespace Pccurico\HostingPanel\Models;

use Pccurico\HostingPanel\Core\Database;
use PDO;

final class Role
{
    public static function all(): array
    {
        $stmt = Database::connection()->query("
            SELECT
                r.id,
                r.name,
                r.description,
                COUNT(ur.user_id) AS users_count,
                COUNT(rp.permission_id) AS permissions_count,
                r.created_at,
                r.updated_at
            FROM roles r
            LEFT JOIN user_roles ur
                ON ur.role_id = r.id
            LEFT JOIN role_permissions rp
                ON rp.role_id = r.id
            GROUP BY
                r.id,
                r.name,
                r.description,
                r.created_at,
                r.updated_at
            ORDER BY r.name
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare("
            SELECT
                id,
                name,
                description,
                created_at,
                updated_at
            FROM roles
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->execute([$id]);

        $role = $stmt->fetch(PDO::FETCH_ASSOC);

        return $role ?: null;
    }

    public static function create(string $name, string $description = ''): int
    {
        $pdo = Database::connection();

        $stmt = $pdo->prepare("
            INSERT INTO roles
                (name, description)
            VALUES
                (?, ?)
        ");

        $stmt->execute([
            $name,
            $description ?: null,
        ]);

        return (int) $pdo->lastInsertId();
    }

    public static function update(int $id, string $name, string $description = ''): void
    {
        $stmt = Database::connection()->prepare("
            UPDATE roles
            SET name = ?, description = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $name,
            $description ?: null,
            $id,
        ]);
    }

    public static function delete(int $id): void
    {
        $stmt = Database::connection()->prepare("
            DELETE FROM roles
            WHERE id = ?
        ");

        $stmt->execute([$id]);
    }
}