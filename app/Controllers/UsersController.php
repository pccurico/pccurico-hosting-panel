<?php

declare(strict_types=1);

namespace Pccurico\HostingPanel\Controllers;

use Pccurico\HostingPanel\Core\Database;
use Pccurico\HostingPanel\Middleware\Csrf;
use Pccurico\HostingPanel\Models\User;

final class UsersController
{
    public function index(): void
    {
        $this->requireAdmin();

        $this->render('users/index', [
            'users' => User::all(),
            'roles' => User::roles(),
            'permissions' => User::permissions((int) ($_SESSION['user_id'] ?? 0)),
            'csrf' => Csrf::token(),
        ]);
    }

    public function save(): void
    {
        $this->requireAdmin();

        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            http_response_code(419);
            exit('Token CSRF inválido.');
        }

        $name = trim((string) ($_POST['name'] ?? ''));
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $password = (string) ($_POST['password'] ?? '');
        $roleId = (int) ($_POST['role_id'] ?? 0);

        if (
            $name === '' ||
            !filter_var($email, FILTER_VALIDATE_EMAIL) ||
            $roleId <= 0 ||
            strlen($password) < 8
        ) {
            $this->redirect('/users?error=datos');
        }

        try {
            $id = User::create(
                $name,
                $email,
                $password,
                $roleId
            );

            $this->audit(
                'users.create',
                'user',
                $id
            );
        } catch (\Throwable $e) {
            error_log((string) $e);
            $this->redirect('/users?error=save');
        }

        $this->redirect('/users?saved=1');
    }

    public function toggle(): void
    {
        $this->requireAdmin();

        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            http_response_code(419);
            exit('Token CSRF inválido.');
        }

        $id = (int) ($_POST['id'] ?? 0);

        $target = User::find($id);
        $current = User::find(
            (int) ($_SESSION['user_id'] ?? 0)
        );

        if (!$target || !$current) {
            $this->redirect('/users?error=user');
        }

        if (
            strtolower((string) $target['email']) ===
            strtolower((string) $current['email'])
        ) {
            $this->redirect('/users?error=self');
        }

        User::setActive(
            $id,
            (int) $target['active'] !== 1
        );

        $this->audit(
            'users.toggle',
            'user',
            $id
        );

        $this->redirect('/users?saved=1');
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

    private function audit(
        string $action,
        string $targetType,
        int $targetId
    ): void {
        try {
            $stmt = Database::connection()->prepare("
                INSERT INTO audit_logs
                (
                    user_id,
                    action,
                    target_type,
                    target_id,
                    ip_address,
                    user_agent
                )
                VALUES
                (?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                (int) ($_SESSION['user_id'] ?? 0),
                $action,
                $targetType,
                (string) $targetId,
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null,
            ]);
        } catch (\Throwable $e) {
            error_log((string) $e);
        }
    }

    private function render(
        string $view,
        array $data = []
    ): void {
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

    private function redirect(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }
}
