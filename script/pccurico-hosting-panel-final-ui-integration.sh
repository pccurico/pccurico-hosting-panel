#!/usr/bin/env bash

set -Eeuo pipefail

PROJECT="/var/www/pccurico-hosting-panel"
APP="$PROJECT/app"
VIEWS="$APP/Views"
LAYOUT="$VIEWS/layouts/app.php"
CSS="$PROJECT/public/assets/css"

BACKUP="/root/pccurico-hosting-panel-final-ui-$(date +%Y%m%d-%H%M%S)"

echo
echo "============================================================"
echo " PCCURICO HOSTING PANEL"
echo " INTEGRACION FINAL DE INTERFAZ"
echo "============================================================"
echo "Proyecto : $PROJECT"
echo "Backup   : $BACKUP"
echo "Fecha    : $(date '+%Y-%m-%d %H:%M:%S')"
echo "============================================================"
echo

fail() {
    echo "[ERROR] $1"
    exit 1
}

ok() {
    echo "[OK] $1"
}

mkdir -p "$BACKUP"
mkdir -p "$VIEWS/layouts"
mkdir -p "$CSS"

echo "===== 1. BACKUP ====="

cp -a "$VIEWS" "$BACKUP/Views"

if [[ -f "$PROJECT/routes/web.php" ]]; then
    cp -a "$PROJECT/routes/web.php" "$BACKUP/web.php"
fi

if [[ -f "$CSS/panel.css" ]]; then
    cp -a "$CSS/panel.css" "$BACKUP/panel.css"
fi

ok "Backup creado"

echo
echo "===== 2. LAYOUT UNIFICADO ====="

sudo tee "$LAYOUT" > /dev/null <<'PHP'
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
PHP

php -l "$LAYOUT"

ok "Layout unificado validado"

echo
echo "===== 3. CSS FINAL ====="

sudo tee "$CSS/pccurico-shell.css" > /dev/null <<'CSS'
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    background: #0b1016;
    color: #e8edf3;
    font-family:
        Inter,
        "Segoe UI",
        Arial,
        sans-serif;
}

.pcc-shell {
    min-height: 100vh;
    display: flex;
    background: #0b1016;
}

.pcc-sidebar {
    width: 250px;
    min-width: 250px;
    background: #10161e;
    border-right: 1px solid #252d38;
    display: flex;
    flex-direction: column;
}

.pcc-brand {
    height: 74px;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 0 20px;
    border-bottom: 1px solid #252d38;
}

.pcc-brand-mark {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: grid;
    place-items: center;
    background: #e8edf3;
    color: #10161e;
    font-weight: 800;
    font-size: 18px;
}

.pcc-brand strong {
    display: block;
    font-size: 14px;
    letter-spacing: 1px;
}

.pcc-brand span {
    display: block;
    margin-top: 3px;
    color: #748192;
    font-size: 9px;
    letter-spacing: 1.5px;
}

.pcc-sidebar-scroll {
    flex: 1;
    overflow-y: auto;
    padding: 18px 12px;
}

.pcc-nav-section {
    margin-bottom: 22px;
}

.pcc-nav-title {
    padding: 0 11px 8px;
    color: #667384;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 1.4px;
    text-transform: uppercase;
}

.pcc-nav-item {
    min-height: 40px;
    display: flex;
    align-items: center;
    gap: 11px;
    padding: 0 11px;
    margin-bottom: 3px;
    border-radius: 6px;
    color: #98a5b5;
    text-decoration: none;
    font-size: 12px;
    transition:
        background .15s ease,
        color .15s ease;
}

.pcc-nav-item:hover {
    background: #171f29;
    color: #e8edf3;
}

.pcc-nav-item.active {
    background: #1b2530;
    color: #ffffff;
}

.pcc-nav-icon {
    width: 20px;
    text-align: center;
    font-size: 13px;
}

.pcc-sidebar-footer {
    padding: 14px;
    border-top: 1px solid #252d38;
}

.pcc-system-status {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #758293;
    font-size: 10px;
    margin-bottom: 12px;
}

.pcc-system-status span {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #66b38a;
}

.pcc-logout {
    display: block;
    padding: 9px 11px;
    border-radius: 6px;
    color: #8e9baa;
    text-decoration: none;
    font-size: 11px;
}

.pcc-logout:hover {
    background: #171f29;
    color: #fff;
}

.pcc-main {
    flex: 1;
    min-width: 0;
}

.pcc-topbar {
    min-height: 92px;
    padding: 20px 30px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    border-bottom: 1px solid #252d38;
    background: #10161e;
}

.pcc-breadcrumb {
    color: #657384;
    font-size: 9px;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    margin-bottom: 5px;
}

.pcc-topbar h1 {
    margin: 0;
    font-size: 20px;
    font-weight: 650;
}

.pcc-topbar-subtitle {
    margin: 4px 0 0;
    color: #758293;
    font-size: 11px;
}

.pcc-user-area {
    display: flex;
    align-items: center;
    gap: 10px;
}

.pcc-user-avatar {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: grid;
    place-items: center;
    background: #242d38;
    color: #e8edf3;
    font-size: 12px;
    font-weight: 700;
}

.pcc-user-info strong {
    display: block;
    font-size: 11px;
}

.pcc-user-info span {
    display: block;
    margin-top: 2px;
    color: #667384;
    font-size: 9px;
}

.pcc-content {
    padding: 28px 30px 45px;
}

.pcc-page-title {
    display: none;
}

.pcc-module-grid {
    display: grid;
    grid-template-columns:
        repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
    margin-bottom: 20px;
}

.pcc-module-card,
.pcc-panel-section {
    background: #10161e;
    border: 1px solid #252e39;
    border-radius: 8px;
}

.pcc-module-card {
    padding: 19px;
}

.pcc-panel-section {
    padding: 20px;
    margin-bottom: 18px;
}

.pcc-card-kicker {
    color: #647285;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 1.2px;
    text-transform: uppercase;
}

.pcc-module-card h3,
.pcc-panel-section h3 {
    margin: 7px 0 0;
    font-size: 14px;
    font-weight: 600;
}

.pcc-stat-value {
    margin-top: 11px;
    color: #dbe2e9;
    font-size: 14px;
    word-break: break-word;
}

.pcc-notice {
    display: flex;
    gap: 14px;
    padding: 18px;
    margin-bottom: 18px;
    background: #111922;
    border: 1px solid #2b3744;
    border-radius: 8px;
}

.pcc-notice-icon {
    width: 25px;
    height: 25px;
    min-width: 25px;
    display: grid;
    place-items: center;
    border-radius: 50%;
    background: #25303c;
    color: #b9c5d2;
    font-weight: 700;
}

.pcc-notice strong {
    display: block;
    font-size: 13px;
}

.pcc-notice p {
    margin: 5px 0 0;
    color: #8996a5;
    font-size: 11px;
    line-height: 1.6;
}

@media (max-width: 900px) {
    .pcc-sidebar {
        width: 210px;
        min-width: 210px;
    }

    .pcc-topbar {
        padding: 18px 20px;
    }

    .pcc-content {
        padding: 20px;
    }
}

@media (max-width: 700px) {
    .pcc-sidebar {
        width: 68px;
        min-width: 68px;
    }

    .pcc-brand {
        justify-content: center;
        padding: 0;
    }

    .pcc-brand > div:last-child,
    .pcc-nav-title,
    .pcc-nav-item span:last-child,
    .pcc-system-status,
    .pcc-logout {
        display: none;
    }

    .pcc-nav-item {
        justify-content: center;
    }

    .pcc-user-info {
        display: none;
    }
}
CSS

ok "CSS final instalado"

echo
echo "===== 4. PREPARAR CONTROLADOR DE RENDER ====="

sudo tee "$APP/Controllers/UiController.php" > /dev/null <<'PHP'
<?php

declare(strict_types=1);

namespace Pccurico\HostingPanel\Controllers;

final class UiController
{
    public static function render(
        string $view,
        array $data = [],
        string $title = 'PCCURICO Hosting Panel',
        string $subtitle = ''
    ): void {
        $file = dirname(__DIR__) . '/Views/' . $view . '.php';

        if (!is_file($file)) {
            http_response_code(500);
            exit('Vista no encontrada.');
        }

        extract($data, EXTR_SKIP);

        ob_start();

        require $file;

        $content = ob_get_clean();

        require dirname(__DIR__) . '/Views/layouts/app.php';
    }
}
PHP

php -l "$APP/Controllers/UiController.php"

ok "Renderizador preparado"

echo
echo "===== 5. VALIDAR ARCHIVOS ====="

find "$APP/Controllers" \
    -type f \
    -name '*.php' \
    -print0 |
while IFS= read -r -d '' FILE; do
    php -l "$FILE" >/dev/null ||
        fail "PHP inválido: $FILE"
done

find "$VIEWS" \
    -type f \
    -name '*.php' \
    -print0 |
while IFS= read -r -d '' FILE; do
    php -l "$FILE" >/dev/null ||
        fail "Vista PHP inválida: $FILE"
done

php -l "$PROJECT/routes/web.php"

ok "Todos los PHP son válidos"

echo
echo "===== 6. APACHE ====="

apache2ctl configtest

echo
echo "===== 7. SERVICIOS ====="

systemctl is-active --quiet apache2 ||
    fail "Apache no está activo"

systemctl is-active --quiet php8.3-fpm ||
    fail "PHP-FPM no está activo"

systemctl is-active --quiet mysql ||
    fail "MySQL no está activo"

ok "Apache, PHP-FPM y MySQL activos"

echo
echo "===== 8. TEST HTTP ====="

for PATH_TEST in \
    /dashboard \
    /sites \
    /users \
    /domains \
    /dns \
    /apache \
    /php \
    /mysql \
    /databases \
    /mail \
    /ssl \
    /backups \
    /logs \
    /settings \
    /tools \
    /audit \
    /roles \
    /permissions
do

    STATUS=$(
        curl -sS \
            -o /dev/null \
            -w "%{http_code}" \
            --max-time 15 \
            -H 'Host: hosting.local' \
            "http://127.0.0.1${PATH_TEST}" \
            || true
    )

    case "$STATUS" in
        200|302)
            echo "[OK] $PATH_TEST -> HTTP $STATUS"
            ;;
        *)
            echo "[ERROR] $PATH_TEST -> HTTP $STATUS"
            ;;
    esac

done

echo
echo "============================================================"
echo " INTEGRACION FINAL COMPLETADA"
echo "============================================================"
echo
echo "Layout:"
echo "  $LAYOUT"
echo
echo "CSS:"
echo "  $CSS/pccurico-shell.css"
echo
echo "Backup:"
echo "  $BACKUP"
echo
echo "NO se modificó MySQL."
echo "NO se modificaron tablas."
echo "NO se insertaron registros."
echo "NO se modificó Cloudflare."
echo "NO se ejecutó git commit."
echo "NO se ejecutó git push."
echo
echo "============================================================"
echo " FIN"
echo "============================================================"
