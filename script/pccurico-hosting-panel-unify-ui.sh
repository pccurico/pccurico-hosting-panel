#!/usr/bin/env bash

set -Eeuo pipefail

PROJECT="/var/www/pccurico-hosting-panel"
APP="$PROJECT/app"
VIEWS="$APP/Views"
LAYOUTS="$VIEWS/layouts"
CSS="$PROJECT/public/assets/css"

BACKUP="/root/pccurico-hosting-panel-ui-$(date +%Y%m%d-%H%M%S)"

echo
echo "============================================================"
echo " PCCURICO HOSTING PANEL"
echo " UNIFICACION VISUAL COMPLETA"
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

mkdir -p "$LAYOUTS"
mkdir -p "$CSS"
mkdir -p "$BACKUP"

echo "===== 1. BACKUP DE VISTAS EXISTENTES ====="

for DIR in \
    "$VIEWS/dashboard" \
    "$VIEWS/sites" \
    "$VIEWS/users" \
    "$VIEWS/layouts"
do
    if [[ -d "$DIR" ]]; then
        NAME=$(basename "$DIR")
        cp -a "$DIR" "$BACKUP/$NAME"
    fi
done

if [[ -f "$CSS/panel.css" ]]; then
    cp -a "$CSS/panel.css" "$BACKUP/panel.css"
fi

ok "Backup creado"

echo
echo "===== 2. LAYOUT PRINCIPAL ====="

sudo tee "$LAYOUTS/app.php" > /dev/null <<'PHP'
<?php

declare(strict_types=1);

$title = $title ?? 'PCCURICO Hosting Panel';
$subtitle = $subtitle ?? '';
$content = $content ?? '';
$active = $active ?? '';

$userName = $_SESSION['user_name'] ?? 'Administrador';
$userEmail = $_SESSION['user_email'] ?? '';

$navGroups = [
    'Principal' => [
        ['url' => '/dashboard', 'label' => 'Dashboard', 'icon' => '▦'],
    ],

    'Hosting' => [
        ['url' => '/sites', 'label' => 'Sitios', 'icon' => '⌂'],
        ['url' => '/domains', 'label' => 'Dominios', 'icon' => '🌐'],
        ['url' => '/dns', 'label' => 'DNS', 'icon' => '🔗'],
        ['url' => '/databases', 'label' => 'Bases de datos', 'icon' => '💾'],
        ['url' => '/mail', 'label' => 'Correo', 'icon' => '✉'],
    ],

    'Servidor' => [
        ['url' => '/apache', 'label' => 'Apache', 'icon' => '🖥'],
        ['url' => '/php', 'label' => 'PHP', 'icon' => '🐘'],
        ['url' => '/mysql', 'label' => 'MySQL', 'icon' => '🗄'],
        ['url' => '/logs', 'label' => 'Logs', 'icon' => '▤'],
    ],

    'Seguridad' => [
        ['url' => '/ssl', 'label' => 'SSL', 'icon' => '🔒'],
        ['url' => '/backups', 'label' => 'Backups', 'icon' => '↻'],
        ['url' => '/audit', 'label' => 'Auditoría', 'icon' => '◉'],
    ],

    'Usuarios' => [
        ['url' => '/users', 'label' => 'Usuarios', 'icon' => '♙'],
        ['url' => '/roles', 'label' => 'Roles', 'icon' => '♟'],
        ['url' => '/permissions', 'label' => 'Permisos', 'icon' => '✓'],
    ],

    'Sistema' => [
        ['url' => '/settings', 'label' => 'Configuración', 'icon' => '⚙'],
        ['url' => '/tools', 'label' => 'Herramientas', 'icon' => '🛠'],
    ],
];

function pcc_nav_active(string $url, string $active): string
{
    if ($active !== '') {
        return $active === $url ? 'active' : '';
    }

    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

    return $path === $url ? 'active' : '';
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
        <?= htmlspecialchars($title) ?> |
        PCCURICO Hosting Panel
    </title>

    <link
        rel="stylesheet"
        href="/assets/css/panel.css"
    >

    <link
        rel="stylesheet"
        href="/assets/css/pccurico-modules.css"
    >
</head>

<body class="pcc-panel-body">

<div class="pcc-shell">

    <aside class="pcc-sidebar">

        <div class="pcc-brand">

            <div class="pcc-brand-mark">
                P
            </div>

            <div>
                <strong>PCCURICO</strong>
                <span>Hosting Panel</span>
            </div>

        </div>

        <nav class="pcc-nav">

            <?php foreach ($navGroups as $groupName => $items): ?>

                <div class="pcc-nav-label">
                    <?= htmlspecialchars($groupName) ?>
                </div>

                <?php foreach ($items as $item): ?>

                    <a
                        href="<?= htmlspecialchars($item['url']) ?>"
                        class="pcc-nav-link <?= pcc_nav_active($item['url'], $active) ?>"
                    >
                        <span>
                            <?= $item['icon'] ?>
                        </span>

                        <span>
                            <?= htmlspecialchars($item['label']) ?>
                        </span>
                    </a>

                <?php endforeach; ?>

            <?php endforeach; ?>

            <div class="pcc-nav-label">
                Cuenta
            </div>

            <a
                href="/logout"
                class="pcc-nav-link pcc-nav-danger"
            >
                <span>⇥</span>
                <span>Cerrar sesión</span>
            </a>

        </nav>

    </aside>

    <main class="pcc-main">

        <header class="pcc-topbar">

            <div>

                <div class="pcc-breadcrumb">
                    PCCURICO Hosting Panel
                </div>

                <h1>
                    <?= htmlspecialchars($title) ?>
                </h1>

                <?php if ($subtitle !== ''): ?>

                    <p>
                        <?= htmlspecialchars($subtitle) ?>
                    </p>

                <?php endif; ?>

            </div>

            <div class="pcc-user-box">

                <span class="pcc-status-dot"></span>

                <div>
                    <strong>
                        <?= htmlspecialchars($userName) ?>
                    </strong>

                    <?php if ($userEmail !== ''): ?>

                        <small>
                            <?= htmlspecialchars($userEmail) ?>
                        </small>

                    <?php endif; ?>

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

ok "Layout principal creado"

echo
echo "===== 3. CSS SHELL ====="

sudo tee "$CSS/pccurico-shell.css" > /dev/null <<'CSS'
.pcc-panel-body {
    margin: 0;
    padding: 0;
    min-height: 100vh;
    background: #0d1117;
    color: #e6edf3;
    font-family:
        Inter,
        -apple-system,
        BlinkMacSystemFont,
        "Segoe UI",
        sans-serif;
}

.pcc-shell {
    display: flex;
    min-height: 100vh;
}

.pcc-sidebar {
    position: fixed;
    inset: 0 auto 0 0;
    width: 250px;
    background: #11161d;
    border-right: 1px solid #252d38;
    padding: 22px 14px;
    box-sizing: border-box;
    overflow-y: auto;
    z-index: 100;
}

.pcc-brand {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px 10px 25px;
    border-bottom: 1px solid #252d38;
    margin-bottom: 18px;
}

.pcc-brand-mark {
    width: 38px;
    height: 38px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #1d2733;
    border: 1px solid #394656;
    font-size: 20px;
    font-weight: 800;
}

.pcc-brand strong {
    display: block;
    font-size: 14px;
    letter-spacing: 1px;
}

.pcc-brand span {
    display: block;
    margin-top: 2px;
    color: #8491a2;
    font-size: 11px;
}

.pcc-nav-label {
    color: #697687;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    padding: 17px 11px 7px;
}

.pcc-nav-link {
    display: flex;
    align-items: center;
    gap: 11px;
    min-height: 39px;
    padding: 0 11px;
    margin: 3px 0;
    border-radius: 7px;
    color: #aeb9c7;
    text-decoration: none;
    font-size: 13px;
    transition: background .15s ease, color .15s ease;
}

.pcc-nav-link:hover {
    background: #19212b;
    color: #f4f7fb;
}

.pcc-nav-link.active {
    background: #202b38;
    color: #ffffff;
    border-left: 3px solid #7aa2f7;
    padding-left: 8px;
}

.pcc-nav-link span:first-child {
    width: 20px;
    text-align: center;
}

.pcc-nav-danger {
    color: #d78a8a;
}

.pcc-main {
    width: calc(100% - 250px);
    margin-left: 250px;
    min-width: 0;
}

.pcc-topbar {
    min-height: 90px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 25px;
    padding: 20px 32px;
    box-sizing: border-box;
    border-bottom: 1px solid #252d38;
    background: #10151c;
}

.pcc-breadcrumb {
    color: #697687;
    font-size: 11px;
    margin-bottom: 7px;
}

.pcc-topbar h1 {
    margin: 0;
    font-size: 24px;
    line-height: 1.2;
}

.pcc-topbar p {
    margin: 6px 0 0;
    color: #8491a2;
    font-size: 13px;
}

.pcc-user-box {
    display: flex;
    align-items: center;
    gap: 9px;
    border: 1px solid #293340;
    background: #151b23;
    padding: 8px 12px;
    border-radius: 8px;
    color: #c4ced9;
    font-size: 12px;
}

.pcc-user-box strong {
    display: block;
    font-size: 12px;
}

.pcc-user-box small {
    display: block;
    margin-top: 2px;
    color: #697687;
    font-size: 10px;
}

.pcc-status-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #6fbd7b;
    flex: 0 0 8px;
}

.pcc-content {
    padding: 30px 32px 50px;
}

@media (max-width: 900px) {

    .pcc-sidebar {
        width: 210px;
    }

    .pcc-main {
        width: calc(100% - 210px);
        margin-left: 210px;
    }

    .pcc-topbar,
    .pcc-content {
        padding-left: 22px;
        padding-right: 22px;
    }

}

@media (max-width: 680px) {

    .pcc-sidebar {
        position: static;
        width: 100%;
        min-height: auto;
        max-height: none;
    }

    .pcc-shell {
        display: block;
    }

    .pcc-main {
        width: 100%;
        margin-left: 0;
    }

    .pcc-topbar {
        align-items: flex-start;
    }

    .pcc-user-box {
        display: none;
    }

}
CSS

ok "CSS shell creado"

echo
echo "===== 4. CONTROLADOR DE LAYOUT ====="

sudo tee "$APP/Controllers/UnifiedViewController.php" > /dev/null <<'PHP'
<?php

declare(strict_types=1);

namespace Pccurico\HostingPanel\Controllers;

abstract class UnifiedViewController
{
    protected function renderPage(
        string $view,
        string $title,
        string $subtitle = '',
        string $active = '',
        array $data = []
    ): void {

        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $viewFile =
            dirname(__DIR__) .
            '/Views/' .
            $view .
            '.php';

        if (!is_file($viewFile)) {
            http_response_code(500);
            exit('Vista no encontrada.');
        }

        extract($data, EXTR_SKIP);

        ob_start();

        require $viewFile;

        $content = ob_get_clean();

        require dirname(__DIR__) . '/Views/layouts/app.php';
    }
}
PHP

php -l "$APP/Controllers/UnifiedViewController.php"

ok "Controlador base creado"

echo
echo "===== 5. ACTUALIZAR CSS DE MODULOS ====="

if ! grep -q "pccurico-shell.css" "$VIEWS/modules/domains.php"; then

    sed -i \
        's#<link rel="stylesheet" href="/assets/css/panel.css">#<link rel="stylesheet" href="/assets/css/panel.css">\n    <link rel="stylesheet" href="/assets/css/pccurico-shell.css">#' \
        "$VIEWS/modules/"*.php

fi

ok "CSS shell añadido a módulos"

echo
echo "===== 6. CREAR VISTA DE CONTENIDO DE MODULO ====="

sudo tee "$VIEWS/module-content.php" > /dev/null <<'PHP'
<?php
declare(strict_types=1);
?>

<div class="pcc-page-title">

    <div class="pcc-page-icon">
        <?= htmlspecialchars($icon ?? '▦') ?>
    </div>

    <div>
        <h2>
            <?= htmlspecialchars($title ?? 'Módulo') ?>
        </h2>

        <p>
            <?= htmlspecialchars($subtitle ?? '') ?>
        </p>
    </div>

</div>

<div class="pcc-module-grid">

    <div class="pcc-module-card pcc-module-main">

        <div class="pcc-module-card-header">

            <div>

                <span class="pcc-card-kicker">
                    MÓDULO
                </span>

                <h3>
                    <?= htmlspecialchars($title ?? '') ?>
                </h3>

            </div>

            <span class="pcc-module-icon">
                <?= htmlspecialchars($icon ?? '') ?>
            </span>

        </div>

        <p class="pcc-module-description">
            <?= htmlspecialchars($subtitle ?? '') ?>
        </p>

        <div class="pcc-module-state">

            <span class="pcc-status-dot"></span>

            Interfaz disponible

        </div>

    </div>

    <div class="pcc-module-card">

        <span class="pcc-card-kicker">
            ESTADO
        </span>

        <div class="pcc-stat-value">
            Preparado
        </div>

        <p>
            Módulo integrado al panel administrativo.
        </p>

    </div>

    <div class="pcc-module-card">

        <span class="pcc-card-kicker">
            SEGURIDAD
        </span>

        <div class="pcc-stat-value">
            Protegido
        </div>

        <p>
            Acceso mediante sesión autenticada.
        </p>

    </div>

</div>

<div class="pcc-panel-section">

    <div class="pcc-section-header">

        <span class="pcc-card-kicker">
            ADMINISTRACIÓN
        </span>

        <h3>
            Operaciones
        </h3>

    </div>

    <div class="pcc-action-grid">

        <button type="button" class="pcc-action-card">

            <span>＋</span>

            <strong>
                Crear
            </strong>

            <small>
                Crear nuevo recurso
            </small>

        </button>

        <button type="button" class="pcc-action-card">

            <span>≡</span>

            <strong>
                Administrar
            </strong>

            <small>
                Gestionar recursos
            </small>

        </button>

        <button type="button" class="pcc-action-card">

            <span>↻</span>

            <strong>
                Actualizar
            </strong>

            <small>
                Consultar estado
            </small>

        </button>

        <button type="button" class="pcc-action-card">

            <span>⚙</span>

            <strong>
                Configuración
            </strong>

            <small>
                Configurar módulo
            </small>

        </button>

    </div>

</div>

<div class="pcc-notice">

    <div class="pcc-notice-icon">
        i
    </div>

    <div>

        <strong>
            Módulo preparado
        </strong>

        <p>
            La interfaz está preparada para conectar las operaciones
            reales del servidor durante la siguiente fase.
        </p>

    </div>

</div>
PHP

ok "Vista base creada"

echo
echo "===== 7. VALIDACIONES ====="

find "$APP/Controllers" \
    -maxdepth 1 \
    -type f \
    -name '*.php' \
    -print0 |
while IFS= read -r -d '' FILE; do
    php -l "$FILE" >/dev/null ||
        fail "Error PHP en $FILE"
done

find "$VIEWS" \
    -type f \
    -name '*.php' \
    -print0 |
while IFS= read -r -d '' FILE; do
    php -l "$FILE" >/dev/null ||
        fail "Error PHP en $FILE"
done

php -l "$PROJECT/routes/web.php"

ok "Todas las vistas y controladores son válidos"

echo
echo "===== 8. APACHE ====="

apache2ctl configtest

echo
echo "===== 9. SERVICIOS ====="

systemctl is-active --quiet apache2 ||
    fail "Apache no está activo"

systemctl is-active --quiet php8.3-fpm ||
    fail "PHP-FPM no está activo"

systemctl is-active --quiet mysql ||
    fail "MySQL no está activo"

ok "Servicios activos"

echo
echo "===== 10. TEST HTTP ====="

for MODULE in \
    dashboard \
    sites \
    users \
    domains \
    dns \
    apache \
    php \
    mysql \
    databases \
    mail \
    ssl \
    backups \
    logs \
    settings \
    tools \
    audit \
    roles \
    permissions
do

    STATUS=$(curl -sS \
        -o /dev/null \
        -w "%{http_code}" \
        --max-time 10 \
        -H 'Host: hosting.local' \
        "http://127.0.0.1/$MODULE" || true)

    case "$STATUS" in
        200|302)
            echo "[OK] /$MODULE -> HTTP $STATUS"
            ;;
        *)
            echo "[AVISO] /$MODULE -> HTTP $STATUS"
            ;;
    esac

done

echo
echo "============================================================"
echo " UI UNIFICADA"
echo "============================================================"
echo
echo "Layout:"
echo "  $LAYOUTS/app.php"
echo
echo "CSS:"
echo "  $CSS/pccurico-shell.css"
echo
echo "Backup:"
echo "  $BACKUP"
echo
echo "NO se modificó MySQL."
echo "NO se modificó Cloudflare."
echo "NO se ejecutó git commit."
echo "NO se ejecutó git push."
echo
echo "============================================================"
echo " FIN"
echo "============================================================"
