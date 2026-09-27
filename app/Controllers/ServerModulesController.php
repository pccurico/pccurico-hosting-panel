<?php

declare(strict_types=1);

namespace Pccurico\HostingPanel\Controllers;

final class ServerModulesController
{
    public function show(string $module): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $allowed = [
            'domains',
            'dns',
            'apache',
            'php',
            'mysql',
            'databases',
            'mail',
            'ssl',
            'backups',
            'logs',
            'settings',
            'tools',
            'audit',
        ];

        if (!in_array($module, $allowed, true)) {
            http_response_code(404);
            exit('Módulo no encontrado.');
        }

        $data = match ($module) {
            'domains' => $this->domains(),
            'dns' => $this->dns(),
            'apache' => $this->apache(),
            'php' => $this->php(),
            'mysql' => $this->mysql(),
            'databases' => $this->databases(),
            'mail' => $this->mail(),
            'ssl' => $this->ssl(),
            'backups' => $this->backups(),
            'logs' => $this->logs(),
            'settings' => $this->settings(),
            'tools' => $this->tools(),
            'audit' => $this->audit(),
        };

        $titles = [
            'domains' => ['Dominios', 'Dominios detectados en Apache', '🌐', 'Hosting'],
            'dns' => ['DNS', 'Estado y configuración DNS local', '🔗', 'Hosting'],
            'apache' => ['Apache', 'Estado y VirtualHosts del servidor', '🖥', 'Servidor'],
            'php' => ['PHP', 'PHP CLI y PHP-FPM', '🐘', 'Servidor'],
            'mysql' => ['MySQL', 'Estado del servidor MySQL', '🗄', 'Servidor'],
            'databases' => ['Bases de datos', 'Bases de datos detectadas', '💾', 'Hosting'],
            'mail' => ['Correo', 'Estado del sistema de correo', '✉', 'Hosting'],
            'ssl' => ['SSL', 'Certificados SSL detectados', '🔒', 'Seguridad'],
            'backups' => ['Backups', 'Estado de las copias de seguridad', '↻', 'Seguridad'],
            'logs' => ['Logs', 'Registros recientes del servidor', '▤', 'Servidor'],
            'settings' => ['Configuración', 'Configuración actual del servidor', '⚙', 'Sistema'],
            'tools' => ['Herramientas', 'Diagnóstico del servidor', '🛠', 'Sistema'],
            'audit' => ['Auditoría', 'Eventos administrativos registrados', '◉', 'Seguridad'],
        ];

        [$title, $subtitle, $icon, $section] = $titles[$module];

        $view = dirname(__DIR__) . '/Views/modules-real/' . $module . '.php';

        if (!is_file($view)) {
            http_response_code(500);
            exit('Vista no encontrada.');
        }

        extract([
            'module' => $module,
            'title' => $title,
            'subtitle' => $subtitle,
            'icon' => $icon,
            'section' => $section,
            'data' => $data,
        ], EXTR_SKIP);

        ob_start();

        require $view;

        $content = ob_get_clean();

        require dirname(__DIR__) . '/Views/layouts/app.php';
    }

    private function domains(): array
    {
        $result = [];

        $files = glob('/etc/apache2/sites-enabled/*.conf') ?: [];

        foreach ($files as $file) {
            $contents = @file_get_contents($file);

            if ($contents === false) {
                continue;
            }

            preg_match_all(
                '/ServerName\s+([^\s#]+)/i',
                $contents,
                $names
            );

            preg_match_all(
                '/ServerAlias\s+([^\s#]+)/i',
                $contents,
                $aliases
            );

            foreach ($names[1] ?? [] as $name) {
                $result[] = [
                    'domain' => $name,
                    'aliases' => implode(', ', $aliases[1] ?? []),
                    'file' => basename($file),
                ];
            }
        }

        return $result;
    }

    private function dns(): array
    {
        $services = [];

        $services[] = [
            'name' => 'dnsmasq',
            'status' => $this->serviceStatus('dnsmasq'),
        ];

        $services[] = [
            'name' => 'systemd-resolved',
            'status' => $this->serviceStatus('systemd-resolved'),
        ];

        $configs = glob('/etc/dnsmasq.d/*.conf') ?: [];

        return [
            'services' => $services,
            'configs' => array_map('basename', $configs),
            'resolv' => $this->command('resolvectl status'),
        ];
    }

    private function apache(): array
    {
        return [
            'status' => $this->serviceStatus('apache2'),
            'version' => trim((string) shell_exec('apache2 -v 2>/dev/null | head -1')),
            'syntax' => trim((string) shell_exec('apache2ctl configtest 2>&1')),
            'vhosts' => trim((string) shell_exec('apache2ctl -S 2>&1')),
            'configs' => $this->files('/etc/apache2/sites-enabled/*.conf'),
        ];
    }

    private function php(): array
    {
        return [
            'cli' => trim((string) shell_exec('php -v 2>/dev/null | head -1')),
            'fpm' => $this->serviceStatus('php8.3-fpm'),
            'modules' => trim((string) shell_exec('php -m 2>/dev/null')),
            'ini' => php_ini_loaded_file() ?: '',
        ];
    }

    private function mysql(): array
    {
        return [
            'status' => $this->serviceStatus('mysql'),
            'version' => trim((string) shell_exec('mysql --version 2>/dev/null')),
            'port' => trim((string) shell_exec(
                "ss -lnt 2>/dev/null | grep ':3306 ' || true"
            )),
        ];
    }

    private function databases(): array
    {
        return [
            'note' => 'Lectura de bases de datos pendiente de conexión administrativa segura.',
            'connection' => 'No se ejecutan consultas de escritura.',
        ];
    }

    private function mail(): array
    {
        $services = [];

        foreach (['postfix', 'exim4', 'dovecot'] as $service) {
            $services[$service] = $this->serviceStatus($service);
        }

        return [
            'services' => $services,
        ];
    }

    private function ssl(): array
    {
        $certificates = [];

        $paths = [
            '/etc/letsencrypt/live/*/fullchain.pem',
            '/etc/ssl/certs/*.pem',
        ];

        foreach ($paths as $pattern) {
            foreach (glob($pattern) ?: [] as $file) {
                $certificates[] = $file;
            }
        }

        return array_values(array_unique($certificates));
    }

    private function backups(): array
    {
        $paths = [
            '/root',
            '/var/backups',
            '/var/www',
        ];

        $result = [];

        foreach ($paths as $path) {
            if (!is_dir($path)) {
                continue;
            }

            $result[] = [
                'path' => $path,
                'size' => $this->directorySize($path),
            ];
        }

        return $result;
    }

    private function logs(): array
    {
        $files = [
            '/var/log/apache2/error.log',
            '/var/log/apache2/access.log',
            '/var/log/apache2/pccurico-hosting-panel-error.log',
            '/var/log/php8.3-fpm.log',
        ];

        $result = [];

        foreach ($files as $file) {
            if (!is_file($file)) {
                continue;
            }

            $lines = $this->tail($file, 20);

            $result[$file] = $lines;
        }

        return $result;
    }

    private function settings(): array
    {
        return [
            'hostname' => gethostname() ?: '',
            'os' => trim((string) shell_exec('lsb_release -ds 2>/dev/null')),
            'kernel' => php_uname('r'),
            'architecture' => php_uname('m'),
            'timezone' => date_default_timezone_get(),
            'php' => PHP_VERSION,
            'memory_limit' => ini_get('memory_limit'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
        ];
    }

    private function tools(): array
    {
        return [
            'disk' => $this->command('df -h /'),
            'memory' => $this->command('free -h'),
            'uptime' => $this->command('uptime'),
            'load' => sys_getloadavg(),
            'hostname' => gethostname() ?: '',
        ];
    }

    private function audit(): array
    {
        return [
            'note' => 'Los registros existentes se mostrarán cuando se conecte la capa de repositorio.',
            'table' => 'audit_logs',
        ];
    }

    private function serviceStatus(string $service): string
    {
        $status = trim((string) shell_exec(
            'systemctl is-active ' . escapeshellarg($service) . ' 2>/dev/null'
        ));

        if ($status === '') {
            return 'no instalado';
        }

        return $status;
    }

    private function command(string $command): string
    {
        return trim((string) shell_exec($command . ' 2>/dev/null'));
    }

    private function files(string $pattern): array
    {
        return array_map(
            'basename',
            glob($pattern) ?: []
        );
    }

    private function directorySize(string $path): string
    {
        return trim((string) shell_exec(
            'du -sh ' . escapeshellarg($path) . ' 2>/dev/null | awk \'{print $1}\''
        ));
    }

    private function tail(string $file, int $lines): array
    {
        $output = [];

        exec(
            'tail -n ' . $lines . ' ' . escapeshellarg($file) . ' 2>/dev/null',
            $output
        );

        return $output;
    }
}
