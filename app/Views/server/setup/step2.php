<?php

declare(strict_types=1);

$title = $title ?? 'Configuración del Servidor';
$subtitle = $subtitle ?? 'Paso 2 de 9 - Sistema';

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
            <h2>Paso 2: Configuración del Sistema</h2>
            <p>Configura los parámetros básicos del sistema operativo</p>
        </div>

        <div class="wizard-progress">
            <div class="progress-bar">
                <div class="progress-fill" style="width: 22.22%;"></div>
            </div>
            <span class="progress-text">2/9 pasos completados</span>
        </div>

        <form action="/server/setup/process" method="post" class="wizard-form">
            <input type="hidden" name="step" value="2">

            <div class="form-group">
                <label for="hostname">Hostname del Servidor</label>
                <input type="text" id="hostname" name="hostname" class="form-control" 
                       value="<?= h($system['hostname'] ?? '') ?>" required>
            </div>

            <div class="wizard-actions">
                <a href="/server/setup?step=1" class="btn btn-secondary">← Anterior</a>
                <button type="submit" class="btn btn-primary btn-lg">Continuar →</button>
            </div>
        </form>
    </div>
</div>