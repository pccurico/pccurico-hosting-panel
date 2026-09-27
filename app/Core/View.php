<?php

declare(strict_types=1);

namespace Pccurico\HostingPanel\Core;

use RuntimeException;

final class View
{
    public static function render(string $view, array $data = []): void
    {
        $file = dirname(__DIR__) . '/Views/' . $view . '.php';

        if (!is_file($file)) {
            throw new RuntimeException("Vista no encontrada: {$view}");
        }

        extract($data, EXTR_SKIP);

        require $file;
    }
}
