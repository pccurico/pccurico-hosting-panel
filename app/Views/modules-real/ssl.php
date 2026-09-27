<div class="pcc-page-title">
    <div class="pcc-page-icon">🔒</div>
    <div>
        <h2>SSL</h2>
        <p>Certificados encontrados en el servidor.</p>
    </div>
</div>

<div class="pcc-panel-section">

    <span class="pcc-card-kicker">
        CERTIFICADOS
    </span>

    <h3>
        Certificados detectados
    </h3>

    <div class="pcc-list">

    <?php foreach ($data as $certificate): ?>

        <div class="pcc-list-item">
            <?= htmlspecialchars($certificate) ?>
        </div>

    <?php endforeach; ?>

    <?php if (!$data): ?>

        <div class="pcc-list-item">
            No se detectaron certificados.
        </div>

    <?php endif; ?>

    </div>

</div>
