<?php

declare(strict_types=1);

namespace Pccurico\HostingPanel\Controllers;

use Pccurico\HostingPanel\Middleware\Csrf;
use Pccurico\HostingPanel\Services\ApacheService;

final class SitesController
{
    public function index(): void
    {
        $apache = new ApacheService();

        $sites = $apache->getSites();

        $this->render('sites/index', [
            'sites' => $sites,
        ]);
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->createSite();
            return;
        }

        $this->render('sites/create', [
            'csrf' => $this->csrfToken(),
            'error' => null,
            'success' => null,
            'preview' => null,
        ]);
    }

    private function createSite(): void
    {
        /*
         * CSRF
         */
        $csrf = (string)($_POST['_csrf'] ?? '');

        if (!$this->validateCsrf($csrf)) {
            $this->render('sites/create', [
                'csrf' => $this->csrfToken(),
                'error' => 'Token de seguridad inválido. Recarga la página.',
                'success' => null,
                'preview' => null,
            ]);
            return;
        }

        $serverName = trim((string)($_POST['server_name'] ?? ''));
        $documentRoot = trim((string)($_POST['document_root'] ?? ''));
        $aliases = trim((string)($_POST['aliases'] ?? ''));
        $phpVersion = trim((string)($_POST['php_version'] ?? '8.3'));

        /*
         * Dominio
         */
        if (!$this->validDomain($serverName)) {
            $this->renderCreateError(
                'El nombre de dominio no es válido.'
            );
            return;
        }

        /*
         * DocumentRoot
         */
        if (
            $documentRoot === '' ||
            !str_starts_with($documentRoot, '/var/www/') &&
            $documentRoot !== '/var/www'
        ) {
            $this->renderCreateError(
                'El DocumentRoot debe estar dentro de /var/www/.'
            );
            return;
        }

        if (str_contains($documentRoot, '..')) {
            $this->renderCreateError(
                'El DocumentRoot contiene una ruta no permitida.'
            );
            return;
        }

        /*
         * PHP
         */
        if (!in_array($phpVersion, ['8.2', '8.3'], true)) {
            $this->renderCreateError(
                'La versión PHP seleccionada no está permitida.'
            );
            return;
        }

        /*
         * Alias
         */
        $aliasList = [];

        if ($aliases !== '') {
            foreach (preg_split('/[\s,]+/', $aliases) ?: [] as $alias) {
                $alias = trim($alias);

                if ($alias === '') {
                    continue;
                }

                if (!$this->validDomain($alias)) {
                    $this->renderCreateError(
                        "Alias inválido: {$alias}"
                    );
                    return;
                }

                $aliasList[] = $alias;
            }
        }

        /*
         * No permitir conflicto directo con el panel.
         */
        if (
            $serverName === 'hosting.local' ||
            $serverName === 'hosting.pccurico.cl'
        ) {
            $this->renderCreateError(
                'Ese dominio está reservado para PCCURICO Hosting Panel.'
            );
            return;
        }

        /*
         * Ejecutar helper root restringido.
         */
        $helper = '/usr/local/sbin/pccurico-create-vhost';

        $command = sprintf(
            'sudo %s %s %s %s %s 2>&1',
            escapeshellarg($helper),
            escapeshellarg($serverName),
            escapeshellarg($documentRoot),
            escapeshellarg(implode(' ', $aliasList)),
            escapeshellarg($phpVersion)
        );

        exec(
            $command,
            $output,
            $exitCode
        );

        if ($exitCode !== 0) {
            $message = implode("\n", $output);

            $this->renderCreateError(
                "No fue posible crear el sitio.\n\n{$message}"
            );
            return;
        }

        /*
         * Limpiar token para evitar reutilización.
         */
        $this->rotateCsrf();

        $this->render('sites/create', [
            'csrf' => $this->csrfToken(),
            'error' => null,
            'success' => "Sitio {$serverName} creado correctamente.",
            'preview' => implode("\n", $output),
        ]);
    }

    private function renderCreateError(string $message): void
    {
        $this->render('sites/create', [
            'csrf' => $this->csrfToken(),
            'error' => $message,
            'success' => null,
            'preview' => null,
        ]);
    }

    public function view(): void
    {
        $config = trim((string)($_GET['config'] ?? ''));
        $server = trim((string)($_GET['server'] ?? ''));

        $apache = new ApacheService();

        $site = $apache->findSite($config, $server);

        if ($site === null) {
            $this->render('sites/not-found', [
                'config' => $config,
                'server' => $server,
            ]);
            return;
        }

        $this->render('sites/view', [
            'site' => $site,
        ]);
    }

    private function validDomain(string $domain): bool
    {
        if ($domain === '' || strlen($domain) > 253) {
            return false;
        }

        return preg_match(
            '/^(?=.{1,253}$)(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?\.)*[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?$/',
            $domain
        ) === 1;
    }

    private function csrfToken(): string
    {
        return Csrf::token();
    }

    private function validateCsrf(string $token): bool
    {
        return Csrf::validate($token);
    }

    private function rotateCsrf(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }

    private function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);

        $viewFile = dirname(__DIR__) . '/Views/' . $view . '.php';
        $layoutFile = dirname(__DIR__) . '/Views/layouts/app.php';

        if (!is_file($viewFile)) {
            http_response_code(500);
            echo 'Vista no encontrada.';
            return;
        }

        if (!is_file($layoutFile)) {
            http_response_code(500);
            echo 'Layout no encontrado.';
            return;
        }

        ob_start();

        require $viewFile;

        $content = ob_get_clean();

        $title = $title ?? 'PCCURICO Hosting Panel';

        require $layoutFile;
    }
}
