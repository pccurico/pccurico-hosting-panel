<?php

declare(strict_types=1);

namespace Pccurico\HostingPanel\Controllers;

use Pccurico\HostingPanel\Core\Database;
use Pccurico\HostingPanel\Core\View;

final class FilesController
{
    public function index(): void
    {
        $this->requireAdmin();

        $data = $this->getFilesData();

        View::render('files/index', [
            'title' => 'Archivos',
            'subtitle' => 'Explorador de archivos del servidor',
            'icon' => '📁',
            'section' => 'Sistema',
            'data' => $data,
            'csrf' => \Pccurico\HostingPanel\Middleware\Csrf::token(),
        ]);
    }

    private function getFilesData(): array
    {
        $result = [];

        // Directories to scan
        $directories = [
            '/var/www/html' => 'Sitios web',
            '/etc/apache2' => 'Configuración Apache',
            '/etc/php' => 'Configuración PHP',
            '/var/log' => 'Logs del sistema',
            '/home' => 'Usuarios del sistema',
        ];

        foreach ($directories as $path => $label) {
            if (is_dir($path)) {
                $files = glob($path . '/*') ?: [];
                $result[] = [
                    'name' => $label,
                    'path' => $path,
                    'count' => count($files),
                    'writable' => is_writable($path),
                    'readable' => is_readable($path),
                ];
            } else {
                $result[] = [
                    'name' => $label,
                    'path' => $path,
                    'count' => 0,
                    'writable' => false,
                    'readable' => false,
                ];
            }
        }

        // Disk usage
        $diskUsage = shell_exec('df -h / 2>/dev/null | tail -1') ?: '';
        $diskParts = preg_split('/\s+/', trim($diskUsage));

        return [
            'directories' => $result,
            'disk_usage' => [
                'filesystem' => $diskParts[0] ?? 'N/A',
                'size' => $diskParts[1] ?? 'N/A',
                'used' => $diskParts[2] ?? 'N/A',
                'available' => $diskParts[3] ?? 'N/A',
                'use_percent' => $diskParts[4] ?? 'N/A',
            ],
        ];
    }

    private function requireAdmin(): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
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

        $roles = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        if (
            !in_array('SuperAdministrador', $roles, true) &&
            !in_array('Administrador', $roles, true)
        ) {
            http_response_code(403);
            exit('Acceso denegado.');
        }
    }
}