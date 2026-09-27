#!/usr/bin/env bash
set -euo pipefail

DNS_FILE="/etc/dnsmasq.d/hosting-pccurico.conf"
IP="192.168.2.250"

echo "=============================================="
echo " PCCURICO HOSTING PANEL - FIX DNS LOCAL"
echo "=============================================="

echo "[1/6] Backup..."

BACKUP="/root/pccurico-hosting-dns-fix-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP"

if [ -f "$DNS_FILE" ]; then
    cp -a "$DNS_FILE" "$BACKUP/"
fi

echo "[OK] Backup: $BACKUP"

echo "[2/6] Configurando hosting.local..."

cat > "$DNS_FILE" <<EOF
address=/hosting.local/$IP
address=/hosting.pccurico.cl/$IP
EOF

echo "[OK] DNS configurado."

echo "[3/6] Validando dnsmasq..."

dnsmasq --test

echo "[OK] Sintaxis correcta."

echo "[4/6] Recargando dnsmasq..."

systemctl reload dnsmasq

sleep 1

echo "[OK] dnsmasq recargado."

echo "[5/6] Probando resolución..."

echo
echo "--- hosting.local ---"
dig @127.0.0.1 hosting.local A +noall +answer || true

echo
echo "--- hosting.pccurico.cl ---"
dig @127.0.0.1 hosting.pccurico.cl A +noall +answer || true

echo
echo "[6/6] Probando HTTP directo..."

curl -sS --max-time 5 \
    -H "Host: hosting.local" \
    -o /dev/null \
    -w "HTTP: %{http_code}\n" \
    http://127.0.0.1/users

echo
echo "=============================================="
echo " DNS LOCAL CORREGIDO"
echo "=============================================="
echo "IP: $IP"
echo "Archivo: $DNS_FILE"
echo "Backup: $BACKUP"
echo
echo "No se modificó Apache."
echo "No se modificó Cloudflare."
echo "No se modificaron sitios."
