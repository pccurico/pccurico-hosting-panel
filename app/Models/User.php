<?php

declare(strict_types=1);

namespace Pccurico\HostingPanel\Models;

use Pccurico\HostingPanel\Core\Database;
use PDO;

final class User
{
    public static function all(): array
    {
        $stmt = Database::connection()->query("
            SELECT
                u.id,
                u.name,
                u.email,
                u.active,
                u.created_at,
                GROUP_CONCAT(
                    r.name
                    ORDER BY r.name
                    SEPARATOR ', '
                ) AS roles
            FROM users u
            LEFT JOIN user_roles ur
                ON ur.user_id = u.id
            LEFT JOIN roles r
                ON r.id = ur.role_id
            GROUP BY
                u.id,
                u.name,
                u.email,
                u.active,
                u.created_at
            ORDER BY u.id DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare("
            SELECT
                id,
                name,
                email,
                active,
                created_at,
                updated_at
            FROM users
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->execute([$id]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public static function create(
        string $name,
        string $email,
        string $password,
        int $roleId
    ): int {
        $pdo = Database::connection();

        $stmt = $pdo->prepare("
            INSERT INTO users
                (name, email, password_hash, active)
            VALUES
                (?, ?, ?, 1)
        ");

        $stmt->execute([
            $name,
            $email,
            password_hash($password, PASSWORD_DEFAULT),
        ]);

        $id = (int) $pdo->lastInsertId();

        self::setRole($id, $roleId);

        return $id;
    }

    public static function setActive(
        int $id,
        bool $active
    ): void {
        $stmt = Database::connection()->prepare("
            UPDATE users
            SET active = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $active ? 1 : 0,
            $id,
        ]);
    }

    public static function setRole(
        int $userId,
        int $roleId
    ): void {
        $pdo = Database::connection();

        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare("
                DELETE FROM user_roles
                WHERE user_id = ?
            ");

            $stmt->execute([$userId]);

            $stmt = $pdo->prepare("
                INSERT INTO user_roles
                    (user_id, role_id)
                VALUES
                    (?, ?)
            ");

            $stmt->execute([
                $userId,
                $roleId,
            ]);

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function roles(): array
    {
        return Database::connection()
            ->query("
                SELECT
                    id,
                    name
                FROM roles
                ORDER BY name
            ")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function permissions(int $userId): array
    {
        return Database::connection()
            ->query("
                SELECT DISTINCT
                    p.id,
                    p.name
                FROM permissions p
                INNER JOIN role_permissions rp
                    ON rp.permission_id = p.id
                INNER JOIN roles r
                    ON r.id = rp.role_id
                INNER JOIN user_roles ur
                    ON ur.role_id = r.id
                WHERE ur.user_id = ?
                ORDER BY p.name
            ", [$userId])
            ->fetchAll(PDO::FETCH_ASSOC);
    }
}
