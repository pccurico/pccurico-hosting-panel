<?php

declare(strict_types=1);

$title = $title ?? 'Tareas Cron';
?>

<div class="pcc-page-wrapper">

    <div class="page-header">
        <div>
            <h1>
                <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <p>
                <?= htmlspecialchars(
                    $subtitle ?? '',
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>
        </div>
    </div>

    <div class="pcc-module-grid">

        <div class="pcc-module-card pcc-module-main">

            <div class="pcc-module-card-header">

                <div>
                    <span class="pcc-card-kicker">
                        SERVICIO
                    </span>
                    <h3>Estado de Cron</h3>
                </div>

                <span class="pcc-module-icon">
                    <?= htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') ?>
                </span>

            </div>

            <p class="pcc-module-description">
                Estado del servicio de tareas programadas.
            </p>

            <div class="pcc-info-grid">
                <div>
                    <span class="pcc-info-label">Servicio</span>
                    <code>cron</code>
                </div>
                <div>
                    <span class="pcc-info-label">Estado</span>
                    <code>
                        <?= htmlspecialchars(
                            $data['service_status'] ?? 'desconocido',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </code>
                </div>
            </div>

        </div>

        <div class="pcc-module-card">

            <div class="pcc-module-card-header">

                <div>
                    <span class="pcc-card-kicker">
                        TAREAS PROGRAMADAS
                    </span>
                    <h3>Trabajos activos</h3>
                </div>

            </div>

            <div class="pcc-module-card-body">

                <?php if (empty($data['jobs'])): ?>

                    <p class="pcc-empty-state">
                        No se encontraron tareas programadas.
                    </p>

                <?php else: ?>

                    <div class="pcc-jobs-list">

                        <?php foreach ($data['jobs'] as $job): ?>

                            <div class="pcc-job-item">

                                <div class="pcc-job-header">

                                    <span class="pcc-job-type">
                                        <?= htmlspecialchars(
                                            $job['type'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </span>

                                    <code class="pcc-job-name">
                                        <?= htmlspecialchars(
                                            $job['name'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </code>

                                </div>

                                <pre class="pcc-job-content">
                                    <?= htmlspecialchars(
                                        $job['content'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </pre>

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </div>

</div>
