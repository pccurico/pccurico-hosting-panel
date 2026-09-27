<div class="pcc-page-title">
    <div class="pcc-page-icon">▤</div>
    <div>
        <h2>Logs</h2>
        <p>Últimos registros disponibles.</p>
    </div>
</div>

<?php foreach ($data as $file => $lines): ?>

<div class="pcc-panel-section">

    <span class="pcc-card-kicker">
        <?= htmlspecialchars($file) ?>
    </span>

    <pre class="pcc-code-box"><?= htmlspecialchars(implode("\n", $lines)) ?></pre>

</div>

<?php endforeach; ?>
