<?php

declare(strict_types=1);

use Pccurico\HostingPanel\Controllers\AuthController;
use Pccurico\HostingPanel\Controllers\DashboardController;
use Pccurico\HostingPanel\Controllers\SitesController;
use Pccurico\HostingPanel\Core\Router;

$router = new Router();

$auth = new AuthController();
$sitesController = new SitesController();

/*
 * Auth
 */
$router->get('/', function (): void {
    header('Location: /dashboard');
    exit;
});

$router->get('/login', [$auth::class, 'showLogin']);
$router->post('/login', [$auth::class, 'login']);
$router->get('/logout', [$auth::class, 'logout']);

/*
 * Dashboard
 */
$router->get(
    '/dashboard',
    [DashboardController::class, 'index']
);

/*
 * Hosting -> Sitios
 */
$router->get('/sites', [$sitesController::class, 'index']);

$router->get(
    '/sites/view',
    [$sitesController::class, 'view']
);

$router->get(
    '/sites/create',
    [$sitesController::class, 'create']
);

$router->post(
    '/sites/create',
    [$sitesController::class, 'create']
);

return $router;
