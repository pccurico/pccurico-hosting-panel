<?php

declare(strict_types=1);

$title = $title ?? 'Configuración del Servidor';
$subtitle = $subtitle ?? 'Paso 4 de 9 - PHP Engine';

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
            <h2>Paso 4: PHP Engine</h2>
            <p>Configura el motor PHP del servidor</p>
        </div>

        <div class="wizard-progress">
            <div class="progress-bar">
                <div class="progress-fill" style="width: 44.44%;"></div>
            </div>
            <span class="progress-text">4/9 pasos completados</span>
        </div>

        <form action="/server/setup/process" method="post" class="wizard-form">
            <input type="hidden" name="step" value="4">

            <div class="form-group">
                <label for="php_version">Versión de PHP</label>
                <select id="php_version" name="php_version" class="form-control">
                    <option value="8.0">8.0</option>
                    <option value="8.1" selected>8.1</option>
                    <option value="8.2">8.2</option>
                    <option value="8.3">8.3</option>
                </select>
            </div>

            <div class="form-group">
                <label for="php_cli">Línea de Comandos PHP</label>
                <input type="text" id="php_cli" name="php_cli" class="form-control" 
                       value="<?= h($php['cli'] ?? '/usr/bin/php') ?>">
            </div>

            <div class="wizard-actions">
                <a href="/server/setup?step=3" class="btn btn-secondary">← Anterior</a>
                <button type="submit" class="btn btn-primary btn-lg">Continuar →</button>
            </div>
        </form>
    </div>
</div>