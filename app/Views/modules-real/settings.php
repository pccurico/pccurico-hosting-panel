<div class="pcc-page-title">
    <div class="pcc-page-icon">⚙</div>
    <div>
        <h2>Configuración</h2>
        <p>Información actual del entorno del servidor.</p>
    </div>
</div>

<div class="pcc-module-grid">

<?php foreach ($data as $key => $value): ?>

    <div class="pcc-module-card">

        <span class="pcc-card-kicker">
            <?= htmlspecialchars($key) ?>
        </span>

        <div class="pcc-stat-value">
            <?= htmlspecialchars((string)$value) ?>
        </div>

    </div>

<?php endforeach; ?>

</div>
