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
