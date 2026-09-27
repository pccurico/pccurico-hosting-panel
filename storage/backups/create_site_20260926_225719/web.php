<?php

declare(strict_types=1);

use Pccurico\HostingPanel\Controllers\AuthController;
use Pccurico\HostingPanel\Controllers\DashboardController;
use Pccurico\HostingPanel\Core\Router;

$router = new Router();

$auth = new AuthController();

$router->get('/', function (): void {
    header('Location: /dashboard');
    exit;
});

$router->get('/login', [$auth::class, 'showLogin']);
$router->post('/login', [$auth::class, 'login']);
$router->get('/logout', [$auth::class, 'logout']);

$router->get(
    '/dashboard',
    [DashboardController::class, 'index']
);

return $router;

/*
 * Hosting -> Sitios
 */
$sitesController = new \Pccurico\HostingPanel\Controllers\SitesController();

$router->get('/sites', [$sitesController::class, 'index']);

$router->get('/sites/view', [$sitesController::class, 'view']);
