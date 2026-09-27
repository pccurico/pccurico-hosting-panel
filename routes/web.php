<?php

declare(strict_types=1);

use Pccurico\HostingPanel\Controllers\AuthController;
use Pccurico\HostingPanel\Controllers\CronController;
use Pccurico\HostingPanel\Controllers\DashboardController;
use Pccurico\HostingPanel\Controllers\FilesController;
use Pccurico\HostingPanel\Controllers\PermissionsController;
use Pccurico\HostingPanel\Controllers\RolesController;
use Pccurico\HostingPanel\Controllers\SitesController;
use Pccurico\HostingPanel\Controllers\UsersController;
use Pccurico\HostingPanel\Controllers\PanelModulesController;
use Pccurico\HostingPanel\Controllers\ServerModulesController;
use Pccurico\HostingPanel\Controllers\ServerSetupController;
use Pccurico\HostingPanel\Core\Router;

$router = new Router();

$auth = new AuthController();
$sitesController = new SitesController();
$usersController = new UsersController();
$cronController = new CronController();
$filesController = new FilesController();
$panelModulesController = new PanelModulesController();
$serverModulesController = new ServerModulesController();
$rolesController = new RolesController();
$permissionsController = new PermissionsController();
$serverSetupController = new ServerSetupController();

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
 * Cron y Archivos
 */
$router->get('/cron', [$cronController, 'index']);
$router->get('/files', [$filesController, 'index']);

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
$router->get('/roles', [RolesController::class, 'index']);
$router->get('/roles/create', [RolesController::class, 'create']);
$router->get('/roles/{id}', [RolesController::class, 'show']);
$router->get('/roles/{id}/edit', [RolesController::class, 'edit']);
$router->post('/roles', [RolesController::class, 'create']);
$router->post('/roles/update', [RolesController::class, 'update']);
$router->post('/roles/destroy', [RolesController::class, 'destroy']);

$router->get('/permissions', [PermissionsController::class, 'index']);
$router->get('/permissions/create', [PermissionsController::class, 'create']);
$router->post('/permissions', [PermissionsController::class, 'create']);
$router->post('/permissions/assign', [PermissionsController::class, 'assign']);
$router->post('/permissions/revoke', [PermissionsController::class, 'revoke']);

/*
 * Servidor - Configuración y Administración
 */
$router->get('/server', function (): void {
    header('Location: /server/setup');
    exit;
});

$router->get('/server/setup', [ServerSetupController::class, 'index']);

$router->get('/server/setup/step1', [ServerSetupController::class, 'step1']);
$router->get('/server/setup/step2', [ServerSetupController::class, 'step2']);
$router->get('/server/setup/step3', [ServerSetupController::class, 'step3']);
$router->get('/server/setup/step4', [ServerSetupController::class, 'step4']);
$router->get('/server/setup/step5', [ServerSetupController::class, 'step5']);
$router->get('/server/setup/step6', [ServerSetupController::class, 'step6']);
$router->get('/server/setup/step7', [ServerSetupController::class, 'step7']);
$router->get('/server/setup/step8', [ServerSetupController::class, 'step8']);
$router->get('/server/setup/step9', [ServerSetupController::class, 'step9']);

$router->post('/server/setup/process', [ServerSetupController::class, 'process']);

return $router;

