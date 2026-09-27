<?php

declare(strict_types=1);

namespace Pccurico\HostingPanel\Controllers;

use Pccurico\HostingPanel\Services\ApacheService;
use Pccurico\HostingPanel\Core\View;

final class SitesController
{
    public function index(): void
    {
        $apache = new ApacheService();

        $sites = $apache->getSites();

        usort(
            $sites,
            static fn(array $a, array $b): int =>
                strcasecmp(
                    $a['server_name'] ?? '',
                    $b['server_name'] ?? ''
                )
        );

        View::render('sites/index', [
            'title' => 'Sitios',
            'sites' => $sites,
            'apacheStatus' => $apache->getApacheStatus(),
            'apacheVersion' => $apache->getApacheVersion(),
        ]);
    }


    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            View::render('sites/create', [
                'title' => 'Crear sitio',
            ]);

            return;
        }

        $serverName = trim((string)($_POST['server_name'] ?? ''));
        $documentRoot = trim((string)($_POST['document_root'] ?? ''));
        $aliasesRaw = trim((string)($_POST['aliases'] ?? ''));

        if (
            $serverName === '' ||
            !preg_match(
                '/^(?=.{1,253}$)([a-zA-Z0-9](?:[a-zA-Z0-9.-]*[a-zA-Z0-9])?)$/',
                $serverName
            )
        ) {
            http_response_code(422);
            View::render('sites/create', [
                'title' => 'Crear sitio',
                'error' => 'Dominio inválido.',
            ]);
            return;
        }

        if (
            $documentRoot === '' ||
            !str_starts_with($documentRoot, '/var/www/')
        ) {
            http_response_code(422);
            View::render('sites/create', [
                'title' => 'Crear sitio',
                'error' => 'DocumentRoot inválido. Debe estar dentro de /var/www/.',
            ]);
            return;
        }

        $aliases = [];

        if ($aliasesRaw !== '') {
            foreach (preg_split('/\s+/', $aliasesRaw) as $alias) {
                if (
                    $alias !== '' &&
                    preg_match(
                        '/^(?=.{1,253}$)([a-zA-Z0-9](?:[a-zA-Z0-9.-]*[a-zA-Z0-9])?)$/',
                        $alias
                    )
                ) {
                    $aliases[] = $alias;
                }
            }
        }

        $filename = preg_replace(
            '/[^a-zA-Z0-9._-]+/',
            '-',
            $serverName
        ) . '.conf';

        $apacheConfig = "/etc/apache2/sites-available/" . $filename;

        if (file_exists($apacheConfig)) {
            http_response_code(409);

            View::render('sites/create', [
                'title' => 'Crear sitio',
                'error' => 'Ya existe una configuración con ese nombre.',
            ]);

            return;
        }

        $aliasesConfig = '';

        if ($aliases !== []) {
            $aliasesConfig =
                "\n    ServerAlias "
                . implode(' ', array_unique($aliases));
        }

        $config = <<<APACHE
<VirtualHost *:80>
    ServerName {$serverName}{$aliasesConfig}

    DocumentRoot {$documentRoot}

    <Directory {$documentRoot}>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    <FilesMatch \.php$>
        SetHandler "proxy:unix:/run/php/php8.3-fpm.sock|fcgi://localhost/"
    </FilesMatch>

    ErrorLog \${APACHE_LOG_DIR}/{$serverName}-error.log
    CustomLog \${APACHE_LOG_DIR}/{$serverName}-access.log combined
</VirtualHost>
APACHE;

        $result = [
            'config' => $config,
            'file' => $apacheConfig,
            'server_name' => $serverName,
            'document_root' => $documentRoot,
        ];

        View::render('sites/create', [
            'title' => 'Crear sitio',
            'preview' => $result,
        ]);
    }


    public function view(): void
    {
        $config = trim((string)($_GET['config'] ?? ''));
        $server = trim((string)($_GET['server'] ?? ''));

        $apache = new ApacheService();

        $site = $apache->findSite($config, $server);

        if ($site === null) {
            http_response_code(404);

            View::render('sites/not-found', [
                'title' => 'Sitio no encontrado',
            ]);

            return;
        }

        View::render('sites/view', [
            'title' => $site['server_name'],
            'site' => $site,
        ]);
    }
}
