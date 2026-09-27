<?php

declare(strict_types=1);

$title = $title ?? 'Archivos';
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
                        SISTEMA
                    </span>
                    <h3>Uso del disco</h3>
                </div>

                <span class="pcc-module-icon">
                    <?= htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') ?>
                </span>

            </div>

            <div class="pcc-module-card-body">

                <div class="pcc-disk-usage">
                    <div>
                        <span class="pcc-info-label">Sistema de archivos</span>
                        <code>
                            <?= htmlspecialchars(
                                $data['disk_usage']['filesystem'] ?? 'N/A',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </code>
                    </div>
                    <div>
                        <span class="pcc-info-label">Tamaño total</span>
                        <code>
                            <?= htmlspecialchars(
                                $data['disk_usage']['size'] ?? 'N/A',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </code>
                    </div>
                    <div>
                        <span class="pcc-info-label">Usado</span>
                        <code>
                            <?= htmlspecialchars(
                                $data['disk_usage']['used'] ?? 'N/A',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </code>
                    </div>
                    <div>
                        <span class="pcc-info-label">Disponible</span>
                        <code>
                            <?= htmlspecialchars(
                                $data['disk_usage']['available'] ?? 'N/A',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </code>
                    </div>
                </div>

            </div>

        </div>

        <div class="pcc-module-card">

            <div class="pcc-module-card-header">

                <div>
                    <span class="pcc-card-kicker">
                        DIRECTORIOS
                    </span>
                    <h3>Exploración del sistema de archivos</h3>
                </div>

            </div>

            <div class="pcc-module-card-body">

                <div class="pcc-directories-grid">

                    <?php foreach ($data['directories'] as $dir): ?>

                        <div class="pcc-directory-card">
                            <div class="pcc-directory-header">
                                <h4>
                                    <?= htmlspecialchars(
                                        $dir['name'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </h4>
                                <code class="pcc-directory-path">
                                    <?= htmlspecialchars($dir['path'], ENT_QUOTES, 'UTF-8') ?>
                                </code>
                            </div>

                            <div class="pcc-directory-stats">
                                <div>
                                    <span class="pcc-stat-label">Archivos</span>
                                    <span class="pcc-stat-value">
                                        <?= (int) $dir['count'] > 0 ? (int) $dir['count'] : '0' ?>
                                    </span>
                                </div>
                                <div>
                                    <span class="pcc-stat-label">Escritura</span>
                                    <span class="pcc-stat-value">
                                        <?= $dir['writable'] ? 'Sí' : 'No' ?>
                                    </span>
                                </div>
                                <div>
                                    <span class="pcc-stat-label">Lectura</span>
                                    <span class="pcc-stat-value">
                                        <?= $dir['readable'] ? 'Sí' : 'No' ?>
                                    </span>
                                </div>
                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            </div>

        </div>

    </div>

</div>
