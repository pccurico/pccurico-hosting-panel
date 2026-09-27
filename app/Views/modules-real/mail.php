<div class="pcc-page-title">
    <div class="pcc-page-icon">✉</div>
    <div>
        <h2>Correo</h2>
        <p>Estado de servicios de correo detectados.</p>
    </div>
</div>

<div class="pcc-module-grid">

<?php foreach ($data['services'] as $service => $status): ?>

    <div class="pcc-module-card">
        <span class="pcc-card-kicker">SERVICIO</span>

        <h3><?= htmlspecialchars($service) ?></h3>

        <div class="pcc-stat-value">
            <?= htmlspecialchars($status) ?>
        </div>
    </div>

<?php endforeach; ?>

</div>
