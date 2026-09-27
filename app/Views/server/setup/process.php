<?php

declare(strict_types=1);

$title = $title ?? 'Configuración del Servidor';
$subtitle = $subtitle ?? 'Proceso de Configuración';

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
            <h2>Configuración Completada</h2>
            <p>Tu servidor está configurado y listo</p>
        </div>

        <div class="wizard-progress">
            <div class="progress-bar">
                <div class="progress-fill" style="width: 100%;"></div>
            </div>
            <span class="progress-text">¡Proceso completado!</span>
        </div>

        <?php if ($success ?? false): ?>
            <div class="alert alert-success">
                <h4>✅ Configuración Exitosa</h4>
                <p>El servidor ha sido configurado correctamente. Todos los servicios están listos para usar.</p>
            </div>
        <?php elseif (isset($errors) && !empty($errors)): ?>
            <div class="alert alert-danger">
                <h4>❌ Errores en la Configuración</h4>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li>
                            <?= h($error) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <h4>ℹ️ Proceso de Configuración</h4>
                <p>No se han recibido datos de configuración. Completa todos los pasos del asistente.</p>
            </div>
        <?php endif; ?>

        <div class="wizard-actions">
            <a href="/server" class="btn btn-primary btn-lg">← Volver al Dashboard</a>
        </div>
    </div>
</div>