<?php

declare(strict_types=1);

namespace Pccurico\HostingPanel\Controllers;

use Pccurico\HostingPanel\Core\Database;
use Pccurico\HostingPanel\Core\View;
use Pccurico\HostingPanel\Middleware\Csrf;

final class AuthController
{
    public function showLogin(): void
    {
        if (!empty($_SESSION['user_id'])) {
            header('Location: /dashboard');
            exit;
        }

        View::render('auth/login', [
            'csrf' => Csrf::token(),
        ]);
    }

    public function login(): void
    {
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            http_response_code(419);
            exit('Solicitud inválida.');
        }

        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        $stmt = Database::connection()->prepare(
            'SELECT id, name, email, password_hash, active
             FROM users
             WHERE email = :email
             LIMIT 1'
        );

        $stmt->execute(['email' => $email]);

        $user = $stmt->fetch();

        if (
            !$user ||
            !(bool) $user['active'] ||
            !password_verify($password, $user['password_hash'])
        ) {
            usleep(400000);

            View::render('auth/login', [
                'csrf' => Csrf::token(),
                'error' => 'Credenciales incorrectas.',
            ]);

            return;
        }

        session_regenerate_id(true);

        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];

        header('Location: /dashboard');
        exit;
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'] ?? '',
                (bool) $params['secure'],
                (bool) $params['httponly']
            );
        }

        session_destroy();

        header('Location: /login');
        exit;
    }
}
