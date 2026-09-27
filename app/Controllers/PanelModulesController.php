<?php

declare(strict_types=1);

namespace Pccurico\HostingPanel\Controllers;

final class PanelModulesController
{
    private array $modules = [
        'domains' => [
            'title' => 'Dominios',
            'subtitle' => 'Administración de dominios y aliases',
            'icon' => '🌐',
            'section' => 'Hosting',
        ],
        'dns' => [
            'title' => 'DNS',
            'subtitle' => 'Gestión de zonas y registros DNS',
            'icon' => '🔗',
            'section' => 'Hosting',
        ],
        'apache' => [
            'title' => 'Apache',
            'subtitle' => 'Servidor web y VirtualHosts',
            'icon' => '🖥',
            'section' => 'Servidor',
        ],
        'php' => [
            'title' => 'PHP',
            'subtitle' => 'Versiones, PHP-FPM y configuración',
            'icon' => '🐘',
            'section' => 'Servidor',
        ],
        'mysql' => [
            'title' => 'MySQL',
            'subtitle' => 'Servidor y estado de MySQL',
            'icon' => '🗄',
            'section' => 'Servidor',
        ],
        'databases' => [
            'title' => 'Bases de datos',
            'subtitle' => 'Bases de datos, usuarios y permisos',
            'icon' => '💾',
            'section' => 'Hosting',
        ],
        'mail' => [
            'title' => 'Correo',
            'subtitle' => 'Cuentas y configuración de correo',
            'icon' => '✉',
            'section' => 'Hosting',
        ],
        'ssl' => [
            'title' => 'SSL',
            'subtitle' => 'Certificados y seguridad HTTPS',
            'icon' => '🔒',
            'section' => 'Seguridad',
        ],
        'backups' => [
            'title' => 'Backups',
            'subtitle' => 'Copias de seguridad y restauración',
            'icon' => '↻',
            'section' => 'Seguridad',
        ],
        'logs' => [
            'title' => 'Logs',
            'subtitle' => 'Registros del servidor y aplicaciones',
            'icon' => '▤',
            'section' => 'Servidor',
        ],
        'settings' => [
            'title' => 'Configuración',
            'subtitle' => 'Configuración general del panel',
            'icon' => '⚙',
            'section' => 'Sistema',
        ],
        'tools' => [
            'title' => 'Herramientas',
            'subtitle' => 'Utilidades administrativas del servidor',
            'icon' => '🛠',
            'section' => 'Sistema',
        ],
        'audit' => [
            'title' => 'Auditoría',
            'subtitle' => 'Actividad y eventos administrativos',
            'icon' => '◉',
            'section' => 'Seguridad',
        ],
        'roles' => [
            'title' => 'Roles',
            'subtitle' => 'Administración de roles de acceso',
            'icon' => '♟',
            'section' => 'Usuarios',
        ],
        'permissions' => [
            'title' => 'Permisos',
            'subtitle' => 'Permisos y capacidades del panel',
            'icon' => '✓',
            'section' => 'Usuarios',
        ],
    ];

    public function show(string $module): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        if (!isset($this->modules[$module])) {
            http_response_code(404);
            exit('Módulo no encontrado.');
        }

        $data = $this->modules[$module];

        $view = dirname(__DIR__) . '/Views/modules/' . $module . '.php';

        if (!is_file($view)) {
            http_response_code(500);
            exit('Vista del módulo no encontrada.');
        }

        extract([
            'module' => $module,
            'title' => $data['title'],
            'subtitle' => $data['subtitle'],
            'icon' => $data['icon'],
            'section' => $data['section'],
        ], EXTR_SKIP);

        require $view;
    }
}
