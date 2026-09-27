<?php

declare(strict_types=1);

$title = $title ?? 'Configuración del Servidor';
$subtitle = $subtitle ?? 'Paso 6 de 9 - SSL Certificates';

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
            <h2>Paso 6: Certificados SSL</h2>
            <p>Configura los certificados SSL/TLS</p>
        </div>

        <div class="wizard-progress">
            <div class="progress-bar">
                <div class="progress-fill" style="width: 66.67%;"></div>
            </div>
            <span class="progress-text">6/9 pasos completados</span>
        </div>

        <form action="/server/setup/process" method="post" class="wizard-form">
            <input type="hidden" name="step" value="6">

            <div class="form-group">
                <label for="ssl_certificate">Ruta del Certificado</label>
                <input type="text" id="ssl_certificate" name="ssl_certificate" class="form-control" 
                       value="<?= h($ssl['certificate'] ?? '/etc/ssl/certs/server.crt') ?>">
            </div>

            <div class="form-group">
                <label for="ssl_key">Ruta de la Clave Privada</label>
                <input type="text" id="ssl_key" name="ssl_key" class="form-control" 
                       value="<?= h($ssl['key'] ?? '/etc/ssl/private/server.key') ?>">
            </div>

            <div class="form-group">
                <label for="ssl_enabled">SSL Habilitado</label>
                <select id="ssl_enabled" name="ssl_enabled" class="form-control">
                    <option value="1" <?= ($ssl['enabled'] ?? false) ? 'selected' : '' ?>>Sí</option>
                    <option value="0" <?= !($ssl['enabled'] ?? false) ? 'selected' : '' ?>>No</option>
                </select>
            </div>

            <div class="wizard-actions">
                <a href="/server/setup?step=5" class="btn btn-secondary">← Anterior</a>
                <button type="submit" class="btn btn-primary btn-lg">Continuar →</button>
            </div>
        </form>
    </div>
</div>