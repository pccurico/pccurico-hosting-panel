<?php

declare(strict_types=1);

$title = $title ?? 'Configuración del Servidor';
$subtitle = $subtitle ?? 'Paso 8 de 9 - Seguridad';

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
            <h2>Paso 8: Seguridad</h2>
            <p>Configura las opciones de seguridad del servidor</p>
        </div>

        <div class="wizard-progress">
            <div class="progress-bar">
                <div class="progress-fill" style="width: 88.89%;"></div>
            </div>
            <span class="progress-text">8/9 pasos completados</span>
        </div>

        <form action="/server/setup/process" method="post" class="wizard-form">
            <input type="hidden" name="step" value="8">

            <div class="form-group">
                <label for="ssh_port">Puerto SSH</label>
                <input type="number" id="ssh_port" name="ssh_port" class="form-control" 
                       value="<?= h($server['ssh_port'] ?? 22) ?>">
            </div>

            <div class="form-group">
                <label for="firewall_enabled">Firewall Habilitado</label>
                <select id="firewall_enabled" name="firewall_enabled" class="form-control">
                    <option value="1" <?= ($server['firewall_enabled'] ?? true) ? 'selected' : '' ?>>Sí</option>
                    <option value="0" <?= !($server['firewall_enabled'] ?? true) ? 'selected' : '' ?>>No</option>
                </select>
            </div>

            <div class="form-group">
                <label for="allow_root_login">Permitir Login Root</label>
                <select id="allow_root_login" name="allow_root_login" class="form-control">
                    <option value="0" <?= !($server['allow_root_login'] ?? false) ? 'selected' : '' ?>>No</option>
                    <option value="1" <?= ($server['allow_root_login'] ?? false) ? 'selected' : '' ?>>Sí</option>
                </select>
            </div>

            <div class="wizard-actions">
                <a href="/server/setup?step=7" class="btn btn-secondary">← Anterior</a>
                <button type="submit" class="btn btn-primary btn-lg">Continuar →</button>
            </div>
        </form>
    </div>
</div>