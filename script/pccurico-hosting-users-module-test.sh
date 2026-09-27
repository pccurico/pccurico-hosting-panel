#!/usr/bin/env bash

set -Eeuo pipefail

PROJECT="/var/www/pccurico-hosting-panel"
LOG_FILE="/var/log/pccurico-hosting-users-module-test.log"

touch "$LOG_FILE"

exec > >(tee -a "$LOG_FILE") 2>&1

echo
echo "============================================================"
echo " PCCURICO HOSTING PANEL"
echo " VALIDACION MODULO USUARIOS"
echo " SOLO LECTURA - SIN CAMBIOS EN BD"
echo "============================================================"
echo "Fecha    : $(date '+%Y-%m-%d %H:%M:%S')"
echo "Proyecto : $PROJECT"
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

echo "===== 1. ESTRUCTURA DEL PROYECTO ====="

for DIR in \
    app \
    app/Core \
    app/Controllers \
    app/Models \
    app/Views \
    app/Views/users \
    app/Middleware \
    config \
    database \
    public \
    public/assets \
    public/assets/css \
    routes \
    storage \
    script
do
    if [[ -d "$PROJECT/$DIR" ]]; then
        echo "[OK] $DIR"
    else
        warn "Falta directorio: $DIR"
    fi
done

echo
echo "===== 2. ARCHIVOS DEL MODULO ====="

FILES=(
    "$PROJECT/app/Models/User.php"
    "$PROJECT/app/Controllers/UsersController.php"
    "$PROJECT/app/Views/users/index.php"
    "$PROJECT/routes/web.php"
    "$PROJECT/app/Middleware/Csrf.php"
)

for FILE in "${FILES[@]}"; do

    if [[ -f "$FILE" ]]; then
        echo "[OK] $FILE"
    else
        warn "Falta: $FILE"
    fi

done

echo
echo "===== 3. VALIDACION PHP ====="

PHP_FILES=(
    "$PROJECT/app/Models/User.php"
    "$PROJECT/app/Controllers/UsersController.php"
    "$PROJECT/app/Views/users/index.php"
    "$PROJECT/routes/web.php"
    "$PROJECT/app/Middleware/Csrf.php"
)

for FILE in "${PHP_FILES[@]}"; do

    if [[ -f "$FILE" ]]; then

        echo
        echo ">>> $FILE"

        php -l "$FILE" \
            || fail "Error de sintaxis en $FILE"

    fi

done

ok "Archivos PHP válidos"

echo
echo "===== 4. RUTAS USUARIOS ====="

grep -nE \
    "UsersController|/users|users/save|users/toggle" \
    "$PROJECT/routes/web.php" \
    || fail "No se encontraron las rutas de usuarios"

ok "Rutas Users presentes"

echo
echo "===== 5. CONTROLLER ====="

grep -nE \
    "class UsersController|function index|function save|function toggle|requireAdmin" \
    "$PROJECT/app/Controllers/UsersController.php" \
    || fail "Controller Users incompleto"

ok "Controller Users encontrado"

echo
echo "===== 6. MODELO ====="

grep -nE \
    "class User|function all|function find|function create|function setActive|function setRole|function roles" \
    "$PROJECT/app/Models/User.php" \
    || fail "Modelo User incompleto"

ok "Modelo User encontrado"

echo
echo "===== 7. VISTA ====="

grep -nE \
    "<form|_csrf|users/save|users/toggle|role_id|password" \
    "$PROJECT/app/Views/users/index.php" \
    || fail "Vista Users incompleta"

ok "Vista Users encontrada"

echo
echo "===== 8. TEST HTTP ====="

HEADERS=$(curl -sS \
    -i \
    --max-time 10 \
    -H 'Host: hosting.local' \
    http://127.0.0.1/users \
    | head -30)

echo "$HEADERS"

if echo "$HEADERS" | grep -q "Location: /login"; then
    ok "/users está protegido por autenticación"
elif echo "$HEADERS" | grep -q "HTTP/1.1 200"; then
    ok "/users responde HTTP 200"
else
    warn "Respuesta HTTP inesperada"
fi

echo
echo "===== 9. TEST LOGIN ====="

LOGIN=$(curl -sS \
    -i \
    --max-time 10 \
    -H 'Host: hosting.local' \
    http://127.0.0.1/login \
    | head -30)

echo "$LOGIN"

if echo "$LOGIN" | grep -q "HTTP/1.1 200"; then
    ok "Login responde correctamente"
else
    warn "Login no devolvió HTTP 200"
fi

echo
echo "===== 10. APACHE ====="

apache2ctl configtest

echo
echo "===== 11. SERVICIOS ====="

printf "Apache    : "
systemctl is-active apache2

printf "PHP-FPM   : "
systemctl is-active php8.3-fpm

printf "MySQL     : "
systemctl is-active mysql

echo
echo "===== 12. BASE DE DATOS - SOLO LECTURA ====="

echo
echo "Las tablas existentes serán consultadas únicamente mediante"
echo "el usuario administrativo que solicite contraseña."
echo "Este script NO INSERTA, UPDATEA, DELETEA ni ALTERA nada."

echo
echo "Para revisar la estructura real posteriormente:"
echo
echo "  SHOW TABLES;"
echo "  SHOW CREATE TABLE permissions;"
echo "  SHOW CREATE TABLE role_permissions;"
echo
echo "La instalación definitiva de BD se hará al final del proyecto."

echo
echo "============================================================"
echo " VALIDACION TERMINADA"
echo "============================================================"
echo
echo "NO se modificó:"
echo "- MySQL"
echo "- usuarios"
echo "- roles"
echo "- permisos"
echo "- Apache"
echo "- DNS"
echo "- Cloudflare Tunnel"
echo
echo "NO se ejecutó:"
echo "- git commit"
echo "- git push"
echo
echo "Log:"
echo "$LOG_FILE"
echo
echo "============================================================"

