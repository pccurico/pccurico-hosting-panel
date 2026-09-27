#!/usr/bin/env bash
set -euo pipefail

HOSTS="/etc/hosts"
IP="192.168.2.250"
NAMES="hosting.local hosting.pccurico.cl"

BACKUP="/root/pccurico-hosts-backup-$(date +%Y%m%d-%H%M%S)"

echo "=============================================="
echo " PCCURICO HOSTING PANEL - HOSTS LOCAL"
echo "=============================================="

echo "[1/5] Backup..."

mkdir -p "$BACKUP"
cp -a "$HOSTS" "$BACKUP/hosts"

echo "[OK] Backup: $BACKUP/hosts"

echo "[2/5] Eliminando entradas anteriores del panel..."

sed -i \
    -E '/[[:space:]]hosting\.local([[:space:]]|$)/d;
        /[[:space:]]hosting\.pccurico\.cl([[:space:]]|$)/d' \
    "$HOSTS"

echo "[OK] Entradas anteriores eliminadas."

echo "[3/5] Agregando resolución local..."

printf '%s %s\n' "$IP" "$NAMES" >> "$HOSTS"

echo "[OK] $IP -> $NAMES"

echo "[4/5] Verificando..."

echo
echo "--- getent hosting.local ---"
getent hosts hosting.local

echo
echo "--- getent hosting.pccurico.cl ---"
getent hosts hosting.pccurico.cl

echo
echo "--- curl ---"
curl -I --max-time 5 http://hosting.local/users

echo
echo "=============================================="
echo " RESOLUCIÓN LOCAL CORREGIDA"
echo "=============================================="
echo "Backup: $BACKUP"
echo
echo "No se modificó Apache."
echo "No se modificó dnsmasq."
echo "No se modificó Cloudflare."
