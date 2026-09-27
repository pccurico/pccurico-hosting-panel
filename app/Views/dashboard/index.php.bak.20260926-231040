<?php

declare(strict_types=1);

$server = $server ?? [];
$resources = $resources ?? [];
$services = $services ?? [];
$storage = $storage ?? [];

function h(mixed $value): string
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
}

$memoryPercent = (float)($resources['memory_percent'] ?? 0);
$diskPercent   = (float)($storage['percent'] ?? 0);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>PCCURICO Hosting Panel</title>

    <link
        rel="stylesheet"
        href="/assets/css/panel.css"
    >
</head>

<body class="panel-page">

<header class="topbar">

    <div class="topbar-brand">
        <div class="brand-mark">P</div>

        <div class="brand-text">
            <strong>PCCURICO</strong>
            <span>Hosting Panel</span>
        </div>
    </div>

    <div class="topbar-server">
        <span class="status-dot"></span>
        <?= h($server['hostname'] ?? 'ia-server') ?>
    </div>

    <a
        class="logout-link"
        href="/logout"
    >
        Cerrar sesión
    </a>

</header>

<div class="panel-shell">

    <aside class="sidebar">

        <div class="sidebar-title">
            Administración
        </div>

        <nav>

            <a
                class="nav-item active"
                href="/dashboard"
            >
                <span>▦</span>
                Dashboard
            </a>

            <a
                class="nav-item"
                href="#"
            >
                <span>◈</span>
                Hosting
            </a>

            <a
                class="nav-item"
                href="#"
            >
                <span>◇</span>
                Sitios
            </a>

            <a
                class="nav-item"
                href="#"
            >
                <span>◎</span>
                Dominios
            </a>

            <a
                class="nav-item"
                href="#"
            >
                <span>▤</span>
                Bases de datos
            </a>

            <a
                class="nav-item"
                href="#"
            >
                <span>⌘</span>
                Archivos
            </a>

            <a
                class="nav-item"
                href="#"
            >
                <span>◇</span>
                SSL
            </a>

            <a
                class="nav-item"
                href="#"
            >
                <span>◷</span>
                Cron
            </a>

            <a
                class="nav-item"
                href="#"
            >
                <span>▣</span>
                Backups
            </a>

            <div class="sidebar-divider"></div>

            <a
                class="nav-item"
                href="#"
            >
                <span>◉</span>
                Usuarios
            </a>

            <a
                class="nav-item"
                href="#"
            >
                <span>⚙</span>
                Configuración
            </a>

        </nav>

    </aside>

    <main class="dashboard">

        <div class="page-heading">

            <div>
                <div class="eyebrow">
                    INFRAESTRUCTURA
                </div>

                <h1>
                    Dashboard
                </h1>

                <p>
                    Estado general del servidor PCCURICO.
                </p>
            </div>

            <div class="server-badge">
                <span class="status-dot"></span>
                Servidor operativo
            </div>

        </div>

        <section class="metrics-grid">

            <article class="metric-card">

                <div class="metric-label">
                    Memoria RAM
                </div>

                <div class="metric-value">
                    <?= h($resources['memory_used'] ?? 'N/D') ?>
                </div>

                <div class="metric-detail">
                    de <?= h($resources['memory_total'] ?? 'N/D') ?>
                </div>

                <div class="progress">
                    <div
                        class="progress-bar"
                        style="width: <?= min(100, max(0, $memoryPercent)) ?>%"
                    ></div>
                </div>

                <div class="metric-foot">
                    <?= h($memoryPercent) ?>% utilizada
                </div>

            </article>

            <article class="metric-card">

                <div class="metric-label">
                    Almacenamiento
                </div>

                <div class="metric-value">
                    <?= h($storage['used'] ?? 'N/D') ?>
                </div>

                <div class="metric-detail">
                    de <?= h($storage['total'] ?? 'N/D') ?>
                </div>

                <div class="progress">
                    <div
                        class="progress-bar"
                        style="width: <?= min(100, max(0, $diskPercent)) ?>%"
                    ></div>
                </div>

                <div class="metric-foot">
                    <?= h($diskPercent) ?>% utilizado
                </div>

            </article>

            <article class="metric-card">

                <div class="metric-label">
                    CPU
                </div>

                <div class="metric-value">
                    <?= h($resources['load_1'] ?? '0') ?>
                </div>

                <div class="metric-detail">
                    Load Average
                </div>

                <div class="metric-foot">
                    <?= h($resources['cpu_cores'] ?? '1') ?> núcleos disponibles
                </div>

            </article>

            <article class="metric-card">

                <div class="metric-label">
                    Uptime
                </div>

                <div class="metric-value">
                    <?= h($server['uptime'] ?? 'N/D') ?>
                </div>

                <div class="metric-detail">
                    Tiempo activo
                </div>

                <div class="metric-foot">
                    <?= h($server['hostname'] ?? 'ia-server') ?>
                </div>

            </article>

        </section>

        <section class="dashboard-grid">

            <article class="panel-card services-card">

                <div class="card-header">

                    <div>
                        <div class="card-title">
                            Servicios
                        </div>

                        <div class="card-subtitle">
                            Estado de los servicios principales
                        </div>
                    </div>

                    <span class="live-badge">
                        EN VIVO
                    </span>

                </div>

                <div class="service-list">

                    <?php foreach ($services as $service): ?>

                        <div class="service-row">

                            <div class="service-info">

                                <span
                                    class="service-dot <?= $service['active'] ? 'online' : 'offline' ?>"
                                ></span>

                                <div>
                                    <strong>
                                        <?= h($service['label']) ?>
                                    </strong>

                                    <small>
                                        <?= h($service['key']) ?>
                                    </small>
                                </div>

                            </div>

                            <span
                                class="service-status <?= $service['active'] ? 'online-text' : 'offline-text' ?>"
                            >
                                <?= h($service['status']) ?>
                            </span>

                        </div>

                    <?php endforeach; ?>

                </div>

            </article>

            <article class="panel-card">

                <div class="card-header">

                    <div>
                        <div class="card-title">
                            Servidor
                        </div>

                        <div class="card-subtitle">
                            Información del sistema
                        </div>
                    </div>

                </div>

                <div class="info-list">

                    <div class="info-row">
                        <span>Hostname</span>
                        <strong><?= h($server['hostname'] ?? 'N/D') ?></strong>
                    </div>

                    <div class="info-row">
                        <span>Sistema</span>
                        <strong><?= h($server['os'] ?? 'N/D') ?></strong>
                    </div>

                    <div class="info-row">
                        <span>Kernel</span>
                        <strong><?= h($server['kernel'] ?? 'N/D') ?></strong>
                    </div>

                    <div class="info-row">
                        <span>Arquitectura</span>
                        <strong><?= h($server['architecture'] ?? 'N/D') ?></strong>
                    </div>

                    <div class="info-row">
                        <span>PHP</span>
                        <strong><?= h(PHP_VERSION) ?></strong>
                    </div>

                </div>

            </article>

        </section>

        <section class="panel-card load-card">

            <div class="card-header">

                <div>
                    <div class="card-title">
                        Carga del sistema
                    </div>

                    <div class="card-subtitle">
                        Load Average de Linux
                    </div>
                </div>

            </div>

            <div class="load-grid">

                <div>
                    <span>1 minuto</span>
                    <strong><?= h($resources['load_1'] ?? '0') ?></strong>
                </div>

                <div>
                    <span>5 minutos</span>
                    <strong><?= h($resources['load_5'] ?? '0') ?></strong>
                </div>

                <div>
                    <span>15 minutos</span>
                    <strong><?= h($resources['load_15'] ?? '0') ?></strong>
                </div>

            </div>

        </section>

    </main>

</div>

</body>
</html>
