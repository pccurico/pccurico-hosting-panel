<?php

declare(strict_types=1);

namespace Pccurico\HostingPanel\Controllers;

use Pccurico\HostingPanel\Core\Database;
use Pccurico\HostingPanel\Middleware\Csrf;
use Pccurico\HostingPanel\Models\Permission;
use Pccurico\HostingPanel\Models\Role;

final class PermissionsController
{
    public function index(): void
    {
        $this->requireAdmin();

        $permissions = Permission::all();
        $roles = Role::all();

        $this->render('permissions/index', [
            'data' => [
                'permissions' => $permissions,
                'roles' => $roles,
            ],
            'title' => 'Permisos',
            'subtitle' => 'Gestión de permisos del sistema',
        ]);
    }

    public function create(): void
    {
        $this->requireAdmin();

        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            http_response_code(419);
            exit('Token CSRF inválido.');
        }

        $name = trim((string) ($_POST['name'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $roleId = (int) ($_POST['role_id'] ?? 0);

        if ($name === '' || $roleId <= 0) {
            $this->redirect('/permissions?error=datos');
        }

        try {
            Permission::create($name, $description, $roleId);

            $this->audit('permissions.create', 'permission', 0);
        } catch (\Throwable $e) {
            error_log((string) $e);
            $this->redirect('/permissions?error=save');
        }

        $this->redirect('/permissions?created=1');
    }

    public function assign(): void
    {
        $this->requireAdmin();

        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            http_response_code(419);
            exit('Token CSRF inválido.');
        }

        $permissionId = (int) ($_POST['permission_id'] ?? 0);
        $roleId = (int) ($_POST['role_id'] ?? 0);

        if ($permissionId <= 0 || $roleId <= 0) {
            $this->redirect('/permissions?error=datos');
        }

        try {
            Permission::assignToRole($permissionId, $roleId);

            $this->audit('permissions.assign', 'permission', $permissionId);
        } catch (\Throwable $e) {
            error_log((string) $e);
            $this->redirect('/permissions?error=assign');
        }

        $this->redirect('/permissions?assigned=1');
    }

    public function revoke(): void
    {
        $this->requireAdmin();

        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            http_response_code(419);
            exit('Token CSRF inválido.');
        }

        $permissionId = (int) ($_POST['permission_id'] ?? 0);
        $roleId = (int) ($_POST['role_id'] ?? 0);

        if ($permissionId <= 0 || $roleId <= 0) {
            $this->redirect('/permissions?error=datos');
        }

        try {
            Permission::revokeFromRole($permissionId, $roleId);

            $this->audit('permissions.revoke', 'permission', $permissionId);
        } catch (\Throwable $e) {
            error_log((string) $e);
            $this->redirect('/permissions?error=revoke');
        }

        $this->redirect('/permissions?revoked=1');
    }

    private function requireAdmin(): void
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('/login');
        }

        $stmt = Database::connection()->prepare("
            SELECT r.name
            FROM user_roles ur
            INNER JOIN roles r
                ON r.id = ur.role_id
            WHERE ur.user_id = ?
        ");

        $stmt->execute([
            (int) $_SESSION['user_id']
        ]);

        $roles = $stmt->fetchAll(
            \PDO::FETCH_COLUMN
        );

        if (
            !in_array(
                'SuperAdministrador',
                $roles,
                true
            ) &&
            !in_array(
                'Administrador',
                $roles,
                true
            )
        ) {
            http_response_code(403);
            exit('Acceso denegado.');
        }
    }

    private function render(
        string $view,
        array $data = []
    ): void
    {
        extract($data, EXTR_SKIP);

        $file =
            dirname(__DIR__) .
            '/Views/' .
            $view .
            '.php';

        if (!is_file($file)) {
            http_response_code(500);
            exit('Vista no encontrada.');
        }

        require $file;
    }
}