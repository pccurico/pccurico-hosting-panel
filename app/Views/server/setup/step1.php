<?php

declare(strict_types=1);

$title = $title ?? 'Configuración del Servidor';
$subtitle = $subtitle ?? 'Paso 1 de 9 - Diagnóstico';

function h(mixed $value): string
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
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
    </div>

    <div class="wizard-container">
        <div class="wizard-header">
            <h2>Paso 1: Diagnóstico</h2>
            <p>Verificamos el estado actual de tu servidor e infraestructura</p>
        </div>

        <div class="wizard-progress">
            <div class="progress-bar">
                <div class="progress-fill" style="width: 11.11%;"></div>
            </div>
            <span class="progress-text">1/9 pasos completados</span>
        </div>

        <form action="/server/setup/process" method="post" class="wizard-form">
            <input type="hidden" name="step" value="1">

            <div class="diagnostic-grid">
                <div class="diagnostic-card">
                    <h3>🖥️ Sistema Operativo</h3>
                    <div class="diagnostic-item">
                        <span class="label">Hostname:</span>
                        <span class="value"><?= h($system['hostname'] ?? 'NO DISPONIBLE') ?></span>
                    </div>
                    <div class="diagnostic-item">
                        <span class="label">Uptime:</span>
                        <span class="value"><?= h(gmdate('H:i:s', $system['uptime_seconds'] ?? 0)) ?></span>
                    </div>
                </div>

                <div class="diagnostic-card">
                    <h3>⚙️ Servicios Críticos</h3>
                    <div class="diagnostic-item">
                        <span class="label">Apache:</span>
                        <span class="value status-<?= $apache['running'] ? 'running' : 'stopped' ?>">
                            <?= $apache['running'] ? 'ACTIVO' : 'DETENIDO' ?>
                        </span>
                    </div>
                    <div class="diagnostic-item">
                        <span class="label">PHP:</span>
                        <span class="value status-<?= $php['status'] === 'running' ? 'running' : 'stopped' ?>">
                            <?= h($php['status'] ?? 'NO DISPONIBLE') ?>
                        </span>
                    </div>
                    <div class="diagnostic-item">
                        <span class="label">MariaDB:</span>
                        <span class="value status-<?= $maria_db['connected'] ? 'connected' : 'disconnected' ?>">
                            <?= $maria_db['connected'] ? 'CONECTADO' : 'DESCONECTADO' ?>
                        </span>
                    </div>
                </div>

                <div class="diagnostic-card">
                    <h3>🔐 Seguridad y Red</h3>
                    <div class="diagnostic-item">
                        <span class="label">SSL:</span>
                        <span class="value status-<?= $ssl['valid'] ? 'valid' : 'invalid' ?>">
                            <?= $ssl['valid'] ? 'VÁLIDO' : 'INVÁLIDO' ?>
                        </span>
                    </div>
                    <div class="diagnostic-item">
                        <span class="label">DNS:</span>
                        <span class="value status-<?= $dns['enabled'] ? 'enabled' : 'disabled' ?>">
                            <?= $dns['enabled'] ? 'CONFIGURADO' : 'NO CONFIGURADO' ?>
                        </span>
                    </div>
                    <div class="diagnostic-item">
                        <span class="label">Cloudflare:</span>
                        <span class="value status-<?= $cloudflare['api_key_valid'] ? 'enabled' : 'not_configured' ?>">
                            <?= $cloudflare['api_key_valid'] ? 'CONFIGURADO' : 'NO CONFIGURADO' ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="wizard-actions">
                <button type="submit" class="btn btn-primary btn-lg">Continuar →</button>
            </div>
        </form>
    </div>
</div>