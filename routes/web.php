<?php

declare(strict_types=1);

use Pccurico\HostingPanel\Controllers\AuthController;
use Pccurico\HostingPanel\Controllers\DashboardController;
use Pccurico\HostingPanel\Controllers\SitesController;
use Pccurico\HostingPanel\Controllers\UsersController;
use Pccurico\HostingPanel\Controllers\PanelModulesController;
use Pccurico\HostingPanel\Controllers\ServerModulesController;
use Pccurico\HostingPanel\Core\Router;

$router = new Router();

$auth = new AuthController();
$sitesController = new SitesController();
$usersController = new UsersController();
$panelModulesController = new PanelModulesController();
$serverModulesController = new ServerModulesController();

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


$router->get(
    '/users',
    [$usersController, 'index']
);

$router->post(
    '/users/save',
    [$usersController, 'save']
);

$router->post(
    '/users/toggle',
    [$usersController, 'toggle']
);


/*
 * Modulos del Hosting Panel
 */
$router->get('/domains', function () use ($serverModulesController): void {
    $serverModulesController->show('domains');
});

$router->get('/dns', function () use ($serverModulesController): void {
    $serverModulesController->show('dns');
});

$router->get('/apache', function () use ($serverModulesController): void {
    $serverModulesController->show('apache');
});

$router->get('/php', function () use ($serverModulesController): void {
    $serverModulesController->show('php');
});

$router->get('/mysql', function () use ($serverModulesController): void {
    $serverModulesController->show('mysql');
});

$router->get('/databases', function () use ($serverModulesController): void {
    $serverModulesController->show('databases');
});

$router->get('/mail', function () use ($serverModulesController): void {
    $serverModulesController->show('mail');
});

$router->get('/ssl', function () use ($serverModulesController): void {
    $serverModulesController->show('ssl');
});

$router->get('/backups', function () use ($serverModulesController): void {
    $serverModulesController->show('backups');
});

$router->get('/logs', function () use ($serverModulesController): void {
    $serverModulesController->show('logs');
});

$router->get('/settings', function () use ($serverModulesController): void {
    $serverModulesController->show('settings');
});

$router->get('/tools', function () use ($serverModulesController): void {
    $serverModulesController->show('tools');
});

$router->get('/audit', function () use ($serverModulesController): void {
    $serverModulesController->show('audit');
});

/*
 * Roles y Permisos (gestión de usuarios)
 */
$router->get('/roles', [$usersController, 'index']);
$router->get('/permissions', [$usersController, 'index']);

return $router;

