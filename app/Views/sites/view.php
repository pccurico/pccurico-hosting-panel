<?php

declare(strict_types=1);

$site = $site ?? [];

$serverName = (string)($site['server_name'] ?? '');
$configFile = (string)($site['config_file'] ?? '');
$configPath = (string)($site['config_path'] ?? '');
$documentRoot = (string)($site['document_root'] ?? '');
$endpoint = (string)($site['endpoint'] ?? '');
$phpHandler = (string)($site['php_handler'] ?? 'No detectado');
$aliases = $site['aliases'] ?? [];
$rawConfig = (string)($site['raw_config'] ?? '');

$rootExists = (bool)($site['document_root_exists'] ?? false);
$rootWritable = (bool)($site['document_root_writable'] ?? false);
$ssl = (bool)($site['ssl'] ?? false);
?>

<div class="page-header">
    <div>
        <div class="page-eyebrow">HOSTING / SITIOS</div>

        <h1 class="page-title">
            <?= htmlspecialchars($serverName, ENT_QUOTES, 'UTF-8') ?>
        </h1>

        <p class="page-description">
            Detalle de configuración del VirtualHost.
        </p>
    </div>

    <div class="page-header-actions">
        <a href="/sites" class="panel-button">
            ← Volver a sitios
        </a>
    </div>
</div>

<div class="panel-card">
    <div class="panel-card-header">
        <h2>Información general</h2>
    </div>

    <div class="detail-grid">

        <div class="detail-item">
            <span>Estado</span>
            <strong class="detail-ok">Activo</strong>
        </div>

        <div class="detail-item">
            <span>ServerName</span>
            <strong>
                <?= htmlspecialchars($serverName, ENT_QUOTES, 'UTF-8') ?>
            </strong>
        </div>

        <div class="detail-item">
            <span>VirtualHost</span>
            <strong>
                <?= htmlspecialchars($endpoint, ENT_QUOTES, 'UTF-8') ?>
            </strong>
        </div>

        <div class="detail-item">
            <span>Configuración</span>
            <code>
                <?= htmlspecialchars($configFile, ENT_QUOTES, 'UTF-8') ?>
            </code>
        </div>

        <div class="detail-item detail-wide">
            <span>Ruta configuración</span>
            <code>
                <?= htmlspecialchars($configPath, ENT_QUOTES, 'UTF-8') ?>
            </code>
        </div>

        <div class="detail-item detail-wide">
            <span>DocumentRoot</span>
            <code>
                <?= htmlspecialchars(
                    $documentRoot ?: 'No definido',
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </code>
        </div>

        <div class="detail-item">
            <span>Directorio</span>

            <?php if ($rootExists): ?>
                <strong class="detail-ok">Existe</strong>
            <?php else: ?>
                <strong class="detail-warning">No existe</strong>
            <?php endif; ?>
        </div>

        <div class="detail-item">
            <span>Escritura</span>

            <?php if ($rootWritable): ?>
                <strong class="detail-ok">Permitida</strong>
            <?php else: ?>
                <strong class="detail-warning">No disponible</strong>
            <?php endif; ?>
        </div>

        <div class="detail-item">
            <span>PHP</span>
            <strong>
                <?= htmlspecialchars($phpHandler, ENT_QUOTES, 'UTF-8') ?>
            </strong>
        </div>

        <div class="detail-item">
            <span>SSL</span>

            <?php if ($ssl): ?>
                <strong class="detail-ok">Configurado</strong>
            <?php else: ?>
                <strong>Sin SSL detectado</strong>
            <?php endif; ?>
        </div>

    </div>
</div>

<div class="panel-card">
    <div class="panel-card-header">
        <h2>Dominios y alias</h2>
    </div>

    <div class="domain-list">
        <div class="domain-primary">
            <?= htmlspecialchars($serverName, ENT_QUOTES, 'UTF-8') ?>
        </div>

        <?php foreach ($aliases as $alias): ?>
            <div class="domain-alias">
                <?= htmlspecialchars($alias, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endforeach; ?>

        <?php if ($aliases === []): ?>
            <div class="domain-empty">
                No hay ServerAlias configurados.
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="panel-card">
    <div class="panel-card-header">
        <h2>Configuración Apache</h2>
        <p>
            Configuración real del VirtualHost detectada en el servidor.
        </p>
    </div>

    <pre class="apache-config"><?= htmlspecialchars(
        $rawConfig,
        ENT_QUOTES,
        'UTF-8'
    ) ?></pre>
</div>
