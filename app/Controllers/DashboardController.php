<?php

declare(strict_types=1);

namespace Pccurico\HostingPanel\Controllers;

use Pccurico\HostingPanel\Controllers\UnifiedViewController;

final class DashboardController extends UnifiedViewController
{
    public function index(): void
    {
        $this->requireAuthentication();

        $data = [
            'server'   => $this->serverInfo(),
            'resources'=> $this->resources(),
            'services' => $this->services(),
            'storage'  => $this->storage(),
        ];

        $this->renderPage(
            'dashboard/index',
            'Dashboard',
            'INFRAESTRUCTURA',
            '/dashboard',
            $data
        );
    }

    private function requireAuthentication(): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }

    private function command(string $command): string
    {
        $result = shell_exec($command);

        return is_string($result) ? trim($result) : '';
    }

    private function serverInfo(): array
    {
        $hostname = gethostname() ?: 'ia-server';

        $os = $this->command(
            "grep -oP '^PRETTY_NAME=\"\\K[^\"]+' /etc/os-release 2>/dev/null"
        );

        $kernel = php_uname('r');
        $architecture = php_uname('m');

        $uptimeSeconds = 0;

        if (is_readable('/proc/uptime')) {
            $contents = file_get_contents('/proc/uptime');

            if ($contents !== false) {
                $parts = preg_split('/\s+/', trim($contents));

                if (!empty($parts[0])) {
                    $uptimeSeconds = (int)floor((float)$parts[0]);
                }
            }
        }

        return [
            'hostname'     => $hostname,
            'os'           => $os ?: 'Ubuntu Server',
            'kernel'       => $kernel,
            'architecture' => $architecture,
            'uptime'       => $this->formatDuration($uptimeSeconds),
        ];
    }

    private function resources(): array
    {
        $cpuCores = (int)$this->command(
            "nproc 2>/dev/null"
        );

        if ($cpuCores < 1) {
            $cpuCores = 1;
        }

        $load = sys_getloadavg();

        $memoryTotal = 0;
        $memoryAvailable = 0;

        if (is_readable('/proc/meminfo')) {
            $meminfo = file_get_contents('/proc/meminfo');

            if ($meminfo !== false) {
                if (preg_match(
                    '/^MemTotal:\s+(\d+)\s+kB$/m',
                    $meminfo,
                    $matches
                )) {
                    $memoryTotal = (int)$matches[1] * 1024;
                }

                if (preg_match(
                    '/^MemAvailable:\s+(\d+)\s+kB$/m',
                    $meminfo,
                    $matches
                )) {
                    $memoryAvailable = (int)$matches[1] * 1024;
                }
            }
        }

        $memoryUsed = max(0, $memoryTotal - $memoryAvailable);

        $memoryPercent = $memoryTotal > 0
            ? round(($memoryUsed / $memoryTotal) * 100, 1)
            : 0;

        return [
            'cpu_cores'      => $cpuCores,
            'load_1'         => round((float)($load[0] ?? 0), 2),
            'load_5'         => round((float)($load[1] ?? 0), 2),
            'load_15'        => round((float)($load[2] ?? 0), 2),
            'memory_total'   => $this->formatBytes($memoryTotal),
            'memory_used'    => $this->formatBytes($memoryUsed),
            'memory_available'=> $this->formatBytes($memoryAvailable),
            'memory_percent' => $memoryPercent,
        ];
    }

    private function storage(): array
    {
        $path = '/';

        $total = @disk_total_space($path);
        $free  = @disk_free_space($path);

        if ($total === false || $free === false || $total <= 0) {
            return [
                'total'   => 'N/D',
                'used'    => 'N/D',
                'free'    => 'N/D',
                'percent' => 0,
            ];
        }

        $used = $total - $free;
        $percent = round(($used / $total) * 100, 1);

        return [
            'total'   => $this->formatBytes($total),
            'used'    => $this->formatBytes($used),
            'free'    => $this->formatBytes($free),
            'percent' => $percent,
        ];
    }

    private function services(): array
    {
        $definitions = [
            'apache2' => [
                'label' => 'Apache',
                'unit'  => 'apache2',
            ],
            'php8.3-fpm' => [
                'label' => 'PHP-FPM 8.3',
                'unit'  => 'php8.3-fpm',
            ],
            'mysql' => [
                'label' => 'MySQL',
                'unit'  => 'mysql',
            ],
            'cloudflared' => [
                'label' => 'Cloudflare Tunnel',
                'unit'  => 'cloudflared',
            ],
            'ollama' => [
                'label' => 'Ollama',
                'unit'  => 'ollama',
            ],
            'fail2ban' => [
                'label' => 'Fail2ban',
                'unit'  => 'fail2ban',
            ],
        ];

        $services = [];

        foreach ($definitions as $key => $definition) {
            $status = $this->command(
                'systemctl is-active ' .
                escapeshellarg($definition['unit']) .
                ' 2>/dev/null'
            );

            $active = $status === 'active';

            $services[] = [
                'key'    => $key,
                'label'  => $definition['label'],
                'status' => $active ? 'Activo' : ($status ?: 'No disponible'),
                'active' => $active,
            ];
        }

        return $services;
    }

    private function formatBytes(float|int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $index = 0;
        $value = (float)$bytes;

        while ($value >= 1024 && $index < count($units) - 1) {
            $value /= 1024;
            $index++;
        }

        return number_format($value, 1, ',', '.') . ' ' . $units[$index];
    }

    private function formatDuration(int $seconds): string
    {
        if ($seconds <= 0) {
            return 'N/D';
        }

        $days = intdiv($seconds, 86400);
        $seconds %= 86400;

        $hours = intdiv($seconds, 3600);
        $seconds %= 3600;

        $minutes = intdiv($seconds, 60);

        $parts = [];

        if ($days > 0) {
            $parts[] = $days . ' d';
        }

        if ($hours > 0) {
            $parts[] = $hours . ' h';
        }

        if ($minutes > 0) {
            $parts[] = $minutes . ' min';
        }

        return $parts
            ? implode(' ', $parts)
            : '< 1 min';
    }
}
