<?php

declare(strict_types=1);

namespace Pccurico\HostingPanel\Controllers;

abstract class UnifiedViewController
{
    protected function renderPage(
        string $view,
        string $title,
        string $subtitle = '',
        string $active = '',
        array $data = []
    ): void {

        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $viewFile =
            dirname(__DIR__) .
            '/Views/' .
            $view .
            '.php';

        if (!is_file($viewFile)) {
            http_response_code(500);
            exit('Vista no encontrada.');
        }

        extract($data, EXTR_SKIP);

        ob_start();

        require $viewFile;

        $content = ob_get_clean();

        require dirname(__DIR__) . '/Views/layouts/app.php';
    }
}
