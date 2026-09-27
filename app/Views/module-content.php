<?php
declare(strict_types=1);
?>

<div class="pcc-page-title">

    <div class="pcc-page-icon">
        <?= htmlspecialchars($icon ?? '▦') ?>
    </div>

    <div>
        <h2>
            <?= htmlspecialchars($title ?? 'Módulo') ?>
        </h2>

        <p>
            <?= htmlspecialchars($subtitle ?? '') ?>
        </p>
    </div>

</div>

<div class="pcc-module-grid">

    <div class="pcc-module-card pcc-module-main">

        <div class="pcc-module-card-header">

            <div>

                <span class="pcc-card-kicker">
                    MÓDULO
                </span>

                <h3>
                    <?= htmlspecialchars($title ?? '') ?>
                </h3>

            </div>

            <span class="pcc-module-icon">
                <?= htmlspecialchars($icon ?? '') ?>
            </span>

        </div>

        <p class="pcc-module-description">
            <?= htmlspecialchars($subtitle ?? '') ?>
        </p>

        <div class="pcc-module-state">

            <span class="pcc-status-dot"></span>

            Interfaz disponible

        </div>

    </div>

    <div class="pcc-module-card">

        <span class="pcc-card-kicker">
            ESTADO
        </span>

        <div class="pcc-stat-value">
            Preparado
        </div>

        <p>
            Módulo integrado al panel administrativo.
        </p>

    </div>

    <div class="pcc-module-card">

        <span class="pcc-card-kicker">
            SEGURIDAD
        </span>

        <div class="pcc-stat-value">
            Protegido
        </div>

        <p>
            Acceso mediante sesión autenticada.
        </p>

    </div>

</div>

<div class="pcc-panel-section">

    <div class="pcc-section-header">

        <span class="pcc-card-kicker">
            ADMINISTRACIÓN
        </span>

        <h3>
            Operaciones
        </h3>

    </div>

    <div class="pcc-action-grid">

        <button type="button" class="pcc-action-card">

            <span>＋</span>

            <strong>
                Crear
            </strong>

            <small>
                Crear nuevo recurso
            </small>

        </button>

        <button type="button" class="pcc-action-card">

            <span>≡</span>

            <strong>
                Administrar
            </strong>

            <small>
                Gestionar recursos
            </small>

        </button>

        <button type="button" class="pcc-action-card">

            <span>↻</span>

            <strong>
                Actualizar
            </strong>

            <small>
                Consultar estado
            </small>

        </button>

        <button type="button" class="pcc-action-card">

            <span>⚙</span>

            <strong>
                Configuración
            </strong>

            <small>
                Configurar módulo
            </small>

        </button>

    </div>

</div>

<div class="pcc-notice">

    <div class="pcc-notice-icon">
        i
    </div>

    <div>

        <strong>
            Módulo preparado
        </strong>

        <p>
            La interfaz está preparada para conectar las operaciones
            reales del servidor durante la siguiente fase.
        </p>

    </div>

</div>
