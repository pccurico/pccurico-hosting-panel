<?php

declare(strict_types=1);

namespace Pccurico\HostingPanel\Controllers;

use Pccurico\HostingPanel\Core\Database;
use Pccurico\HostingPanel\Core\View;

final class CronController
{
    public function index(): void
    {
        $this->requireAdmin();

        $data = $this->getCronData();

        View::render('cron/index', [
            'title' => 'Tareas Cron',
            'subtitle' => 'Gestión de tareas programadas del sistema',
            'icon' => '⏰',
            'section' => 'Sistema',
            'data' => $data,
            'csrf' => \Pccurico\HostingPanel\Middleware\Csrf::token(),
        ]);
    }

    private function getCronData(): array
    {
        $result = [];

        // System crontabs
        $systemCronDir = '/etc/cron.d/';
        if (is_dir($systemCronDir)) {
            $files = glob($systemCronDir . '*.conf') ?: glob($systemCronDir . '*') ?: [];
            foreach ($files as $file) {
                $contents = @file_get_contents($file);
                if ($contents !== false) {
                    $result[] = [
                        'type' => 'system',
                        'name' => basename($file),
                        'path' => $file,
                        'content' => trim($contents),
                        'active' => true,
                    ];
                }
            }
        }

        // User crontabs
        $output = shell_exec('crontab -l 2>/dev/null || true');
        if ($output && trim($output) !== '') {
            $result[] = [
                'type' => 'user',
                'name' => 'crontab de usuario actual',
                'path' => 'crontab -l',
                'content' => trim($output),
                'active' => true,
            ];
        }

        // Cron service status
        $cronStatus = trim((string) shell_exec('systemctl is-active cron 2>/dev/null || systemctl is-active crond 2>/dev/null || echo "unknown"'));

        return [
            'jobs' => $result,
            'service_status' => $cronStatus,
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