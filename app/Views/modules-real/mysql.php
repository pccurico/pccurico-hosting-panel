<div class="pcc-page-title">
    <div class="pcc-page-icon">🗄</div>
    <div>
        <h2>MySQL</h2>
        <p>Estado del servidor MySQL.</p>
    </div>
</div>

<div class="pcc-module-grid">

    <div class="pcc-module-card">
        <span class="pcc-card-kicker">ESTADO</span>
        <div class="pcc-stat-value"><?= htmlspecialchars($data['status']) ?></div>
    </div>

    <div class="pcc-module-card">
        <span class="pcc-card-kicker">VERSIÓN</span>
        <div class="pcc-stat-value"><?= htmlspecialchars($data['version']) ?></div>
    </div>

    <div class="pcc-module-card">
        <span class="pcc-card-kicker">PUERTO</span>
        <div class="pcc-stat-value">3306</div>
    </div>

</div>
