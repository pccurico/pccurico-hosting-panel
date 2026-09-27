<?php

declare(strict_types=1);

$title = $title ?? 'Configuración del Servidor';
$subtitle = $subtitle ?? 'Paso 9 de 9 - Herramientas';

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
            <h2>Paso 9: Herramientas y Mantenimiento</h2>
            <p>Configura herramientas de monitoreo y mantenimiento</p>
        </div>

        <div class="wizard-progress">
            <div class="progress-bar">
                <div class="progress-fill" style="width: 100%;"></div>
            </div>
            <span class="progress-text">9/9 pasos completados</span>
        </div>

        <form action="/server/setup/process" method="post" class="wizard-form">
            <input type="hidden" name="step" value="9">

            <div class="form-group">
                <label for="log_retention_días">Retención de Logs (días)</label>
                <input type="number" id="log_retention_días" name="log_retention_días" class="form-control" 
                       value="<?= h($server['log_retention_days'] ?? 30) ?>">
            </div>

            <div class="form-group">
                <label for="backup_enabled">Backups Automatizados</label>
                <select id="backup_enabled" name="backup_enabled" class="form-control">
                    <option value="1" <?= ($server['backup_enabled'] ?? true) ? 'selected' : '' ?>>Sí</option>
                    <option value="0" <?= !($server['backup_enabled'] ?? true) ? 'selected' : '' ?>>No</option>
                </select>
            </div>

            <div class="form-group">
                <label for="cron_enabled">Cron Jobs Habilitados</label>
                <select id="cron_enabled" name="cron_enabled" class="form-control">
                    <option value="1" <?= ($server['cron_enabled'] ?? true) ? 'selected' : '' ?>>Sí</option>
                    <option value="0" <?= !($server['cron_enabled'] ?? true) ? 'selected' : '' ?>>No</option>
                </select>
            </div>

            <div class="form-group">
                <label for="mail_smtp">Servidor SMTP</label>
                <input type="text" id="mail_smtp" name="mail_smtp" class="form-control" 
                       value="<?= h($server['mail_smtp'] ?? 'smtp.gmail.com') ?>">
            </div>

            <div class="wizard-actions">
                <a href="/server/setup?step=8" class="btn btn-secondary">← Anterior</a>
                <button type="submit" class="btn btn-success btn-lg">Finalizar →</button>
            </div>
        </form>
    </div>
</div>