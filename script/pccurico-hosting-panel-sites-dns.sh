#!/usr/bin/env bash
set -Eeuo pipefail

PANEL_DIR="/var/www/pccurico-hosting-panel"
HELPER="/usr/local/sbin/pccurico-create-vhost"
DNS_FILE="/etc/dnsmasq.d/pccurico-hosting-sites.conf"
DNS_IP="192.168.2.250"
DNS_SUFFIX="home.pccontrolhub.arpa"

TIMESTAMP="$(date +%Y%m%d-%H%M%S)"
BACKUP_DIR="/root/pccurico-hosting-sites-dns-backup-${TIMESTAMP}"

fail() {
    echo "[ERROR] $*" >&2
    exit 1
}

ok() {
    echo "[OK] $*"
}

[[ "$EUID" -eq 0 ]] || fail "Ejecuta este script como root."

[[ -d "$PANEL_DIR" ]] || fail "No existe $PANEL_DIR"

echo "============================================================"
echo " PCCURICO HOSTING PANEL - APACHE + DNS"
echo "============================================================"

mkdir -p "$BACKUP_DIR"

echo
echo "[1/8] Backup"

[[ -f "$HELPER" ]] &&
    cp -a "$HELPER" "$BACKUP_DIR/pccurico-create-vhost"

[[ -f "$DNS_FILE" ]] &&
    cp -a "$DNS_FILE" "$BACKUP_DIR/pccurico-hosting-sites.conf"

[[ -f "$PANEL_DIR/app/Controllers/SitesController.php" ]] &&
    cp -a \
        "$PANEL_DIR/app/Controllers/SitesController.php" \
        "$BACKUP_DIR/SitesController.php"

ok "Backup: $BACKUP_DIR"

echo
echo "[2/8] Instalando helper"

cat > "$HELPER" <<'PHP'
#!/usr/bin/env php
<?php

declare(strict_types=1);

const DNS_FILE = '/etc/dnsmasq.d/pccurico-hosting-sites.conf';
const DNS_IP = '192.168.2.250';
const DNS_SUFFIX = 'home.pccontrolhub.arpa';

function fail(string $message): never
{
    fwrite(STDERR, "ERROR|{$message}\n");
    exit(1);
}

function runCommand(
    string $command,
    ?array &$output = null,
    ?int &$exitCode = null
): string {
    $lines = [];
    exec($command . ' 2>&1', $lines, $code);

    $output = $lines;
    $exitCode = $code;

    return trim(implode("\n", $lines));
}

function validDomain(string $domain): bool
{
    if ($domain === '' || strlen($domain) > 253) {
        return false;
    }

    return (bool)preg_match(
        '/^(?=.{1,253}$)(?!-)(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?\.)*[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?$/',
        $domain
    );
}

function validDocumentRoot(string $path): bool
{
    if (
        $path === '' ||
        !str_starts_with($path, '/var/www/') ||
        str_contains($path, '..')
    ) {
        return false;
    }

    return (bool)preg_match(
        '#^/var/www/[A-Za-z0-9._/-]+$#',
        $path
    );
}

function dnsNameForDomain(string $domain): string
{
    $domain = strtolower($domain);

    if (str_ends_with($domain, '.' . DNS_SUFFIX)) {
        return $domain;
    }

    if (str_ends_with($domain, '.pccurico.local')) {
        $prefix = substr(
            $domain,
            0,
            -strlen('.pccurico.local')
        );

        $prefix = trim($prefix, '.');

        if ($prefix !== '' && validDomain($prefix)) {
            return $prefix . '.' . DNS_SUFFIX;
        }
    }

    return $domain . '.' . DNS_SUFFIX;
}

function ensureDnsFile(): void
{
    if (!file_exists(DNS_FILE)) {
        file_put_contents(
            DNS_FILE,
            "# PCCURICO HOSTING PANEL - DNS LOCAL\n"
        );

        chmod(DNS_FILE, 0644);
    }
}

function backupDns(): string
{
    ensureDnsFile();

    $backup = DNS_FILE . '.backup-' . date('YmdHis');

    if (!copy(DNS_FILE, $backup)) {
        fail('No se pudo crear backup DNS.');
    }

    return $backup;
}

function restoreDns(string $backup): void
{
    if (!file_exists($backup)) {
        return;
    }

    copy($backup, DNS_FILE);
    chmod(DNS_FILE, 0644);

    $output = [];
    $code = 0;

    runCommand(
        '/usr/sbin/dnsmasq --test',
        $output,
        $code
    );

    if ($code === 0) {
        runCommand(
            '/usr/bin/systemctl reload dnsmasq',
            $output,
            $code
        );
    }
}

function addDns(string $hostname): void
{
    ensureDnsFile();

    $contents = file_get_contents(DNS_FILE);

    if ($contents === false) {
        fail('No se pudo leer DNS_FILE.');
    }

    $lines = preg_split('/\R/', $contents);
    $result = [];
    $found = false;

    foreach ($lines as $line) {
        $trim = trim($line);

        if (
            preg_match(
                '#^address=/' .
                preg_quote($hostname, '#') .
                '/#i',
                $trim
            )
        ) {
            if (!$found) {
                $result[] =
                    'address=/' .
                    $hostname .
                    '/' .
                    DNS_IP;

                $found = true;
            }

            continue;
        }

        $result[] = $line;
    }

    if (!$found) {
        $result[] =
            'address=/' .
            $hostname .
            '/' .
            DNS_IP;
    }

    $output = implode("\n", $result);

    if (!str_ends_with($output, "\n")) {
        $output .= "\n";
    }

    file_put_contents(
        DNS_FILE,
        $output,
        LOCK_EX
    );

    chmod(DNS_FILE, 0644);
}

function removeApache(string $domain): void
{
    $output = [];
    $code = 0;

    runCommand(
        '/usr/sbin/a2dissite ' .
        escapeshellarg($domain . '.conf'),
        $output,
        $code
    );

    @unlink(
        '/etc/apache2/sites-available/' .
        $domain .
        '.conf'
    );

    runCommand(
        '/usr/bin/systemctl reload apache2',
        $output,
        $code
    );
}

function validateDns(string $hostname): void
{
    $output = [];
    $code = 0;

    runCommand(
        '/usr/sbin/dnsmasq --test',
        $output,
        $code
    );

    if ($code !== 0) {
        fail(
            'dnsmasq --test falló: ' .
            implode("\n", $output)
        );
    }

    runCommand(
        '/usr/bin/systemctl reload dnsmasq',
        $output,
        $code
    );

    if ($code !== 0) {
        fail(
            'No se pudo recargar dnsmasq: ' .
            implode("\n", $output)
        );
    }

    $output = [];
    $code = 0;

    runCommand(
        '/usr/bin/dig @' .
        DNS_IP .
        ' ' .
        escapeshellarg($hostname) .
        ' A +short',
        $output,
        $code
    );

    foreach ($output as $line) {
        if (trim($line) === DNS_IP) {
            return;
        }
    }

    fail(
        "DNS no resuelve {$hostname} hacia " .
        DNS_IP
    );
}

if (posix_geteuid() !== 0) {
    fail('El helper debe ejecutarse como root.');
}

if ($argc !== 5) {
    fail(
        'Uso: pccurico-create-vhost DOMAIN DOCUMENT_ROOT ALIASES PHP_VERSION'
    );
}

$domain = strtolower(trim($argv[1]));
$documentRoot = trim($argv[2]);
$aliasesRaw = trim($argv[3]);
$phpVersion = trim($argv[4]);

if (!validDomain($domain)) {
    fail('Dominio inválido.');
}

if (!validDocumentRoot($documentRoot)) {
    fail('DocumentRoot inválido.');
}

if (!in_array($phpVersion, ['8.2', '8.3'], true)) {
    fail('PHP permitido: 8.2 o 8.3.');
}

$configFile =
    '/etc/apache2/sites-available/' .
    $domain .
    '.conf';

if (file_exists($configFile)) {
    fail("El sitio ya existe: {$domain}");
}

$aliases = [];

if ($aliasesRaw !== '') {
    foreach (preg_split('/\s+/', $aliasesRaw) as $alias) {
        if ($alias !== '') {
            if (!validDomain($alias)) {
                fail("Alias inválido: {$alias}");
            }

            $aliases[] = strtolower($alias);
        }
    }
}

$dnsHostname = dnsNameForDomain($domain);

echo "DOMAIN|{$domain}\n";
echo "DOCUMENT_ROOT|{$documentRoot}\n";
echo "DNS|{$dnsHostname}\n";

if (!is_dir($documentRoot)) {
    if (!mkdir($documentRoot, 0755, true)) {
        fail('No se pudo crear DocumentRoot.');
    }
}

chown($documentRoot, 'www-data');
chgrp($documentRoot, 'www-data');

$index = $documentRoot . '/index.php';

if (!file_exists($index)) {
    file_put_contents(
        $index,
        "<?php\ndeclare(strict_types=1);\necho 'PCCURICO HOSTING PANEL - SITIO ACTIVO';\n"
    );

    chown($index, 'www-data');
    chgrp($index, 'www-data');
    chmod($index, 0644);
}

$aliasConfig = '';

foreach ($aliases as $alias) {
    $aliasConfig .=
        "    ServerAlias {$alias}\n";
}

$config = <<<APACHE
<VirtualHost *:80>
    ServerName {$domain}
{$aliasConfig}
    DocumentRoot {$documentRoot}

    <Directory {$documentRoot}>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    <FilesMatch \.php$>
        SetHandler "proxy:unix:/run/php/php{$phpVersion}-fpm.sock|fcgi://localhost/"
    </FilesMatch>

    ErrorLog \${APACHE_LOG_DIR}/{$domain}-error.log
    CustomLog \${APACHE_LOG_DIR}/{$domain}-access.log combined

    Header always set X-Content-Type-Options "nosniff"
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
</VirtualHost>
APACHE;

file_put_contents(
    $configFile,
    $config,
    LOCK_EX
);

chmod($configFile, 0644);

$output = [];
$code = 0;

runCommand(
    '/usr/sbin/apache2ctl configtest',
    $output,
    $code
);

if ($code !== 0) {
    @unlink($configFile);

    fail(
        'Apache configtest falló: ' .
        implode("\n", $output)
    );
}

runCommand(
    '/usr/sbin/a2ensite ' .
    escapeshellarg($domain . '.conf'),
    $output,
    $code
);

if ($code !== 0) {
    @unlink($configFile);

    fail(
        'No se pudo habilitar el sitio: ' .
        implode("\n", $output)
    );
}

runCommand(
    '/usr/bin/systemctl reload apache2',
    $output,
    $code
);

if ($code !== 0) {
    removeApache($domain);

    fail(
        'No se pudo recargar Apache: ' .
        implode("\n", $output)
    );
}

$dnsBackup = backupDns();

try {
    addDns($dnsHostname);
    validateDns($dnsHostname);

    @unlink($dnsBackup);

    echo "OK|{$domain}|{$documentRoot}|{$configFile}\n";
    echo "DNS_OK|{$dnsHostname}|" . DNS_IP . "\n";

} catch (Throwable $e) {

    restoreDns($dnsBackup);
    @unlink($dnsBackup);

    removeApache($domain);

    fail($e->getMessage());
}
PHP

chmod 0750 "$HELPER"
chown root:root "$HELPER"

ok "Helper instalado."

echo
echo "[3/8] Validando PHP"

php -l "$HELPER"

echo
echo "[4/8] Configurando sudoers"

SUDOERS="/etc/sudoers.d/pccurico-hosting-panel"

cat > "$SUDOERS" <<EOF
www-data ALL=(root) NOPASSWD: $HELPER
EOF

chmod 0440 "$SUDOERS"
chown root:root "$SUDOERS"

visudo -cf "$SUDOERS"

ok "Sudoers válido."

echo
echo "[5/8] Preparando dnsmasq"

mkdir -p /etc/dnsmasq.d

if [[ ! -f "$DNS_FILE" ]]; then
    cat > "$DNS_FILE" <<'EOF'
# PCCURICO HOSTING PANEL - DNS LOCAL
EOF
fi

chmod 0644 "$DNS_FILE"

dnsmasq --test

ok "dnsmasq válido."

echo
echo "[6/8] Validando Apache"

apache2ctl configtest

ok "Apache válido."

echo
echo "[7/8] Verificando servicios"

systemctl is-active --quiet apache2 ||
    fail "Apache no está activo."

systemctl is-active --quiet php8.3-fpm ||
    fail "PHP-FPM 8.3 no está activo."

systemctl is-active --quiet dnsmasq ||
    fail "dnsmasq no está activo."

ok "Apache activo."
ok "PHP-FPM activo."
ok "dnsmasq activo."

echo
echo "[8/8] Recargando servicios"

systemctl reload dnsmasq
systemctl reload apache2

ok "Servicios recargados."

echo
echo "============================================================"
echo " INSTALACIÓN COMPLETADA"
echo "============================================================"
echo
echo "Helper: $HELPER"
echo "DNS:    $DNS_FILE"
echo "Zona:   *.home.pccontrolhub.arpa"
echo "IP:     $DNS_IP"
echo "Backup: $BACKUP_DIR"
echo
echo "============================================================"
