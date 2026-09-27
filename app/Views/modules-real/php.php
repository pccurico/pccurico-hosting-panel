<div class="pcc-page-title">
    <div class="pcc-page-icon">🐘</div>
    <div>
        <h2>PHP</h2>
        <p>Estado de PHP CLI y PHP-FPM.</p>
    </div>
</div>

<div class="pcc-module-grid">

    <div class="pcc-module-card">
        <span class="pcc-card-kicker">PHP CLI</span>
        <div class="pcc-stat-value"><?= htmlspecialchars($data['cli']) ?></div>
    </div>

    <div class="pcc-module-card">
        <span class="pcc-card-kicker">PHP-FPM</span>
        <div class="pcc-stat-value"><?= htmlspecialchars($data['fpm']) ?></div>
    </div>

    <div class="pcc-module-card">
        <span class="pcc-card-kicker">PHP.INI</span>
        <div class="pcc-stat-value"><?= htmlspecialchars($data['ini']) ?></div>
    </div>

</div>

<div class="pcc-panel-section">
    <span class="pcc-card-kicker">EXTENSIONES</span>
    <pre class="pcc-code-box"><?= htmlspecialchars($data['modules']) ?></pre>
</div>
