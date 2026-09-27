<?php

declare(strict_types=1);

$title = $title ?? 'Configuración del Servidor';
$subtitle = $subtitle ?? 'Paso 7 de 9 - DNS y Cloudflare';

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
            <h2>Paso 7: DNS y Cloudflare</h2>
            <p>Configura la zona DNS y Cloudflare</p>
        </div>

        <div class="wizard-progress">
            <div class="progress-bar">
                <div class="progress-fill" style="width: 77.78%;"></div>
            </div>
            <span class="progress-text">7/9 pasos completados</span>
        </div>

        <form action="/server/setup/process" method="post" class="wizard-form">
            <input type="hidden" name="step" value="7">

            <div class="form-group">
                <label for="dns_zone">Nombre de Zona DNS</label>
                <input type="text" id="dns_zone" name="dns_zone" class="form-control" 
                       value="<?= h($dns['zone'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="dns_records">Cantidad de Registros</label>
                <input type="number" id="dns_records" name="dns_records" class="form-control" 
                       value="<?= h($dns['records_count'] ?? 0) ?>">
            </div>

            <div class="form-group">
                <label for="cloudflare_domain">Dominio Cloudflare</label>
                <input type="text" id="cloudflare_domain" name="cloudflare_domain" class="form-control" 
                       value="<?= h($cloudflare['domain'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="cloudflare_api_key">API Key Cloudflare</label>
                <input type="password" id="cloudflare_api_key" name="cloudflare_api_key" class="form-control" 
                       placeholder="API Key de Cloudflare">
            </div>

            <div class="form-group">
                <label for="cloudflare_zone_id">ID de Zona Cloudflare</label>
                <input type="text" id="cloudflare_zone_id" name="cloudflare_zone_id" class="form-control" 
                       value="<?= h($cloudflare['zone_id'] ?? '') ?>">
            </div>

            <div class="wizard-actions">
                <a href="/server/setup?step=6" class="btn btn-secondary">← Anterior</a>
                <button type="submit" class="btn btn-primary btn-lg">Continuar →</button>
            </div>
        </form>
    </div>
</div>