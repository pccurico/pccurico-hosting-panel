<?php

declare(strict_types=1);

$sites = $sites ?? [];
$apacheStatus = $apacheStatus ?? 'unknown';
$apacheVersion = $apacheVersion ?? 'unknown';

$statusClass = $apacheStatus === 'active'
    ? 'status-ok'
    : 'status-warning';
?>

<div class="page-header">
    <div>
        <div class="page-eyebrow">HOSTING</div>
        <h1 class="page-title">Sitios</h1>
        <p class="page-description">
            VirtualHosts administrados por Apache en este servidor.
        </p>
    </div>

    <div class="page-header-actions">
        <a href="/sites/create" class="panel-button panel-button-primary">+ Crear sitio</a>
        <span class="service-badge <?= htmlspecialchars($statusClass, ENT_QUOTES, 'UTF-8') ?>">
            <span class="service-dot"></span>
            Apache <?= htmlspecialchars($apacheStatus, ENT_QUOTES, 'UTF-8') ?>
        </span>
    </div>
</div>

<div class="stats-grid">
    <div class="panel-stat-card">
        <div class="panel-stat-label">Sitios detectados</div>
        <div class="panel-stat-value"><?= count($sites) ?></div>
        <div class="panel-stat-meta">VirtualHosts configurados</div>
    </div>

    <div class="panel-stat-card">
        <div class="panel-stat-label">Servidor web</div>
        <div class="panel-stat-value">Apache</div>
        <div class="panel-stat-meta">
            <?= htmlspecialchars($apacheVersion, ENT_QUOTES, 'UTF-8') ?>
        </div>
    </div>

    <div class="panel-stat-card">
        <div class="panel-stat-label">Modo</div>
        <div class="panel-stat-value">Lectura</div>
        <div class="panel-stat-meta">Sin cambios sobre Apache</div>
    </div>
</div>

<div class="panel-card">
    <div class="panel-card-header">
        <div>
            <h2>VirtualHosts</h2>
            <p>
                Información obtenida directamente desde
                <code>/etc/apache2/sites-enabled</code>.
            </p>
        </div>
    </div>

    <?php if ($sites === []): ?>

        <div class="empty-state">
            <div class="empty-state-title">No se detectaron sitios</div>
            <div class="empty-state-text">
                Apache no tiene VirtualHosts configurados en
                <code>sites-enabled</code>.
            </div>
        </div>

    <?php else: ?>

        <div class="sites-table-wrapper">
            <table class="sites-table">
                <thead>
                    <tr>
                        <th>Sitio</th>
                        <th>Estado</th>
                        <th>Configuración</th>
                        <th>DocumentRoot</th>
                        <th>PHP</th>
                        <th>SSL</th>
                    </tr>
                </thead>

                <tbody>

                <?php foreach ($sites as $site): ?>

                    <?php
                    $serverName = (string) ($site['server_name'] ?? '');
                    $aliases = $site['aliases'] ?? [];
                    $config = (string) ($site['config_file'] ?? '');
                    $documentRoot = (string) ($site['document_root'] ?? '');
                    $phpHandler = (string) ($site['php_handler'] ?? 'No detectado');
                    $exists = (bool) ($site['document_root_exists'] ?? false);
                    $ssl = (bool) ($site['ssl'] ?? false);
                    ?>

                    <tr>
                        <td>
                            <div class="site-name">
                                <?= htmlspecialchars($serverName, ENT_QUOTES, 'UTF-8') ?>
                            </div>

                            <?php if ($aliases !== []): ?>
                                <div class="site-aliases">
                                    <?php foreach ($aliases as $alias): ?>
                                        <span class="site-alias">
                                            <?= htmlspecialchars($alias, ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if ($exists): ?>
                                <span class="site-status site-status-ok">
                                    <span></span> Activo
                                </span>
                            <?php else: ?>
                                <span class="site-status site-status-warning">
                                    <span></span> Revisar
                                </span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <code>
                                <?= htmlspecialchars($config, ENT_QUOTES, 'UTF-8') ?>
                            </code>
                        </td>

                        <td>
                            <code class="document-root">
                                <?= htmlspecialchars(
                                    $documentRoot !== '' ? $documentRoot : 'No definido',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </code>
                        </td>

                        <td>
                            <?= htmlspecialchars($phpHandler, ENT_QUOTES, 'UTF-8') ?>
                        </td>

                        <td>
                            <?php if ($ssl): ?>
                                <span class="ssl-badge ssl-enabled">HTTPS</span>
                            <?php else: ?>
                                <span class="ssl-badge ssl-disabled">No detectado</span>
                            <?php endif; ?>
                        </td>
                    </tr>

                <?php endforeach; ?>

                </tbody>
            </table>
        </div>

    <?php endif; ?>
</div>

<div class="panel-card panel-info-card">
    <div class="panel-card-header">
        <div>
            <h2>Modo seguro</h2>
            <p>
                Esta sección actualmente solo consulta la configuración de Apache.
                No crea, elimina ni modifica VirtualHosts.
            </p>
        </div>
    </div>

    <div class="panel-info-grid">
        <div>
            <span class="panel-info-label">Configuración</span>
            <code>/etc/apache2/sites-enabled/</code>
        </div>

        <div>
            <span class="panel-info-label">Servidor</span>
            <code>apache2</code>
        </div>

        <div>
            <span class="panel-info-label">Documentación futura</span>
            <span>
                Crear sitio, dominios, alias, SSL y PHP-FPM.
            </span>
        </div>
    </div>
</div>
