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
            static function (array $a, array $b): int {
                return strcasecmp(
                    $a['server_name'] ?? '',
                    $b['server_name'] ?? ''
                );
            }
        );

        View::render('sites/index', [
            'title' => 'Sitios',
            'sites' => $sites,
            'apacheStatus' => $apache->getApacheStatus(),
            'apacheVersion' => $apache->getApacheVersion(),
            'virtualHosts' => $apache->getVirtualHostMap(),
        ]);
    }
}
