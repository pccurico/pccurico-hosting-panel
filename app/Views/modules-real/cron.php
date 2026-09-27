<?php

declare(strict_types=1);

$title = 'Cron Jobs';
$subtitle = 'Scheduled Tasks';
$content = <<<HTML
<div class="pcc-module-grid">
    <div class="pcc-module-card">
        <h3>Cron Scheduler</h3>
        <p>Manage automated tasks for your hosting environment.</p>
        <a href="/cron" class="pcc-nav-item">
            <span class="pcc-nav-icon">⏰</span>
            <span>Cron Jobs</span>
        </a>
    </div>
</div>
HTML;

include 'partials/header.php';
include 'partials/navigation.php';
include 'partials/footer.php';
?>