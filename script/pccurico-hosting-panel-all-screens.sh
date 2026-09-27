#!/usr/bin/env bash

set -Eeuo pipefail

PROJECT="/var/www/pccurico-hosting-panel"
SCRIPT_DIR="$PROJECT/script"
APP_DIR="$PROJECT/app"
CONTROLLER_DIR="$APP_DIR/Controllers"
VIEW_DIR="$APP_DIR/Views"
PUBLIC_DIR="$PROJECT/public"
CSS_DIR="$PUBLIC_DIR/assets/css"
ROUTES="$PROJECT/routes/web.php"

BACKUP="/root/pccurico-hosting-panel-all-screens-$(date +%Y%m%d-%H%M%S)"

echo
echo "============================================================"
echo " PCCURICO HOSTING PANEL"
echo " INSTALACION COMPLETA DE PANTALLAS"
echo "============================================================"
echo "Proyecto : $PROJECT"
echo "Backup   : $BACKUP"
echo "Fecha    : $(date '+%Y-%m-%d %H:%M:%S')"
echo "============================================================"
echo

fail() {
    echo
    echo "[ERROR] $1"
    exit 1
}

ok() {
    echo "[OK] $1"
}

warn() {
    echo "[AVISO] $1"
}

mkdir -p "$SCRIPT_DIR"
mkdir -p "$CONTROLLER_DIR"
mkdir -p "$VIEW_DIR"
mkdir -p "$CSS_DIR"

[[ -d "$PROJECT" ]] || fail "No existe el proyecto"
[[ -f "$ROUTES" ]] || fail "No existe routes/web.php"

echo "===== 1. BACKUP ====="

mkdir -p "$BACKUP"

cp -a "$ROUTES" "$BACKUP/web.php"

if [[ -f "$CSS_DIR/panel.css" ]]; then
    cp -a "$CSS_DIR/panel.css" "$BACKUP/panel.css"
fi

ok "Backup creado"

echo
echo "===== 2. CONTROLADOR DE MODULOS ====="

sudo tee "$CONTROLLER_DIR/PanelModulesController.php" > /dev/null <<'PHP'
<?php

declare(strict_types=1);

namespace Pccurico\HostingPanel\Controllers;

final class PanelModulesController
{
    private array $modules = [
        'domains' => [
            'title' => 'Dominios',
            'subtitle' => 'Administración de dominios y aliases',
            'icon' => '🌐',
            'section' => 'Hosting',
        ],
        'dns' => [
            'title' => 'DNS',
            'subtitle' => 'Gestión de zonas y registros DNS',
            'icon' => '🔗',
            'section' => 'Hosting',
        ],
        'apache' => [
            'title' => 'Apache',
            'subtitle' => 'Servidor web y VirtualHosts',
            'icon' => '🖥',
            'section' => 'Servidor',
        ],
        'php' => [
            'title' => 'PHP',
            'subtitle' => 'Versiones, PHP-FPM y configuración',
            'icon' => '🐘',
            'section' => 'Servidor',
        ],
        'mysql' => [
            'title' => 'MySQL',
            'subtitle' => 'Servidor y estado de MySQL',
            'icon' => '🗄',
            'section' => 'Servidor',
        ],
        'databases' => [
            'title' => 'Bases de datos',
            'subtitle' => 'Bases de datos, usuarios y permisos',
            'icon' => '💾',
            'section' => 'Hosting',
        ],
        'mail' => [
            'title' => 'Correo',
            'subtitle' => 'Cuentas y configuración de correo',
            'icon' => '✉',
            'section' => 'Hosting',
        ],
        'ssl' => [
            'title' => 'SSL',
            'subtitle' => 'Certificados y seguridad HTTPS',
            'icon' => '🔒',
            'section' => 'Seguridad',
        ],
        'backups' => [
            'title' => 'Backups',
            'subtitle' => 'Copias de seguridad y restauración',
            'icon' => '↻',
            'section' => 'Seguridad',
        ],
        'logs' => [
            'title' => 'Logs',
            'subtitle' => 'Registros del servidor y aplicaciones',
            'icon' => '▤',
            'section' => 'Servidor',
        ],
        'settings' => [
            'title' => 'Configuración',
            'subtitle' => 'Configuración general del panel',
            'icon' => '⚙',
            'section' => 'Sistema',
        ],
        'tools' => [
            'title' => 'Herramientas',
            'subtitle' => 'Utilidades administrativas del servidor',
            'icon' => '🛠',
            'section' => 'Sistema',
        ],
        'audit' => [
            'title' => 'Auditoría',
            'subtitle' => 'Actividad y eventos administrativos',
            'icon' => '◉',
            'section' => 'Seguridad',
        ],
        'roles' => [
            'title' => 'Roles',
            'subtitle' => 'Administración de roles de acceso',
            'icon' => '♟',
            'section' => 'Usuarios',
        ],
        'permissions' => [
            'title' => 'Permisos',
            'subtitle' => 'Permisos y capacidades del panel',
            'icon' => '✓',
            'section' => 'Usuarios',
        ],
    ];

    public function show(string $module): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        if (!isset($this->modules[$module])) {
            http_response_code(404);
            exit('Módulo no encontrado.');
        }

        $data = $this->modules[$module];

        $view = dirname(__DIR__) . '/Views/modules/' . $module . '.php';

        if (!is_file($view)) {
            http_response_code(500);
            exit('Vista del módulo no encontrada.');
        }

        extract([
            'module' => $module,
            'title' => $data['title'],
            'subtitle' => $data['subtitle'],
            'icon' => $data['icon'],
            'section' => $data['section'],
        ], EXTR_SKIP);

        require $view;
    }
}
PHP

php -l "$CONTROLLER_DIR/PanelModulesController.php"

ok "Controlador de módulos creado"

echo
echo "===== 3. VISTAS DE MODULOS ====="

mkdir -p "$VIEW_DIR/modules"

create_view() {
    local module="$1"
    local title="$2"
    local subtitle="$3"
    local icon="$4"
    local section="$5"

    sudo tee "$VIEW_DIR/modules/$module.php" > /dev/null <<PHP
<?php

declare(strict_types=1);

\$module = '$module';
\$title = '$title';
\$subtitle = '$subtitle';
\$icon = '$icon';
\$section = '$section';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(\$title) ?> | PCCURICO Hosting Panel</title>
    <link rel="stylesheet" href="/assets/css/panel.css">
</head>

<body class="pcc-panel-body">

<div class="pcc-shell">

    <aside class="pcc-sidebar">

        <div class="pcc-brand">
            <div class="pcc-brand-mark">P</div>
            <div>
                <strong>PCCURICO</strong>
                <span>Hosting Panel</span>
            </div>
        </div>

        <nav class="pcc-nav">

            <div class="pcc-nav-label">Principal</div>

            <a href="/dashboard" class="pcc-nav-link">
                <span>▦</span>
                <span>Dashboard</span>
            </a>

            <div class="pcc-nav-label">Hosting</div>

            <a href="/sites" class="pcc-nav-link">
                <span>⌂</span>
                <span>Sitios</span>
            </a>

            <a href="/domains" class="pcc-nav-link <?= \$module === 'domains' ? 'active' : '' ?>">
                <span>🌐</span>
                <span>Dominios</span>
            </a>

            <a href="/dns" class="pcc-nav-link <?= \$module === 'dns' ? 'active' : '' ?>">
                <span>🔗</span>
                <span>DNS</span>
            </a>

            <a href="/databases" class="pcc-nav-link <?= \$module === 'databases' ? 'active' : '' ?>">
                <span>💾</span>
                <span>Bases de datos</span>
            </a>

            <a href="/mail" class="pcc-nav-link <?= \$module === 'mail' ? 'active' : '' ?>">
                <span>✉</span>
                <span>Correo</span>
            </a>

            <div class="pcc-nav-label">Servidor</div>

            <a href="/apache" class="pcc-nav-link <?= \$module === 'apache' ? 'active' : '' ?>">
                <span>🖥</span>
                <span>Apache</span>
            </a>

            <a href="/php" class="pcc-nav-link <?= \$module === 'php' ? 'active' : '' ?>">
                <span>🐘</span>
                <span>PHP</span>
            </a>

            <a href="/mysql" class="pcc-nav-link <?= \$module === 'mysql' ? 'active' : '' ?>">
                <span>🗄</span>
                <span>MySQL</span>
            </a>

            <a href="/logs" class="pcc-nav-link <?= \$module === 'logs' ? 'active' : '' ?>">
                <span>▤</span>
                <span>Logs</span>
            </a>

            <div class="pcc-nav-label">Seguridad</div>

            <a href="/ssl" class="pcc-nav-link <?= \$module === 'ssl' ? 'active' : '' ?>">
                <span>🔒</span>
                <span>SSL</span>
            </a>

            <a href="/backups" class="pcc-nav-link <?= \$module === 'backups' ? 'active' : '' ?>">
                <span>↻</span>
                <span>Backups</span>
            </a>

            <a href="/audit" class="pcc-nav-link <?= \$module === 'audit' ? 'active' : '' ?>">
                <span>◉</span>
                <span>Auditoría</span>
            </a>

            <div class="pcc-nav-label">Usuarios</div>

            <a href="/users" class="pcc-nav-link">
                <span>♙</span>
                <span>Usuarios</span>
            </a>

            <a href="/roles" class="pcc-nav-link <?= \$module === 'roles' ? 'active' : '' ?>">
                <span>♟</span>
                <span>Roles</span>
            </a>

            <a href="/permissions" class="pcc-nav-link <?= \$module === 'permissions' ? 'active' : '' ?>">
                <span>✓</span>
                <span>Permisos</span>
            </a>

            <div class="pcc-nav-label">Sistema</div>

            <a href="/settings" class="pcc-nav-link <?= \$module === 'settings' ? 'active' : '' ?>">
                <span>⚙</span>
                <span>Configuración</span>
            </a>

            <a href="/tools" class="pcc-nav-link <?= \$module === 'tools' ? 'active' : '' ?>">
                <span>🛠</span>
                <span>Herramientas</span>
            </a>

            <a href="/logout" class="pcc-nav-link pcc-nav-danger">
                <span>⇥</span>
                <span>Cerrar sesión</span>
            </a>

        </nav>

    </aside>

    <main class="pcc-main">

        <header class="pcc-topbar">

            <div>
                <div class="pcc-breadcrumb">
                    PCCURICO Hosting Panel / <?= htmlspecialchars(\$section) ?>
                </div>

                <h1><?= htmlspecialchars(\$title) ?></h1>

                <p><?= htmlspecialchars(\$subtitle) ?></p>
            </div>

            <div class="pcc-user-box">
                <span class="pcc-status-dot"></span>
                <span>Administrador</span>
            </div>

        </header>

        <section class="pcc-content">

            <div class="pcc-page-title">
                <div class="pcc-page-icon"><?= \$icon ?></div>

                <div>
                    <h2><?= htmlspecialchars(\$title) ?></h2>
                    <p><?= htmlspecialchars(\$subtitle) ?></p>
                </div>
            </div>

            <div class="pcc-module-grid">

                <div class="pcc-module-card pcc-module-main">

                    <div class="pcc-module-card-header">
                        <div>
                            <span class="pcc-card-kicker">MÓDULO</span>
                            <h3><?= htmlspecialchars(\$title) ?></h3>
                        </div>

                        <span class="pcc-module-icon"><?= \$icon ?></span>
                    </div>

                    <p class="pcc-module-description">
                        Este módulo forma parte del sistema de administración
                        centralizada de PCCURICO Hosting.
                    </p>

                    <div class="pcc-module-state">
                        <span class="pcc-status-dot"></span>
                        Interfaz preparada
                    </div>

                </div>

                <div class="pcc-module-card">

                    <span class="pcc-card-kicker">ESTADO</span>

                    <div class="pcc-stat-value">
                        Preparado
                    </div>

                    <p>
                        La interfaz está instalada y lista para conectar
                        las operaciones reales del servidor.
                    </p>

                </div>

                <div class="pcc-module-card">

                    <span class="pcc-card-kicker">SEGURIDAD</span>

                    <div class="pcc-stat-value">
                        Sesión requerida
                    </div>

                    <p>
                        El acceso a este módulo requiere autenticación
                        dentro del panel.
                    </p>

                </div>

            </div>

            <div class="pcc-panel-section">

                <div class="pcc-section-header">
                    <div>
                        <span class="pcc-card-kicker">ADMINISTRACIÓN</span>
                        <h3>Herramientas de <?= htmlspecialchars(\$title) ?></h3>
                    </div>
                </div>

                <div class="pcc-action-grid">

                    <button type="button" class="pcc-action-card">
                        <span>＋</span>
                        <strong>Crear</strong>
                        <small>Preparar nuevo recurso</small>
                    </button>

                    <button type="button" class="pcc-action-card">
                        <span>≡</span>
                        <strong>Administrar</strong>
                        <small>Gestionar recursos existentes</small>
                    </button>

                    <button type="button" class="pcc-action-card">
                        <span>↻</span>
                        <strong>Actualizar</strong>
                        <small>Consultar estado actual</small>
                    </button>

                    <button type="button" class="pcc-action-card">
                        <span>⚙</span>
                        <strong>Configuración</strong>
                        <small>Opciones del módulo</small>
                    </button>

                </div>

            </div>

            <div class="pcc-notice">

                <div class="pcc-notice-icon">i</div>

                <div>
                    <strong>Interfaz instalada</strong>

                    <p>
                        Las operaciones destructivas y los cambios sobre
                        servidor/base de datos se habilitarán en la etapa
                        de implementación funcional.
                    </p>
                </div>

            </div>

        </section>

    </main>

</div>

</body>
</html>
PHP
}

create_view "domains" "Dominios" "Administración de dominios y aliases" "🌐" "Hosting"
create_view "dns" "DNS" "Gestión de zonas y registros DNS" "🔗" "Hosting"
create_view "apache" "Apache" "Servidor web y VirtualHosts" "🖥" "Servidor"
create_view "php" "PHP" "Versiones, PHP-FPM y configuración" "🐘" "Servidor"
create_view "mysql" "MySQL" "Servidor y estado de MySQL" "🗄" "Servidor"
create_view "databases" "Bases de datos" "Bases de datos, usuarios y permisos" "💾" "Hosting"
create_view "mail" "Correo" "Cuentas y configuración de correo" "✉" "Hosting"
create_view "ssl" "SSL" "Certificados y seguridad HTTPS" "🔒" "Seguridad"
create_view "backups" "Backups" "Copias de seguridad y restauración" "↻" "Seguridad"
create_view "logs" "Logs" "Registros del servidor y aplicaciones" "▤" "Servidor"
create_view "settings" "Configuración" "Configuración general del panel" "⚙" "Sistema"
create_view "tools" "Herramientas" "Utilidades administrativas del servidor" "🛠" "Sistema"
create_view "audit" "Auditoría" "Actividad y eventos administrativos" "◉" "Seguridad"
create_view "roles" "Roles" "Administración de roles de acceso" "♟" "Usuarios"
create_view "permissions" "Permisos" "Permisos y capacidades del panel" "✓" "Usuarios"

ok "15 pantallas creadas"

echo
echo "===== 4. CSS DEL SHELL ====="

sudo tee "$CSS_DIR/pccurico-modules.css" > /dev/null <<'CSS'
.pcc-panel-body {
    margin: 0;
    padding: 0;
    min-height: 100vh;
    background: #0d1117;
    color: #e6edf3;
    font-family: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
}

.pcc-shell {
    display: flex;
    min-height: 100vh;
}

.pcc-sidebar {
    width: 250px;
    flex: 0 0 250px;
    background: #11161d;
    border-right: 1px solid #252d38;
    padding: 22px 14px;
    box-sizing: border-box;
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
    transition: .15s ease;
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
    flex: 1;
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
    gap: 8px;
    border: 1px solid #293340;
    background: #151b23;
    padding: 9px 13px;
    border-radius: 8px;
    color: #b8c3d0;
    font-size: 12px;
}

.pcc-status-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #6fbd7b;
}

.pcc-content {
    padding: 30px 32px 50px;
}

.pcc-page-title {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 25px;
}

.pcc-page-icon {
    width: 54px;
    height: 54px;
    border: 1px solid #303b48;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #151c25;
    font-size: 24px;
}

.pcc-page-title h2 {
    margin: 0;
    font-size: 20px;
}

.pcc-page-title p {
    margin: 5px 0 0;
    color: #7f8b9b;
    font-size: 13px;
}

.pcc-module-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr;
    gap: 16px;
    margin-bottom: 20px;
}

.pcc-module-card {
    background: #121820;
    border: 1px solid #27313d;
    border-radius: 10px;
    padding: 22px;
    min-height: 150px;
    box-sizing: border-box;
}

.pcc-module-main {
    min-height: 180px;
}

.pcc-module-card-header {
    display: flex;
    justify-content: space-between;
    gap: 20px;
}

.pcc-module-card h3 {
    margin: 5px 0 0;
    font-size: 17px;
}

.pcc-module-icon {
    font-size: 25px;
}

.pcc-card-kicker {
    display: block;
    color: #667486;
    font-size: 9px;
    font-weight: 800;
    letter-spacing: 1.3px;
}

.pcc-module-description,
.pcc-module-card p {
    color: #8995a4;
    font-size: 13px;
    line-height: 1.6;
}

.pcc-module-state {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #aebbc8;
    font-size: 12px;
    margin-top: 10px;
}

.pcc-stat-value {
    margin-top: 14px;
    font-size: 18px;
    font-weight: 700;
}

.pcc-panel-section {
    background: #121820;
    border: 1px solid #27313d;
    border-radius: 10px;
    padding: 23px;
    margin-bottom: 18px;
}

.pcc-section-header {
    margin-bottom: 20px;
}

.pcc-section-header h3 {
    margin: 6px 0 0;
    font-size: 16px;
}

.pcc-action-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
}

.pcc-action-card {
    text-align: left;
    border: 1px solid #2b3643;
    background: #171e27;
    border-radius: 8px;
    color: #e6edf3;
    padding: 17px;
    cursor: pointer;
}

.pcc-action-card:hover {
    background: #1b2430;
    border-color: #3c4b5d;
}

.pcc-action-card span {
    display: block;
    font-size: 19px;
    margin-bottom: 12px;
}

.pcc-action-card strong {
    display: block;
    font-size: 13px;
}

.pcc-action-card small {
    display: block;
    color: #7e8b9b;
    margin-top: 5px;
    font-size: 11px;
}

.pcc-notice {
    display: flex;
    gap: 13px;
    padding: 17px;
    border: 1px solid #293544;
    background: #111821;
    border-radius: 8px;
}

.pcc-notice-icon {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    border: 1px solid #455466;
    display: flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 24px;
    color: #9da9b8;
}

.pcc-notice strong {
    font-size: 12px;
}

.pcc-notice p {
    margin: 5px 0 0;
    color: #7f8b9b;
    font-size: 12px;
    line-height: 1.5;
}

@media (max-width: 1100px) {
    .pcc-module-grid {
        grid-template-columns: 1fr 1fr;
    }

    .pcc-module-main {
        grid-column: 1 / -1;
    }

    .pcc-action-grid {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 800px) {
    .pcc-sidebar {
        width: 205px;
        flex-basis: 205px;
    }

    .pcc-topbar,
    .pcc-content {
        padding-left: 20px;
        padding-right: 20px;
    }
}

@media (max-width: 650px) {
    .pcc-sidebar {
        display: none;
    }

    .pcc-module-grid,
    .pcc-action-grid {
        grid-template-columns: 1fr;
    }

    .pcc-module-main {
        grid-column: auto;
    }

    .pcc-user-box {
        display: none;
    }
}
CSS

ok "CSS de módulos creado"

echo
echo "===== 5. AÑADIR CSS A LAS VISTAS ====="

for FILE in "$VIEW_DIR"/modules/*.php; do

    if ! grep -q "pccurico-modules.css" "$FILE"; then
        sed -i \
            's#<link rel="stylesheet" href="/assets/css/panel.css">#<link rel="stylesheet" href="/assets/css/panel.css">\n    <link rel="stylesheet" href="/assets/css/pccurico-modules.css">#' \
            "$FILE"
    fi

done

ok "CSS conectado"

echo
echo "===== 6. RUTAS ====="

python3 - "$ROUTES" <<'PY'
from pathlib import Path
import sys

path = Path(sys.argv[1])
text = path.read_text()

controller_import = "use Pccurico\\HostingPanel\\Controllers\\PanelModulesController;"

if controller_import not in text:
    marker = "use Pccurico\\HostingPanel\\Controllers\\UsersController;"
    if marker in text:
        text = text.replace(
            marker,
            marker + "\n" + controller_import
        )
    else:
        marker = "use Pccurico\\HostingPanel\\Core\\Router;"
        text = text.replace(
            marker,
            controller_import + "\n" + marker
        )

if "$panelModulesController = new PanelModulesController();" not in text:
    marker = "$usersController = new UsersController();"
    if marker in text:
        text = text.replace(
            marker,
            marker + "\n$panelModulesController = new PanelModulesController();"
        )
    else:
        marker = "$router = new Router();"
        text = text.replace(
            marker,
            marker + "\n\n$panelModulesController = new PanelModulesController();"
        )

routes = """
/*
 * Modulos principales del Hosting Panel
 */
$router->get('/domains', function () use ($panelModulesController): void {
    $panelModulesController->show('domains');
});

$router->get('/dns', function () use ($panelModulesController): void {
    $panelModulesController->show('dns');
});

$router->get('/apache', function () use ($panelModulesController): void {
    $panelModulesController->show('apache');
});

$router->get('/php', function () use ($panelModulesController): void {
    $panelModulesController->show('php');
});

$router->get('/mysql', function () use ($panelModulesController): void {
    $panelModulesController->show('mysql');
});

$router->get('/databases', function () use ($panelModulesController): void {
    $panelModulesController->show('databases');
});

$router->get('/mail', function () use ($panelModulesController): void {
    $panelModulesController->show('mail');
});

$router->get('/ssl', function () use ($panelModulesController): void {
    $panelModulesController->show('ssl');
});

$router->get('/backups', function () use ($panelModulesController): void {
    $panelModulesController->show('backups');
});

$router->get('/logs', function () use ($panelModulesController): void {
    $panelModulesController->show('logs');
});

$router->get('/settings', function () use ($panelModulesController): void {
    $panelModulesController->show('settings');
});

$router->get('/tools', function () use ($panelModulesController): void {
    $panelModulesController->show('tools');
});

$router->get('/audit', function () use ($panelModulesController): void {
    $panelModulesController->show('audit');
});

$router->get('/roles', function () use ($panelModulesController): void {
    $panelModulesController->show('roles');
});

$router->get('/permissions', function () use ($panelModulesController): void {
    $panelModulesController->show('permissions');
});

"""

if "Modulos principales del Hosting Panel" not in text:
    marker = "return $router;"
    if marker not in text:
        raise SystemExit("No se encontró return $router;")

    text = text.replace(
        marker,
        routes + "\n" + marker
    )

path.write_text(text)
PY

ok "Rutas de módulos añadidas"

echo
echo "===== 7. VALIDAR ROUTES ====="

php -l "$ROUTES"

echo
echo "===== 8. VALIDAR CONTROLADOR ====="

php -l "$CONTROLLER_DIR/PanelModulesController.php"

echo
echo "===== 9. VALIDAR VISTAS ====="

for FILE in "$VIEW_DIR"/modules/*.php; do
    php -l "$FILE" >/dev/null || fail "Error PHP en $FILE"
done

ok "Todas las vistas son sintácticamente válidas"

echo
echo "===== 10. CONTAR PANTALLAS ====="

COUNT=$(find "$VIEW_DIR/modules" -maxdepth 1 -type f -name '*.php' | wc -l)

echo "Pantallas creadas: $COUNT"

[[ "$COUNT" -eq 15 ]] || warn "Se esperaban 15 pantallas"

echo
echo "===== 11. MOSTRAR RUTAS NUEVAS ====="

grep -nE \
    "/domains|/dns|/apache|/php|/mysql|/databases|/mail|/ssl|/backups|/logs|/settings|/tools|/audit|/roles|/permissions" \
    "$ROUTES"

echo
echo "===== 12. TEST HTTP ====="

MODULES=(
    domains
    dns
    apache
    php
    mysql
    databases
    mail
    ssl
    backups
    logs
    settings
    tools
    audit
    roles
    permissions
)

for MODULE in "${MODULES[@]}"; do

    STATUS=$(curl -sS \
        -o /dev/null \
        -w "%{http_code}" \
        --max-time 10 \
        -H 'Host: hosting.local' \
        "http://127.0.0.1/$MODULE")

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
echo "===== 13. APACHE ====="

apache2ctl configtest

echo
echo "===== 14. SERVICIOS ====="

printf "Apache  : "
systemctl is-active apache2

printf "PHP-FPM : "
systemctl is-active php8.3-fpm

printf "MySQL   : "
systemctl is-active mysql

echo
echo "============================================================"
echo " PANTALLAS INSTALADAS"
echo "============================================================"
echo
echo "Hosting"
echo "  /sites"
echo "  /domains"
echo "  /dns"
echo "  /databases"
echo "  /mail"
echo
echo "Servidor"
echo "  /apache"
echo "  /php"
echo "  /mysql"
echo "  /logs"
echo
echo "Seguridad"
echo "  /ssl"
echo "  /backups"
echo "  /audit"
echo
echo "Usuarios"
echo "  /users"
echo "  /roles"
echo "  /permissions"
echo
echo "Sistema"
echo "  /settings"
echo "  /tools"
echo
echo "============================================================"
echo " IMPORTANTE"
echo "============================================================"
echo
echo "Esta etapa SOLO crea interfaz y rutas."
echo
echo "NO se modificó MySQL."
echo "NO se crearon tablas."
echo "NO se crearon permisos."
echo "NO se crearon roles."
echo "NO se insertaron datos."
echo "NO se modificó Cloudflare."
echo "NO se modificaron sitios existentes."
echo "NO se ejecutó git commit."
echo "NO se ejecutó git push."
echo
echo "Backup:"
echo "$BACKUP"
echo
echo "============================================================"
echo " FIN"
echo "============================================================"
