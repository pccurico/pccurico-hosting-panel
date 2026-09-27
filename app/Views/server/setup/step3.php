<?php

declare(strict_types=1);

$title = $title ?? 'Configuración del Servidor';
$subtitle = $subtitle ?? 'Paso 3 de 9 - Apache Web Server';

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
            <h2>Paso 3: Apache Web Server</h2>
            <p>Configura el servidor web Apache</p>
        </div>

        <div class="wizard-progress">
            <div class="progress-bar">
                <div class="progress-fill" style="width: 33.33%;"></div>
            </div>
            <span class="progress-text">3/9 pasos completados</span>
        </div>

        <form action="/server/setup/process" method="post" class="wizard-form">
            <input type="hidden" name="step" value="3">

            <div class="form-group">
                <label for="apache_port">Puerto de Escucha</label>
                <input type="number" id="apache_port" name="apache_port" class="form-control" 
                       value="<?= h($apache['port'] ?? 80) ?>" min="1" max="65535" required>
            </div>

            <div class="form-group">
                <label for="apache_server_admin">Administrador del Servidor</label>
                <input type="email" id="apache_server_admin" name="apache_server_admin" class="form-control" 
                       value="<?= h($apache['server_admin'] ?? 'admin@localhost') ?>">
            </div>

            <div class="form-group">
                <label for="apache_enabled">Estado</label>
                <select id="apache_enabled" name="apache_enabled" class="form-control">
                    <option value="1" <?= ($apache['enabled'] ?? true) ? 'selected' : '' ?>>Habilitado</option>
                    <option value="0" <?= !($apache['enabled'] ?? true) ? 'selected' : '' ?>>Deshabilitado</option>
                </select>
            </div>

            <div class="wizard-actions">
                <a href="/server/setup?step=2" class="btn btn-secondary">← Anterior</a>
                <button type="submit" class="btn btn-primary btn-lg">Continuar →</button>
            </div>
        </form>
    </div>
</div>