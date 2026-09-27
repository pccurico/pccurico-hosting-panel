<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use Pccurico\HostingPanel\Core\Bootstrap;

$basePath = dirname(__DIR__);

Bootstrap::init($basePath);

$router = require $basePath . '/routes/web.php';

echo $router->dispatch(
    $_SERVER['REQUEST_METHOD'] ?? 'GET',
    $_SERVER['REQUEST_URI'] ?? '/'
);
