<?php

declare(strict_types=1);

namespace Pccurico\HostingPanel\Core;

use Dotenv\Dotenv;

final class Bootstrap
{
    public static function init(string $basePath): void
    {
        $dotenv = Dotenv::createImmutable($basePath);

        if (is_file($basePath . '/.env')) {
            $dotenv->safeLoad();
        }

        $timezone = $_ENV['APP_TIMEZONE'] ?? 'America/Santiago';

        date_default_timezone_set($timezone);

        if (session_status() === PHP_SESSION_NONE) {
            session_name($_ENV['SESSION_NAME'] ?? 'pccurico_hosting_session');

            session_set_cookie_params([
                'httponly' => true,
                'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
                'samesite' => 'Lax',
                'path' => '/',
            ]);

            session_start();
        }
    }
}
