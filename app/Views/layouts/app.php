<?php

declare(strict_types=1);

$pageTitle = $title ?? 'PCCURICO Hosting Panel';
$pageSubtitle = $subtitle ?? '';
$content = $content ?? '';
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

$navigation = [
    [
        'section' => 'Principal',
        'items' => [
            ['/dashboard', 'Dashboard', '▦'],
        ],
    ],
    [
        'section' => 'Hosting',
        'items' => [
            ['/sites', 'Sitios', '◈'],
            ['/domains', 'Dominios', '🌐'],
            ['/dns', 'DNS', '⌁'],
            ['/mail', 'Correo', '✉'],
            ['/ssl', 'SSL', '🔒'],
            ['/backups', 'Backups', '↻'],
        ],
    ],
    [
        'section' => 'Servidor',
        'items' => [
            ['/apache', 'Apache', '▣'],
            ['/php', 'PHP', '🐘'],
            ['/mysql', 'MySQL', '▤'],
            ['/databases', 'Bases de datos', '◫'],
            ['/logs', 'Logs', '▤'],
        ],
    ],
    [
        'section' => 'Administración',
        'items' => [
            ['/users', 'Usuarios', '♙'],
            ['/roles', 'Roles', '♜'],
            ['/permissions', 'Permisos', '✓'],
            ['/audit', 'Auditoría', '◉'],
        ],
    ],
    [
        'section' => 'Sistema',
        'items' => [
            ['/settings', 'Configuración', '⚙'],
            ['/tools', 'Herramientas', '🛠'],
        ],
    ],
];

function pccActive(string $path, string $currentPath): string
{
    if ($path === '/dashboard') {
        return $currentPath === '/dashboard' ? 'active' : '';
    }

    return str_starts_with($currentPath, $path) ? 'active' : '';
}
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
        <?= htmlspecialchars($pageTitle) ?> | PCCURICO Hosting
    </title>

    <link
        rel="stylesheet"
        href="/assets/css/panel.css"
    >

    <link
        rel="stylesheet"
        href="/assets/css/pccurico-shell.css"
    >

    <link
        rel="stylesheet"
        href="/assets/css/pccurico-server-modules.css"
    >
</head>

<body>

<div class="pcc-shell">

    <aside class="pcc-sidebar">

        <div class="pcc-brand">

            <div class="pcc-brand-mark">
                P
            </div>

            <div>
                <strong>PCCURICO</strong>
                <span>HOSTING PANEL</span>
            </div>

        </div>

        <div class="pcc-sidebar-scroll">

            <?php foreach ($navigation as $group): ?>

                <div class="pcc-nav-section">

                    <div class="pcc-nav-title">
                        <?= htmlspecialchars($group['section']) ?>
                    </div>

                    <?php foreach ($group['items'] as $item): ?>

                        <?php
                        [$path, $label, $icon] = $item;
                        $active = pccActive(
                            $path,
                            $currentPath
                        );
                        ?>

                        <a
                            href="<?= htmlspecialchars($path) ?>"
                            class="pcc-nav-item <?= $active ?>"
                        >
                            <span class="pcc-nav-icon">
                                <?= $icon ?>
                            </span>

                            <span>
                                <?= htmlspecialchars($label) ?>
                            </span>
                        </a>

                    <?php endforeach; ?>

                </div>

            <?php endforeach; ?>

        </div>

        <div class="pcc-sidebar-footer">

            <div class="pcc-system-status">
                <span></span>
                Sistema operativo
            </div>

            <a href="/logout" class="pcc-logout">
                Cerrar sesión
            </a>

        </div>

    </aside>

    <main class="pcc-main">

        <header class="pcc-topbar">

            <div>

                <div class="pcc-breadcrumb">
                    PCCURICO / <?= htmlspecialchars($pageTitle) ?>
                </div>

                <h1>
                    <?= htmlspecialchars($pageTitle) ?>
                </h1>

                <?php if ($pageSubtitle !== ''): ?>

                    <p class="pcc-topbar-subtitle">
                        <?= htmlspecialchars($pageSubtitle) ?>
                    </p>

                <?php endif; ?>

            </div>

            <div class="pcc-user-area">

                <div class="pcc-user-avatar">
                    <?= strtoupper(
                        substr(
                            (string)($_SESSION['user_name'] ?? 'A'),
                            0,
                            1
                        )
                    ) ?>
                </div>

                <div class="pcc-user-info">

                    <strong>
                        <?= htmlspecialchars(
                            (string)(
                                $_SESSION['user_name']
                                ?? 'Administrador'
                            )
                        ) ?>
                    </strong>

                    <span>
                        Administrador
                    </span>

                </div>

            </div>

        </header>

        <section class="pcc-content">

            <?= $content ?>

        </section>

    </main>

</div>

</body>
</html>
