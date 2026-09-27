<?php

declare(strict_types=1);

$title = $title ?? 'Configuración del Servidor';
$subtitle = $subtitle ?? 'Paso 5 de 9 - MariaDB Database';

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
            <h2>Paso 5: Base de Datos MariaDB</h2>
            <p>Configura la base de datos MySQL</p>
        </div>

        <div class="wizard-progress">
            <div class="progress-bar">
                <div class="progress-fill" style="width: 55.56%;"></div>
            </div>
            <span class="progress-text">5/9 pasos completados</span>
        </div>

        <form action="/server/setup/process" method="post" class="wizard-form">
            <input type="hidden" name="step" value="5">

            <div class="form-group">
                <label for="db_host">Host de la BD</label>
                <input type="text" id="db_host" name="db_host" class="form-control" 
                       value="<?= h($maria_db['host'] ?? 'localhost') ?>">
            </div>

            <div class="form-group">
                <label for="db_name">Nombre de la Base de Datos</label>
                <input type="text" id="db_name" name="db_name" class="form-control" 
                       value="<?= h($maria_db['name'] ?? 'myapp') ?>">
            </div>

            <div class="form-group">
                <label for="db_port">Puerto de la BD</label>
                <input type="number" id="db_port" name="db_port" class="form-control" 
                       value="<?= h($maria_db['port'] ?? 3306) ?>">
            </div>

            <div class="form-group">
                <label for="db_user">Usuario de la BD</label>
                <input type="text" id="db_user" name="db_user" class="form-control" 
                       value="<?= h($maria_db['username'] ?? 'admin') ?>">
            </div>

            <div class="form-group">
                <label for="db_password">Contraseña de la BD</label>
                <input type="password" id="db_password" name="db_password" class="form-control" 
                       placeholder="********">
            </div>

            <div class="wizard-actions">
                <a href="/server/setup?step=4" class="btn btn-secondary">← Anterior</a>
                <button type="submit" class="btn btn-primary btn-lg">Continuar →</button>
            </div>
        </form>
    </div>
</div>