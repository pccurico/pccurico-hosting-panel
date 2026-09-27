<div class="pcc-page-title">
    <div class="pcc-page-icon">↻</div>
    <div>
        <h2>Backups</h2>
        <p>Estado de ubicaciones disponibles para respaldos.</p>
    </div>
</div>

<div class="pcc-module-grid">

<?php foreach ($data as $backup): ?>

    <div class="pcc-module-card">

        <span class="pcc-card-kicker">
            UBICACIÓN
        </span>

        <h3>
            <?= htmlspecialchars($backup['path']) ?>
        </h3>

        <div class="pcc-stat-value">
            <?= htmlspecialchars($backup['size']) ?>
        </div>

    </div>

<?php endforeach; ?>

</div>
