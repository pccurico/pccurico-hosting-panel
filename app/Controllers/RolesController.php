<?php

declare(strict_types=1);

namespace Pccurico\HostingPanel\Controllers;

use Pccurico\HostingPanel\Core\Database;
use Pccurico\HostingPanel\Core\Response;
use Pccurico\HostingPanel\Middleware\Csrf;
use Pccurico\HostingPanel\Models\Permission;
use Pccurico\HostingPanel\Models\Role;
use Pccurico\HostingPanel\Models\User;

final class RolesController
{
    public function index(): void
    {
        $this->requireAdmin();

        $data = Role::all();

        $this->render('roles/index', [
            'data' => $data,
            'title' => 'Roles',
            'subtitle' => 'Gestión de roles de usuario',
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

        if ($name === '') {
            $this->redirect('/roles?error=name');
        }

        try {
            Role::create($name, $description);

            $this->audit('roles.create', 'role', 0);
        } catch (\Throwable $e) {
            error_log((string) $e);
            $this->redirect('/roles?error=save');
        }

        $this->redirect('/roles?created=1');
    }

    public function show(int $id): void
    {
        $this->requireAdmin();

        $role = Role::find($id);

        if (!$role) {
            $this->redirect('/roles');
        }

        $permissions = Permission::all();

        $assigned = [];
        foreach ($permissions as $perm) {
            $assigned[] = Permission::isAssigned($perm['id'], $role['id']);
        }

        $this->render('roles/show', [
            'role' => $role,
            'permissions' => $permissions,
            'assigned' => $assigned,
            'title' => 'Ver rol: ' . $role['name'],
        ]);
    }

    public function edit(int $id): void
    {
        $this->requireAdmin();

        $role = Role::find($id);

        if (!$role) {
            $this->redirect('/roles');
        }

        $permissions = Permission::all();

        $assigned = [];
        foreach ($permissions as $perm) {
            $assigned[] = Permission::isAssigned($perm['id'], $role['id']);
        }

        $this->render('roles/edit', [
            'role' => $role,
            'permissions' => $permissions,
            'assigned' => $assigned,
            'title' => 'Editar rol: ' . $role['name'],
        ]);
    }

    public function update(int $id): void
    {
        $this->requireAdmin();

        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            http_response_code(419);
            exit('Token CSRF inválido.');
        }

        $name = trim((string) ($_POST['name'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));

        if ($name === '') {
            $this->redirect('/roles/' . $id . '?error=name');
        }

        try {
            Role::update($id, $name, $description);

            $this->audit('roles.update', 'role', $id);
        } catch (\Throwable $e) {
            error_log((string) $e);
            $this->redirect('/roles/' . $id . '?error=save');
        }

        $this->redirect('/roles/' . $id . '?updated=1');
    }

    public function destroy(int $id): void
    {
        $this->requireAdmin();

        try {
            Role::delete($id);

            $this->audit('roles.destroy', 'role', $id);
        } catch (\Throwable $e) {
            error_log((string) $e);
            $this->redirect('/roles?error=delete');
        }

        $this->redirect('/roles?deleted=1');
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