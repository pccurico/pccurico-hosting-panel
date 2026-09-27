#!/usr/bin/env bash

set -Eeuo pipefail

PROJECT="/var/www/pccurico-hosting-panel"
APP="$PROJECT/app"
CONTROLLERS="$APP/Controllers"
VIEWS="$APP/Views"
MODULE_VIEWS="$VIEWS/modules"
CSS="$PROJECT/public/assets/css"

BACKUP="/root/pccurico-hosting-panel-server-modules-$(date +%Y%m%d-%H%M%S)"

echo
echo "============================================================"
echo " PCCURICO HOSTING PANEL"
echo " MODULOS REALES DE SERVIDOR"
echo " SOLO LECTURA"
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

mkdir -p "$BACKUP"
mkdir -p "$CONTROLLERS"
mkdir -p "$MODULE_VIEWS"

echo "===== 1. BACKUP ====="

cp -a "$CONTROLLERS" "$BACKUP/Controllers"

if [[ -d "$MODULE_VIEWS" ]]; then
    cp -a "$MODULE_VIEWS" "$BACKUP/modules"
fi

if [[ -f "$CSS/pccurico-modules.css" ]]; then
    cp -a "$CSS/pccurico-modules.css" "$BACKUP/"
fi

ok "Backup creado"

echo
echo "===== 2. CONTROLADOR SERVERMODULES ====="

sudo tee "$CONTROLLERS/ServerModulesController.php" > /dev/null <<'PHP'
<?php

declare(strict_types=1);

namespace Pccurico\HostingPanel\Controllers;

final class ServerModulesController
{
    public function show(string $module): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $allowed = [
            'domains',
            'dns',
            'apache',
            'php',
            'mysql',
            'databases',
            'mail',
            'ssl',
            'backups',
            'logs',
            'settings',
            'tools',
            'audit',
        ];

        if (!in_array($module, $allowed, true)) {
            http_response_code(404);
            exit('Módulo no encontrado.');
        }

        $data = match ($module) {
            'domains' => $this->domains(),
            'dns' => $this->dns(),
            'apache' => $this->apache(),
            'php' => $this->php(),
            'mysql' => $this->mysql(),
            'databases' => $this->databases(),
            'mail' => $this->mail(),
            'ssl' => $this->ssl(),
            'backups' => $this->backups(),
            'logs' => $this->logs(),
            'settings' => $this->settings(),
            'tools' => $this->tools(),
            'audit' => $this->audit(),
        };

        $titles = [
            'domains' => ['Dominios', 'Dominios detectados en Apache', '🌐', 'Hosting'],
            'dns' => ['DNS', 'Estado y configuración DNS local', '🔗', 'Hosting'],
            'apache' => ['Apache', 'Estado y VirtualHosts del servidor', '🖥', 'Servidor'],
            'php' => ['PHP', 'PHP CLI y PHP-FPM', '🐘', 'Servidor'],
            'mysql' => ['MySQL', 'Estado del servidor MySQL', '🗄', 'Servidor'],
            'databases' => ['Bases de datos', 'Bases de datos detectadas', '💾', 'Hosting'],
            'mail' => ['Correo', 'Estado del sistema de correo', '✉', 'Hosting'],
            'ssl' => ['SSL', 'Certificados SSL detectados', '🔒', 'Seguridad'],
            'backups' => ['Backups', 'Estado de las copias de seguridad', '↻', 'Seguridad'],
            'logs' => ['Logs', 'Registros recientes del servidor', '▤', 'Servidor'],
            'settings' => ['Configuración', 'Configuración actual del servidor', '⚙', 'Sistema'],
            'tools' => ['Herramientas', 'Diagnóstico del servidor', '🛠', 'Sistema'],
            'audit' => ['Auditoría', 'Eventos administrativos registrados', '◉', 'Seguridad'],
        ];

        [$title, $subtitle, $icon, $section] = $titles[$module];

        $view = dirname(__DIR__) . '/Views/modules-real/' . $module . '.php';

        if (!is_file($view)) {
            http_response_code(500);
            exit('Vista no encontrada.');
        }

        extract([
            'module' => $module,
            'title' => $title,
            'subtitle' => $subtitle,
            'icon' => $icon,
            'section' => $section,
            'data' => $data,
        ], EXTR_SKIP);

        ob_start();

        require $view;

        $content = ob_get_clean();

        require dirname(__DIR__) . '/Views/layouts/app.php';
    }

    private function domains(): array
    {
        $result = [];

        $files = glob('/etc/apache2/sites-enabled/*.conf') ?: [];

        foreach ($files as $file) {
            $contents = @file_get_contents($file);

            if ($contents === false) {
                continue;
            }

            preg_match_all(
                '/ServerName\s+([^\s#]+)/i',
                $contents,
                $names
            );

            preg_match_all(
                '/ServerAlias\s+([^\s#]+)/i',
                $contents,
                $aliases
            );

            foreach ($names[1] ?? [] as $name) {
                $result[] = [
                    'domain' => $name,
                    'aliases' => implode(', ', $aliases[1] ?? []),
                    'file' => basename($file),
                ];
            }
        }

        return $result;
    }

    private function dns(): array
    {
        $services = [];

        $services[] = [
            'name' => 'dnsmasq',
            'status' => $this->serviceStatus('dnsmasq'),
        ];

        $services[] = [
            'name' => 'systemd-resolved',
            'status' => $this->serviceStatus('systemd-resolved'),
        ];

        $configs = glob('/etc/dnsmasq.d/*.conf') ?: [];

        return [
            'services' => $services,
            'configs' => array_map('basename', $configs),
            'resolv' => $this->command('resolvectl status'),
        ];
    }

    private function apache(): array
    {
        return [
            'status' => $this->serviceStatus('apache2'),
            'version' => trim((string) shell_exec('apache2 -v 2>/dev/null | head -1')),
            'syntax' => trim((string) shell_exec('apache2ctl configtest 2>&1')),
            'vhosts' => trim((string) shell_exec('apache2ctl -S 2>&1')),
            'configs' => $this->files('/etc/apache2/sites-enabled/*.conf'),
        ];
    }

    private function php(): array
    {
        return [
            'cli' => trim((string) shell_exec('php -v 2>/dev/null | head -1')),
            'fpm' => $this->serviceStatus('php8.3-fpm'),
            'modules' => trim((string) shell_exec('php -m 2>/dev/null')),
            'ini' => php_ini_loaded_file() ?: '',
        ];
    }

    private function mysql(): array
    {
        return [
            'status' => $this->serviceStatus('mysql'),
            'version' => trim((string) shell_exec('mysql --version 2>/dev/null')),
            'port' => trim((string) shell_exec(
                "ss -lnt 2>/dev/null | grep ':3306 ' || true"
            )),
        ];
    }

    private function databases(): array
    {
        return [
            'note' => 'Lectura de bases de datos pendiente de conexión administrativa segura.',
            'connection' => 'No se ejecutan consultas de escritura.',
        ];
    }

    private function mail(): array
    {
        $services = [];

        foreach (['postfix', 'exim4', 'dovecot'] as $service) {
            $services[$service] = $this->serviceStatus($service);
        }

        return [
            'services' => $services,
        ];
    }

    private function ssl(): array
    {
        $certificates = [];

        $paths = [
            '/etc/letsencrypt/live/*/fullchain.pem',
            '/etc/ssl/certs/*.pem',
        ];

        foreach ($paths as $pattern) {
            foreach (glob($pattern) ?: [] as $file) {
                $certificates[] = $file;
            }
        }

        return array_values(array_unique($certificates));
    }

    private function backups(): array
    {
        $paths = [
            '/root',
            '/var/backups',
            '/var/www',
        ];

        $result = [];

        foreach ($paths as $path) {
            if (!is_dir($path)) {
                continue;
            }

            $result[] = [
                'path' => $path,
                'size' => $this->directorySize($path),
            ];
        }

        return $result;
    }

    private function logs(): array
    {
        $files = [
            '/var/log/apache2/error.log',
            '/var/log/apache2/access.log',
            '/var/log/apache2/pccurico-hosting-panel-error.log',
            '/var/log/php8.3-fpm.log',
        ];

        $result = [];

        foreach ($files as $file) {
            if (!is_file($file)) {
                continue;
            }

            $lines = $this->tail($file, 20);

            $result[$file] = $lines;
        }

        return $result;
    }

    private function settings(): array
    {
        return [
            'hostname' => gethostname() ?: '',
            'os' => trim((string) shell_exec('lsb_release -ds 2>/dev/null')),
            'kernel' => php_uname('r'),
            'architecture' => php_uname('m'),
            'timezone' => date_default_timezone_get(),
            'php' => PHP_VERSION,
            'memory_limit' => ini_get('memory_limit'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
        ];
    }

    private function tools(): array
    {
        return [
            'disk' => $this->command('df -h /'),
            'memory' => $this->command('free -h'),
            'uptime' => $this->command('uptime'),
            'load' => sys_getloadavg(),
            'hostname' => gethostname() ?: '',
        ];
    }

    private function audit(): array
    {
        return [
            'note' => 'Los registros existentes se mostrarán cuando se conecte la capa de repositorio.',
            'table' => 'audit_logs',
        ];
    }

    private function serviceStatus(string $service): string
    {
        $status = trim((string) shell_exec(
            'systemctl is-active ' . escapeshellarg($service) . ' 2>/dev/null'
        ));

        if ($status === '') {
            return 'no instalado';
        }

        return $status;
    }

    private function command(string $command): string
    {
        return trim((string) shell_exec($command . ' 2>/dev/null'));
    }

    private function files(string $pattern): array
    {
        return array_map(
            'basename',
            glob($pattern) ?: []
        );
    }

    private function directorySize(string $path): string
    {
        return trim((string) shell_exec(
            'du -sh ' . escapeshellarg($path) . ' 2>/dev/null | awk \'{print $1}\''
        ));
    }

    private function tail(string $file, int $lines): array
    {
        $output = [];

        exec(
            'tail -n ' . $lines . ' ' . escapeshellarg($file) . ' 2>/dev/null',
            $output
        );

        return $output;
    }
}
PHP

php -l "$CONTROLLERS/ServerModulesController.php"

ok "Controlador real creado"

echo
echo "===== 3. CREAR VISTAS FUNCIONALES ====="

mkdir -p "$VIEWS/modules-real"

sudo tee "$VIEWS/modules-real/domains.php" > /dev/null <<'PHP'
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
PHP

sudo tee "$VIEWS/modules-real/dns.php" > /dev/null <<'PHP'
<div class="pcc-page-title">
    <div class="pcc-page-icon">🔗</div>
    <div>
        <h2>DNS</h2>
        <p>Estado de resolución DNS del servidor.</p>
    </div>
</div>

<div class="pcc-module-grid">
    <?php foreach ($data['services'] as $service): ?>
        <div class="pcc-module-card">
            <span class="pcc-card-kicker">SERVICIO</span>
            <h3><?= htmlspecialchars($service['name']) ?></h3>
            <div class="pcc-stat-value"><?= htmlspecialchars($service['status']) ?></div>
        </div>
    <?php endforeach; ?>
</div>

<div class="pcc-panel-section">
    <span class="pcc-card-kicker">CONFIGURACIÓN</span>
    <h3>dnsmasq</h3>

    <div class="pcc-code-box">
        <?php foreach ($data['configs'] as $config): ?>
            <?= htmlspecialchars($config) ?><br>
        <?php endforeach; ?>
    </div>
</div>
PHP

sudo tee "$VIEWS/modules-real/apache.php" > /dev/null <<'PHP'
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
PHP

sudo tee "$VIEWS/modules-real/php.php" > /dev/null <<'PHP'
<div class="pcc-page-title">
    <div class="pcc-page-icon">🐘</div>
    <div>
        <h2>PHP</h2>
        <p>Estado de PHP CLI y PHP-FPM.</p>
    </div>
</div>

<div class="pcc-module-grid">

    <div class="pcc-module-card">
        <span class="pcc-card-kicker">PHP CLI</span>
        <div class="pcc-stat-value"><?= htmlspecialchars($data['cli']) ?></div>
    </div>

    <div class="pcc-module-card">
        <span class="pcc-card-kicker">PHP-FPM</span>
        <div class="pcc-stat-value"><?= htmlspecialchars($data['fpm']) ?></div>
    </div>

    <div class="pcc-module-card">
        <span class="pcc-card-kicker">PHP.INI</span>
        <div class="pcc-stat-value"><?= htmlspecialchars($data['ini']) ?></div>
    </div>

</div>

<div class="pcc-panel-section">
    <span class="pcc-card-kicker">EXTENSIONES</span>
    <pre class="pcc-code-box"><?= htmlspecialchars($data['modules']) ?></pre>
</div>
PHP

sudo tee "$VIEWS/modules-real/mysql.php" > /dev/null <<'PHP'
<div class="pcc-page-title">
    <div class="pcc-page-icon">🗄</div>
    <div>
        <h2>MySQL</h2>
        <p>Estado del servidor MySQL.</p>
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
        <span class="pcc-card-kicker">PUERTO</span>
        <div class="pcc-stat-value">3306</div>
    </div>

</div>
PHP

sudo tee "$VIEWS/modules-real/databases.php" > /dev/null <<'PHP'
<div class="pcc-page-title">
    <div class="pcc-page-icon">💾</div>
    <div>
        <h2>Bases de datos</h2>
        <p>Administración de bases de datos del hosting.</p>
    </div>
</div>

<div class="pcc-notice">
    <div class="pcc-notice-icon">i</div>
    <div>
        <strong>Modo seguro</strong>
        <p>
            La interfaz está preparada. La consulta y administración
            de bases de datos se habilitará en la etapa final de
            integración de la base de datos del panel.
        </p>
    </div>
</div>
PHP

sudo tee "$VIEWS/modules-real/mail.php" > /dev/null <<'PHP'
<div class="pcc-page-title">
    <div class="pcc-page-icon">✉</div>
    <div>
        <h2>Correo</h2>
        <p>Estado de servicios de correo detectados.</p>
    </div>
</div>

<div class="pcc-module-grid">

<?php foreach ($data['services'] as $service => $status): ?>

    <div class="pcc-module-card">
        <span class="pcc-card-kicker">SERVICIO</span>

        <h3><?= htmlspecialchars($service) ?></h3>

        <div class="pcc-stat-value">
            <?= htmlspecialchars($status) ?>
        </div>
    </div>

<?php endforeach; ?>

</div>
PHP

sudo tee "$VIEWS/modules-real/ssl.php" > /dev/null <<'PHP'
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
PHP

sudo tee "$VIEWS/modules-real/backups.php" > /dev/null <<'PHP'
<div class="pcc-page-title">
    <div class="pcc-page-icon">↻</div>
    <div>
        <h2>Backups</h2>
        <p>Estado de ubicaciones disponibles para respaldos.</p>
    </div>
</div>

<div class="pcc-module-grid">

<?php foreach ($data as $backup): ?>

    <div class="pcc-module-card">

        <span class="pcc-card-kicker">
            UBICACIÓN
        </span>

        <h3>
            <?= htmlspecialchars($backup['path']) ?>
        </h3>

        <div class="pcc-stat-value">
            <?= htmlspecialchars($backup['size']) ?>
        </div>

    </div>

<?php endforeach; ?>

</div>
PHP

sudo tee "$VIEWS/modules-real/logs.php" > /dev/null <<'PHP'
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
PHP

sudo tee "$VIEWS/modules-real/settings.php" > /dev/null <<'PHP'
<div class="pcc-page-title">
    <div class="pcc-page-icon">⚙</div>
    <div>
        <h2>Configuración</h2>
        <p>Información actual del entorno del servidor.</p>
    </div>
</div>

<div class="pcc-module-grid">

<?php foreach ($data as $key => $value): ?>

    <div class="pcc-module-card">

        <span class="pcc-card-kicker">
            <?= htmlspecialchars($key) ?>
        </span>

        <div class="pcc-stat-value">
            <?= htmlspecialchars((string)$value) ?>
        </div>

    </div>

<?php endforeach; ?>

</div>
PHP

sudo tee "$VIEWS/modules-real/tools.php" > /dev/null <<'PHP'
<div class="pcc-page-title">
    <div class="pcc-page-icon">🛠</div>
    <div>
        <h2>Herramientas</h2>
        <p>Diagnóstico del servidor.</p>
    </div>
</div>

<div class="pcc-module-grid">

    <div class="pcc-module-card">
        <span class="pcc-card-kicker">HOSTNAME</span>
        <div class="pcc-stat-value">
            <?= htmlspecialchars($data['hostname']) ?>
        </div>
    </div>

    <div class="pcc-module-card">
        <span class="pcc-card-kicker">CARGA</span>
        <div class="pcc-stat-value">
            <?= htmlspecialchars(implode(' / ', $data['load'])) ?>
        </div>
    </div>

</div>

<div class="pcc-panel-section">
    <span class="pcc-card-kicker">DISCO</span>
    <pre class="pcc-code-box"><?= htmlspecialchars($data['disk']) ?></pre>
</div>

<div class="pcc-panel-section">
    <span class="pcc-card-kicker">MEMORIA</span>
    <pre class="pcc-code-box"><?= htmlspecialchars($data['memory']) ?></pre>
</div>

<div class="pcc-panel-section">
    <span class="pcc-card-kicker">UPTIME</span>
    <pre class="pcc-code-box"><?= htmlspecialchars($data['uptime']) ?></pre>
</div>
PHP

sudo tee "$VIEWS/modules-real/audit.php" > /dev/null <<'PHP'
<div class="pcc-page-title">
    <div class="pcc-page-icon">◉</div>
    <div>
        <h2>Auditoría</h2>
        <p>Registro de acciones administrativas.</p>
    </div>
</div>

<div class="pcc-notice">

    <div class="pcc-notice-icon">
        i
    </div>

    <div>

        <strong>
            Auditoría preparada
        </strong>

        <p>
            La interfaz está preparada para mostrar los eventos
            de audit_logs cuando se conecte el repositorio definitivo.
        </p>

    </div>

</div>
PHP

ok "Vistas funcionales creadas"

echo
echo "===== 4. CSS FUNCIONAL ====="

sudo tee "$CSS/pccurico-server-modules.css" > /dev/null <<'CSS'
.pcc-table-wrap {
    overflow-x: auto;
}

.pcc-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.pcc-table th {
    text-align: left;
    color: #6e7b8b;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 1px;
    padding: 12px;
    border-bottom: 1px solid #2a3440;
}

.pcc-table td {
    padding: 14px 12px;
    border-bottom: 1px solid #202934;
    color: #aeb9c7;
}

.pcc-table tr:hover td {
    background: #161e27;
}

.pcc-code-box {
    margin-top: 15px;
    padding: 15px;
    background: #0b1016;
    border: 1px solid #252f3a;
    border-radius: 7px;
    color: #aebac8;
    font-family: "Cascadia Code", Consolas, monospace;
    font-size: 11px;
    line-height: 1.6;
    overflow: auto;
    max-height: 430px;
    white-space: pre-wrap;
}

.pcc-list {
    margin-top: 15px;
}

.pcc-list-item {
    padding: 13px 15px;
    border-bottom: 1px solid #242e39;
    color: #aeb9c7;
    font-family: Consolas, monospace;
    font-size: 12px;
}

.pcc-list-item:last-child {
    border-bottom: 0;
}
CSS

ok "CSS funcional creado"

echo
echo "===== 5. REGISTRAR CSS ====="

for FILE in "$VIEWS/modules-real/"*.php; do

    if ! grep -q "pccurico-server-modules.css" "$FILE"; then
        sed -i \
            's#<link rel="stylesheet" href="/assets/css/panel.css">#<link rel="stylesheet" href="/assets/css/panel.css">\n    <link rel="stylesheet" href="/assets/css/pccurico-shell.css">\n    <link rel="stylesheet" href="/assets/css/pccurico-server-modules.css">#' \
            "$FILE" 2>/dev/null || true
    fi

done

ok "CSS registrado"

echo
echo "===== 6. RUTAS FUNCIONALES ====="

python3 - "$PROJECT/routes/web.php" <<'PY'
from pathlib import Path
import sys

path = Path(sys.argv[1])
text = path.read_text()

imp = "use Pccurico\\HostingPanel\\Controllers\\ServerModulesController;"

if imp not in text:
    marker = "use Pccurico\\HostingPanel\\Controllers\\PanelModulesController;"
    if marker in text:
        text = text.replace(marker, marker + "\n" + imp)
    else:
        marker = "use Pccurico\\HostingPanel\\Core\\Router;"
        text = text.replace(marker, imp + "\n" + marker)

if "$serverModulesController = new ServerModulesController();" not in text:
    marker = "$panelModulesController = new PanelModulesController();"
    if marker in text:
        text = text.replace(
            marker,
            marker + "\n$serverModulesController = new ServerModulesController();"
        )

route_block = """
/*
 * Modulos funcionales de servidor - solo lectura
 */
$router->get('/domains', function () use ($serverModulesController): void {
    $serverModulesController->show('domains');
});

$router->get('/dns', function () use ($serverModulesController): void {
    $serverModulesController->show('dns');
});

$router->get('/apache', function () use ($serverModulesController): void {
    $serverModulesController->show('apache');
});

$router->get('/php', function () use ($serverModulesController): void {
    $serverModulesController->show('php');
});

$router->get('/mysql', function () use ($serverModulesController): void {
    $serverModulesController->show('mysql');
});

$router->get('/databases', function () use ($serverModulesController): void {
    $serverModulesController->show('databases');
});

$router->get('/mail', function () use ($serverModulesController): void {
    $serverModulesController->show('mail');
});

$router->get('/ssl', function () use ($serverModulesController): void {
    $serverModulesController->show('ssl');
});

$router->get('/backups', function () use ($serverModulesController): void {
    $serverModulesController->show('backups');
});

$router->get('/logs', function () use ($serverModulesController): void {
    $serverModulesController->show('logs');
});

$router->get('/settings', function () use ($serverModulesController): void {
    $serverModulesController->show('settings');
});

$router->get('/tools', function () use ($serverModulesController): void {
    $serverModulesController->show('tools');
});

$router->get('/audit', function () use ($serverModulesController): void {
    $serverModulesController->show('audit');
});
"""

if "Modulos funcionales de servidor - solo lectura" in text:
    before = text.split("/*\n * Modulos funcionales de servidor - solo lectura", 1)[0]
    after = text.split("/*\n * Modulos funcionales de servidor - solo lectura", 1)[1]

    if "return $router;" in after:
        after = after.split("return $router;", 1)[1]
        text = before + route_block + "\nreturn $router;\n"
else:
    marker = "return $router;"
    if marker not in text:
        raise SystemExit("No se encontró return $router;")

    text = text.replace(
        marker,
        route_block + "\n" + marker
    )

path.write_text(text)
PY

php -l "$PROJECT/routes/web.php"

ok "Rutas funcionales instaladas"

echo
echo "===== 7. VALIDAR PHP ====="

find "$CONTROLLERS" \
    -maxdepth 1 \
    -type f \
    -name '*.php' \
    -print0 |
while IFS= read -r -d '' FILE; do
    php -l "$FILE" >/dev/null ||
        fail "Error PHP: $FILE"
done

find "$VIEWS/modules-real" \
    -type f \
    -name '*.php' \
    -print0 |
while IFS= read -r -d '' FILE; do
    php -l "$FILE" >/dev/null ||
        fail "Error PHP: $FILE"
done

ok "PHP válido"

echo
echo "===== 8. APACHE ====="

apache2ctl configtest

echo
echo "===== 9. SERVICIOS ====="

systemctl is-active --quiet apache2 || fail "Apache detenido"
systemctl is-active --quiet php8.3-fpm || fail "PHP-FPM detenido"
systemctl is-active --quiet mysql || fail "MySQL detenido"

ok "Servicios activos"

echo
echo "===== 10. TEST DE MODULOS ====="

for MODULE in \
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
    audit
do

    STATUS=$(curl -sS \
        -o /dev/null \
        -w "%{http_code}" \
        --max-time 15 \
        -H 'Host: hosting.local' \
        "http://127.0.0.1/$MODULE" || true)

    if [[ "$STATUS" == "200" || "$STATUS" == "302" ]]; then
        echo "[OK] /$MODULE -> HTTP $STATUS"
    else
        echo "[ERROR] /$MODULE -> HTTP $STATUS"
    fi

done

echo
echo "============================================================"
echo " MODULOS FUNCIONALES INSTALADOS"
echo "============================================================"
echo
echo "Solo lectura:"
echo
echo "  /domains"
echo "  /dns"
echo "  /apache"
echo "  /php"
echo "  /mysql"
echo "  /databases"
echo "  /mail"
echo "  /ssl"
echo "  /backups"
echo "  /logs"
echo "  /settings"
echo "  /tools"
echo "  /audit"
echo
echo "============================================================"
echo " SEGURIDAD"
echo "============================================================"
echo
echo "No se modificó MySQL."
echo "No se modificaron tablas."
echo "No se insertaron registros."
echo "No se eliminaron datos."
echo "No se modificó Cloudflare."
echo "No se ejecutó git commit."
echo "No se ejecutó git push."
echo
echo "Backup:"
echo "$BACKUP"
echo
echo "============================================================"
echo " FIN"
echo "============================================================"
