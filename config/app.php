<?php

declare(strict_types=1);

return [
    'name' => $_ENV['APP_NAME'] ?? 'PCCURICO Hosting Panel',
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOL),
    'url' => $_ENV['APP_URL'] ?? 'http://hosting.pccurico.cl',
    'timezone' => $_ENV['APP_TIMEZONE'] ?? 'America/Santiago',
    'session_name' => $_ENV['SESSION_NAME'] ?? 'pccurico_hosting_session',
];
