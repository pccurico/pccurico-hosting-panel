<div class="pcc-page-title">
    <div class="pcc-page-icon">🛠</div>
    <div>
        <h2>Herramientas</h2>
        <p>Diagnóstico del servidor.</p>
    </div>
</div>

<div class="pcc-module-grid">

    <div class="pcc-module-card">
        <span class="pcc-card-kicker">HOSTNAME</span>
        <div class="pcc-stat-value">
            <?= htmlspecialchars($data['hostname']) ?>
        </div>
    </div>

    <div class="pcc-module-card">
        <span class="pcc-card-kicker">CARGA</span>
        <div class="pcc-stat-value">
            <?= htmlspecialchars(implode(' / ', $data['load'])) ?>
        </div>
    </div>

</div>

<div class="pcc-panel-section">
    <span class="pcc-card-kicker">DISCO</span>
    <pre class="pcc-code-box"><?= htmlspecialchars($data['disk']) ?></pre>
</div>

<div class="pcc-panel-section">
    <span class="pcc-card-kicker">MEMORIA</span>
    <pre class="pcc-code-box"><?= htmlspecialchars($data['memory']) ?></pre>
</div>

<div class="pcc-panel-section">
    <span class="pcc-card-kicker">UPTIME</span>
    <pre class="pcc-code-box"><?= htmlspecialchars($data['uptime']) ?></pre>
</div>
