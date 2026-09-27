<div class="pcc-page-title">
    <div class="pcc-page-icon">🖥</div>
    <div>
        <h2>Apache</h2>
        <p>Estado del servidor web y VirtualHosts.</p>
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
        <span class="pcc-card-kicker">CONFIGURACIÓN</span>
        <div class="pcc-stat-value"><?= htmlspecialchars($data['syntax']) ?></div>
    </div>

</div>

<div class="pcc-panel-section">
    <span class="pcc-card-kicker">VIRTUALHOSTS</span>
    <h3>Configuración detectada</h3>

    <pre class="pcc-code-box"><?= htmlspecialchars($data['vhosts']) ?></pre>
</div>
