<div class="pcc-page-title">
    <div class="pcc-page-icon">🔗</div>
    <div>
        <h2>DNS</h2>
        <p>Estado de resolución DNS del servidor.</p>
    </div>
</div>

<div class="pcc-module-grid">
    <?php foreach ($data['services'] as $service): ?>
        <div class="pcc-module-card">
            <span class="pcc-card-kicker">SERVICIO</span>
            <h3><?= htmlspecialchars($service['name']) ?></h3>
            <div class="pcc-stat-value"><?= htmlspecialchars($service['status']) ?></div>
        </div>
    <?php endforeach; ?>
</div>

<div class="pcc-panel-section">
    <span class="pcc-card-kicker">CONFIGURACIÓN</span>
    <h3>dnsmasq</h3>

    <div class="pcc-code-box">
        <?php foreach ($data['configs'] as $config): ?>
            <?= htmlspecialchars($config) ?><br>
        <?php endforeach; ?>
    </div>
</div>
