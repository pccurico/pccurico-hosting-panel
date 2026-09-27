<?php

declare(strict_types=1);

$title = $title ?? 'Estado del Servidor';
$subtitle = $subtitle ?? 'Panel de Control de Infraestructura';

function h(mixed $value): string
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
}

function getStatusBadge(string $status): string
{
    return match ($status) {
        'running', 'connected', 'valid', 'enabled' => '<span class="badge badge-success">ACTIVO</span>',
        'stopped', 'disconnected', 'invalid', 'disabled' => '<span class="badge badge-danger">DETENIDO</span>',
        'error' => '<span class="badge badge-error">ERROR</span>',
        'not_installed' => '<span class="badge badge-warning">NO INSTALADO</span>',
        'not_configured' => '<span class="badge badge-warning">NO CONFIGURADO</span>',
        default => '<span class="badge badge-secondary">DESCONOCIDO</span>',
    };
}

function getStatusIcon(array $service): string
{
    $running = $service['running'] ?? $service['connected'] ?? $service['valid'] ?? $service['enabled'] ?? false;
    if ($running === true) {
        return '🟢';
    }
    if ($running === false) {
        return '🔴';
    }
    return '⚪';
}
?>

<div class="pcc-page-wrapper">

    <div class="page-header">
        <div>
            <h1>
                <?= h($title) ?>
            </h1>
            <p>
                <?= h($subtitle) ?>
            </p>
        </div>
        <a href="/server/setup" class="btn btn-primary">⚙ Configurar Servidor</a>
    </div>

    <div class="pcc-module-grid">

        <div class="pcc-module-card pcc-module-main">
            <div class="pcc-module-card-header">
                <div>
                    <span class="pcc-card-kicker">INFRAESTRUCTURA</span>
                    <h3>Sistema</h3>
                </div>
            </div>
            
            <div class="pcc-module-card-body">
                <div class="service-info-grid">
                    <div class="service-item">
                        <span class="service-label">Hostname</span>
                        <span class="service-value"><?= h($system['hostname'] ?? 'NO DISPONIBLE') ?></span>
                    </div>
                    <div class="service-item">
                        <span class="service-label">Uptime</span>
                        <span class="service-value"><?= h(gmdate('H:i:s', $system['uptime_seconds'] ?? 0)) ?></span>
                    </div>
                    <div class="service-item">
                        <span class="service-label">Timestamp</span>
                        <span class="service-value"><?= h($system['timestamp'] ?? 'NO DISPONIBLE') ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="pcc-module-card pcc-module-main">
            <div class="pcc-module-card-header">
                <div>
                    <span class="pcc-card-kicker">SERVICIOS</span>
                    <h3>Apache</h3>
                </div>
            </div>
            
            <div class="pcc-module-card-body">
                <div class="service-status-row">
                    <span class="service-icon"><?= getStatusIcon($apache) ?></span>
                    <span class="service-status"><?= getStatusBadge($apache['running'] ? 'running' : 'stopped') ?></span>
                </div>
                <div class="service-info-grid">
                    <div class="service-item">
                        <span class="service-label">Configuración</span>
                        <span class="service-value"><?= h($apache['config_path'] ?? 'NO DISPONIBLE') ?></span>
                    </div>
                    <div class="service-item">
                        <span class="service-label">Estado</span>
                        <span class="service-value"><?= $apache['enabled'] ? 'Habilitado' : 'Deshabilitado' ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="pcc-module-card pcc-module-main">
            <div class="pcc-module-card-header">
                <div>
                    <span class="pcc-card-kicker">SERVICIOS</span>
                    <h3>PHP</h3>
                </div>
            </div>
            
            <div class="pcc-module-card-body">
                <div class="service-status-row">
                    <span class="service-icon"><?= getStatusIcon($php) ?></span>
                    <span class="service-status"><?= getStatusBadge($php['status'] ?? 'not_installed') ?></span>
                </div>
                <div class="service-info-grid">
                    <div class="service-item">
                        <span class="service-label">Motor</span>
                        <span class="service-value"><?= h($php['engine'] ?? 'NO DISPONIBLE') ?></span>
                    </div>
                    <div class="service-item">
                        <span class="service-label">Estado</span>
                        <span class="service-value"><?= h($php['status'] ?? 'NO DISPONIBLE') ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="pcc-module-card pcc-module-main">
            <div class="pcc-module-card-header">
                <div>
                    <span class="pcc-card-kicker">SERVICIOS</span>
                    <h3>MariaDB</h3>
                </div>
            </div>
            
            <div class="pcc-module-card-body">
                <div class="service-status-row">
                    <span class="service-icon"><?= getStatusIcon($maria_db) ?></span>
                    <span class="service-status"><?= getStatusBadge($maria_db['connected'] ? 'connected' : 'disconnected') ?></span>
                </div>
                <div class="service-info-grid">
                    <div class="service-item">
                        <span class="service-label">Host</span>
                        <span class="service-value"><?= h($maria_db['host'] ?? 'NO DISPONIBLE') ?></span>
                    </div>
                    <div class="service-item">
                        <span class="service-label">Base de datos</span>
                        <span class="service-value"><?= h($maria_db['name'] ?? 'NO DISPONIBLE') ?></span>
                    </div>
                    <div class="service-item">
                        <span class="service-label">Puerto</span>
                        <span class="service-value"><?= h($maria_db['port'] ?? 'NO DISPONIBLE') ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="pcc-module-card pcc-module-main">
            <div class="pcc-module-card-header">
                <div>
                    <span class="pcc-card-kicker">SERVICIOS</span>
                    <h3>SSL</h3>
                </div>
            </div>
            
            <div class="pcc-module-card-body">
                <div class="service-status-row">
                    <span class="service-icon"><?= getStatusIcon($ssl) ?></span>
                    <span class="service-status"><?= getStatusBadge($ssl['valid'] ? 'valid' : 'invalid') ?></span>
                </div>
                <div class="service-info-grid">
                    <div class="service-item">
                        <span class="service-label">Certificado</span>
                        <span class="service-value"><?= h($ssl['certificate'] ?? 'NO DISPONIBLE') ?></span>
                    </div>
                    <div class="service-item">
                        <span class="service-label">Clave</span>
                        <span class="service-value"><?= h($ssl['key'] ?? 'NO DISPONIBLE') ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="pcc-module-card pcc-module-main">
            <div class="pcc-module-card-header">
                <div>
                    <span class="pcc-card-kicker">SERVICIOS</span>
                    <h3>DNS</h3>
                </div>
            </div>
            
            <div class="pcc-module-card-body">
                <div class="service-status-row">
                    <span class="service-icon"><?= getStatusIcon($dns) ?></span>
                    <span class="service-status"><?= getStatusBadge($dns['enabled'] ? 'enabled' : 'disabled') ?></span>
                </div>
                <div class="service-info-grid">
                    <div class="service-item">
                        <span class="service-label">Zona</span>
                        <span class="service-value"><?= h($dns['zone'] ?? 'NO DISPONIBLE') ?></span>
                    </div>
                    <div class="service-item">
                        <span class="service-label">Registros</span>
                        <span class="service-value"><?= h($dns['records_count'] ?? 0) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="pcc-module-card pcc-module-main">
            <div class="pcc-module-card-header">
                <div>
                    <span class="pcc-card-kicker">SERVICIOS</span>
                    <h3>Cloudflare</h3>
                </div>
            </div>
            
            <div class="pcc-module-card-body">
                <div class="service-status-row">
                    <span class="service-icon"><?= getStatusIcon($cloudflare) ?></span>
                    <span class="service-status"><?= getStatusBadge($cloudflare['api_key_valid'] ? 'enabled' : 'not_configured') ?></span>
                </div>
                <div class="service-info-grid">
                    <div class="service-item">
                        <span class="service-label">Dominio</span>
                        <span class="service-value"><?= h($cloudflare['domain'] ?? 'NO DISPONIBLE') ?></span>
                    </div>
                    <div class="service-item">
                        <span class="service-label">API Key</span>
                        <span class="service-value"><?= $cloudflare['api_key_valid'] ? 'Configurada' : 'No configurada' ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="pcc-module-card pcc-module-main">
            <div class="pcc-module-card-header">
                <div>
                    <span class="pcc-card-kicker">SERVIDOR</span>
                    <h3>Panel de Hosting</h3>
                </div>
            </div>
            
            <div class="pcc-module-card-body">
                <div class="service-status-row">
                    <span class="service-icon"><?= getStatusIcon($server) ?></span>
                    <span class="service-status"><?= getStatusBadge($server['active'] ? 'enabled' : 'disabled') ?></span>
                </div>
                <div class="service-info-grid">
                    <div class="service-item">
                        <span class="service-label">Hostname</span>
                        <span class="service-value"><?= h($server['hostname'] ?? 'NO DISPONIBLE') ?></span>
                    </div>
                    <div class="service-item">
                        <span class="service-label">Puerto</span>
                        <span class="service-value"><?= h($server['port'] ?? 'NO DISPONIBLE') ?></span>
                    </div>
                    <div class="service-item">
                        <span class="service-label">Última verificación</span>
                        <span class="service-value"><?= h($server['timestamp'] ?? 'NO DISPONIBLE') ?></span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>