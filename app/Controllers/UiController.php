<?php

declare(strict_types=1);

namespace Pccurico\HostingPanel\Controllers;

final class UiController
{
    public static function render(
        string $view,
        array $data = [],
        string $title = 'PCCURICO Hosting Panel',
        string $subtitle = ''
    ): void {
        $file = dirname(__DIR__) . '/Views/' . $view . '.php';

        if (!is_file($file)) {
            http_response_code(500);
            exit('Vista no encontrada.');
        }

        extract($data, EXTR_SKIP);

        ob_start();

        require $file;

        $content = ob_get_clean();

        require dirname(__DIR__) . '/Views/layouts/app.php';
    }
}
