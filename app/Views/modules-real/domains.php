<div class="pcc-page-title">
    <div class="pcc-page-icon">🌐</div>
    <div>
        <h2>Dominios</h2>
        <p>Dominios detectados desde Apache.</p>
    </div>
</div>

<div class="pcc-panel-section">
    <div class="pcc-section-header">
        <span class="pcc-card-kicker">DOMINIOS</span>
        <h3>VirtualHosts detectados</h3>
    </div>

    <div class="pcc-table-wrap">
        <table class="pcc-table">
            <thead>
                <tr>
                    <th>Dominio</th>
                    <th>Aliases</th>
                    <th>Configuración</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($data as $row): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($row['domain']) ?></strong></td>
                    <td><?= htmlspecialchars($row['aliases'] ?: '-') ?></td>
                    <td><?= htmlspecialchars($row['file']) ?></td>
                </tr>
            <?php endforeach; ?>

            <?php if (!$data): ?>
                <tr>
                    <td colspan="3">No se detectaron dominios.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
