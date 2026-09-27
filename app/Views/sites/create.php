<?php

declare(strict_types=1);
?>

<div class="page-header">
    <div>
        <div class="page-eyebrow">HOSTING / SITIOS</div>
        <h1 class="page-title">Crear sitio</h1>
        <p class="page-description">
            Crear un nuevo VirtualHost Apache.
        </p>
    </div>

    <div class="page-header-actions">
        <a href="/sites" class="panel-button">← Volver</a>
    </div>
</div>

<div class="panel-card">
    <div class="panel-card-header">
        <h2>Configuración del sitio</h2>
        <p>Los campos serán validados antes de modificar Apache.</p>
    </div>

    <form method="post" action="/sites/create" class="site-form">

        <input
            type="hidden"
            name="_csrf"
            value="<?= htmlspecialchars(
                $_SESSION['_csrf'] ?? '',
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >

        <div class="form-grid">

            <div class="form-group">
                <label for="server_name">Dominio principal</label>
                <input
                    id="server_name"
                    name="server_name"
                    type="text"
                    required
                    placeholder="ejemplo.cl"
                    autocomplete="off"
                >
                <small>
                    Ejemplo: sitio.pccurico.cl
                </small>
            </div>

            <div class="form-group">
                <label for="document_root">DocumentRoot</label>
                <input
                    id="document_root"
                    name="document_root"
                    type="text"
                    required
                    value="/var/www/"
                    autocomplete="off"
                >
                <small>
                    Debe estar dentro de /var/www/
                </small>
            </div>

            <div class="form-group form-group-wide">
                <label for="aliases">Alias</label>
                <input
                    id="aliases"
                    name="aliases"
                    type="text"
                    placeholder="www.ejemplo.cl ejemplo.local"
                    autocomplete="off"
                >
                <small>
                    Separados por espacios. Campo opcional.
                </small>
            </div>

            <div class="form-group">
                <label for="php">PHP</label>

                <select id="php_version" name="php_version">
                    <option value="8.3" selected>PHP-FPM 8.3</option>
                </select>
            </div>

            <div class="form-group">
                <label for="ssl">SSL</label>

                <select id="ssl" name="ssl">
                    <option value="none">Sin SSL</option>
                </select>
            </div>

        </div>

        <div class="form-actions">
            <a href="/sites" class="panel-button">
                Cancelar
            </a>

            <button type="submit" class="panel-button panel-button-primary">
                Crear sitio
            </button>
        </div>

    </form>
</div>
