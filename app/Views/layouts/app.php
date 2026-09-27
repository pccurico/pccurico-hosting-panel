<?php

declare(strict_types=1);

$title = $title ?? 'PCCURICO Hosting Panel';
$content = $content ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
        - PCCURICO Hosting Panel
    </title>

    <link
        rel="stylesheet"
        href="/assets/css/panel.css"
    >
</head>

<body>

<?= $content ?>

</body>
</html>
