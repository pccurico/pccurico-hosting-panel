#!/usr/bin/env bash
set -Eeuo pipefail

PANEL="/var/www/pccurico-hosting-panel"
BASE="/var/www"
CONTROLLER="$PANEL/app/Controllers/SitesController.php"
VIEW="$PANEL/app/Views/sites/create.php"
ROUTES="$PANEL/routes/web.php"
CSS="$PANEL/public/assets/css/panel.css"

echo "============================================================"
echo " PCCURICO HOSTING PANEL - SITIOS / CREAR SITIO"
echo "============================================================"

[[ -d "$PANEL" ]] || {
    echo "[ERROR] No existe $PANEL"
    exit 1
}

for f in "$CONTROLLER" "$VIEW" "$ROUTES" "$CSS"; do
    [[ -f "$f" ]] || {
        echo "[ERROR] Falta: $f"
        exit 1
    }
done

BACKUP="/root/pccurico-hosting-panel-backup-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP"

echo "[1/7] Creando respaldo..."
cp -a "$CONTROLLER" "$BACKUP/"
cp -a "$VIEW" "$BACKUP/"
cp -a "$ROUTES" "$BACKUP/"
cp -a "$CSS" "$BACKUP/"

echo "[2/7] Verificando helper Apache..."

HELPER="/usr/local/sbin/pccurico-create-vhost"
SUDOERS="/etc/sudoers.d/pccurico-hosting-panel"

if [[ ! -x "$HELPER" ]]; then
    echo "[ERROR] No existe el helper ejecutable:"
    echo "        $HELPER"
    exit 1
fi

if [[ ! -f "$SUDOERS" ]]; then
    echo "[ERROR] No existe:"
    echo "        $SUDOERS"
    exit 1
fi

visudo -cf "$SUDOERS" >/dev/null

echo "[OK] Helper y sudoers válidos."

echo "[3/7] Verificando rutas del módulo..."

grep -qE '/sites/create' "$ROUTES" || {
    echo "[ERROR] No existe la ruta /sites/create."
    exit 1
}

grep -qE 'function[[:space:]]+create' "$CONTROLLER" || {
    echo "[ERROR] SitesController no contiene create()."
    exit 1
}

grep -qE '_csrf|Csrf' "$VIEW" || {
    echo "[ERROR] El formulario no contiene protección CSRF."
    exit 1
}

echo "[OK] Módulo crear sitio encontrado."

echo "[4/7] Validando PHP..."

php -l "$CONTROLLER"
php -l "$PANEL/public/index.php"

echo "[5/7] Corrigiendo permisos mínimos del panel..."

chown -R www-data:www-data "$PANEL/storage"

find "$PANEL/storage" -type d -exec chmod 775 {} \;
find "$PANEL/storage" -type f -exec chmod 664 {} \;

echo "[6/7] Validando Apache..."

apache2ctl configtest

echo "[7/7] Recargando servicios..."

systemctl reload php8.3-fpm
systemctl reload apache2

echo
echo "============================================================"
echo " INSTALACIÓN COMPLETADA"
echo "============================================================"
echo
echo "Panel:"
echo "  http://hosting.local"
echo
echo "Crear sitio:"
echo "  http://hosting.local/sites/create"
echo
echo "Backup:"
echo "  $BACKUP"
echo
echo "Helper:"
echo "  $HELPER"
echo
echo "Apache: $(systemctl is-active apache2)"
echo "PHP-FPM: $(systemctl is-active php8.3-fpm)"
echo
echo "============================================================"
