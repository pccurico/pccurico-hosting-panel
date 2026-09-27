<?php

declare(strict_types=1);

namespace Pccurico\HostingPanel\Models;

use Pccurico\HostingPanel\Core\Database;
use PDO;

final class Permission
{
    public static function all(): array
    {
        $stmt = Database::connection()->query("
            SELECT
                p.id,
                p.name,
                p.description,
                COUNT(rp.role_id) AS roles_count,
                GROUP_CONCAT(DISTINCT r.name ORDER BY r.name SEPARATOR ', ') AS role_names
            FROM permissions p
            LEFT JOIN role_permissions rp
                ON rp.permission_id = p.id
            LEFT JOIN roles r
                ON r.id = rp.role_id
            GROUP BY
                p.id,
                p.name,
                p.description
            ORDER BY p.name
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare("
            SELECT
                id,
                name,
                description
            FROM permissions
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->execute([$id]);

        $permission = $stmt->fetch(PDO::FETCH_ASSOC);

        return $permission ?: null;
    }

    public static function create(string $name, string $description = '', int $roleId = 0): int
    {
        $pdo = Database::connection();

        $stmt = $pdo->prepare("
            INSERT INTO permissions
                (name, description)
            VALUES
                (?, ?)
        ");

        $stmt->execute([
            $name,
            $description ?: null,
        ]);

        $id = (int) $pdo->lastInsertId();

        // If a roleId is provided, assign the permission to that role
        if ($roleId > 0) {
            self::assignToRole($id, $roleId);
        }

        return $id;
    }

    public static function assignToRole(int $permissionId, int $roleId): bool
    {
        $stmt = Database::connection()->prepare("
            INSERT IGNORE INTO role_permissions
                (role_id, permission_id)
            VALUES
                (?, ?)
        ");

        $stmt->execute([$roleId, $permissionId]);

        return $stmt->rowCount() > 0;
    }

    public static function revokeFromRole(int $permissionId, int $roleId): bool
    {
        $stmt = Database::connection()->prepare("
            DELETE FROM role_permissions
            WHERE role_id = ?
            AND permission_id = ?
        ");

        $stmt->execute([$roleId, $permissionId]);

        return $stmt->rowCount() > 0;
    }

    public static function isAssigned(int $permissionId, int $roleId): bool
    {
        $stmt = Database::connection()->prepare("
            SELECT COUNT(*) AS count
            FROM role_permissions
            WHERE role_id = ?
            AND permission_id = ?
        ");

        $stmt->execute([$roleId, $permissionId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) ($row['count'] ?? 0) > 0;
    }
}