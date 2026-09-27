#!/usr/bin/env bash
set -euo pipefail

INTERFACE="eno2"
DNS="192.168.2.250"
BACKUP="/root/pccurico-resolved-backup-$(date +%Y%m%d-%H%M%S)"

echo "=============================================="
echo " PCCURICO HOSTING PANEL - SYSTEMD RESOLVED"
echo "=============================================="

echo "[1/6] Backup..."

mkdir -p "$BACKUP"

cp -a /etc/systemd/resolved.conf "$BACKUP/resolved.conf"

echo "[OK] Backup: $BACKUP"

echo "[2/6] Configurando systemd-resolved..."

cat > /etc/systemd/resolved.conf <<EOF
[Resolve]
DNS=$DNS
Domains=~.
DNSStubListener=yes
EOF

echo "[OK] resolved.conf configurado."

echo "[3/6] Reiniciando systemd-resolved..."

systemctl restart systemd-resolved

sleep 2

echo "[4/6] Configurando DNS de la interfaz..."

resolvectl dns "$INTERFACE" "$DNS"

echo "[OK] DNS $DNS asignado a $INTERFACE."

echo "[5/6] Limpiando caché DNS..."

resolvectl flush-caches

echo "[OK] Caché limpiada."

echo "[6/6] Verificación..."

echo
echo "--- resolvectl ---"
resolvectl status | head -50

echo
echo "--- hosting.local ---"
getent hosts hosting.local || true

echo
echo "--- dig ---"
dig hosting.local A +short || true

echo
echo "--- HTTP ---"
curl -I --max-time 5 http://hosting.local/users || true

echo
echo "=============================================="
echo " CONFIGURACIÓN TERMINADA"
echo "=============================================="
echo "Backup: $BACKUP"
