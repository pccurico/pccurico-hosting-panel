#!/usr/bin/env bash
set -Eeuo pipefail

PANEL="/var/www/pccurico-hosting-panel"
CSS="$PANEL/public/assets/css/panel.css"

[[ -f "$CSS" ]] || {
    echo "[ERROR] No existe $CSS"
    exit 1
}

if grep -q "PCCURICO-CREATE-SITE-VISIBILITY" "$CSS"; then
    echo "[OK] La mejora visual ya está instalada."
    exit 0
fi

BACKUP="/root/pccurico-hosting-panel-create-style-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP"

cp -a "$CSS" "$BACKUP/panel.css"

cat >> "$CSS" <<'CSS'

/* ============================================================
   PCCURICO-CREATE-SITE-VISIBILITY
   Mejora visual formulario Crear Sitio
   ============================================================ */

form label,
form .form-label {
    display: block;
    color: #e8edf5 !important;
    font-weight: 600 !important;
    font-size: 0.9rem;
    margin-bottom: 0.45rem;
}

form input[type="text"],
form input[type="url"],
form input[type="email"],
form input[type="password"],
form input[type="number"],
form input[type="search"],
form input[type="tel"],
form select,
form textarea {
    width: 100%;
    min-height: 44px;
    box-sizing: border-box;
    color: #f4f7fb !important;
    background: #151a23 !important;
    border: 1px solid #3b4656 !important;
    border-radius: 7px;
    padding: 10px 13px;
    font-size: 0.95rem;
    outline: none;
}

form textarea {
    min-height: 100px;
    resize: vertical;
}

form input::placeholder,
form textarea::placeholder {
    color: #8d98a8 !important;
    opacity: 1;
}

form input:hover,
form select:hover,
form textarea:hover {
    border-color: #596779 !important;
    background: #181e28 !important;
}

form input:focus,
form select:focus,
form textarea:focus {
    border-color: #6ea8fe !important;
    background: #171d27 !important;
    box-shadow: 0 0 0 3px rgba(110, 168, 254, 0.15) !important;
}

form select option {
    color: #f4f7fb;
    background: #151a23;
}

form small,
form .help-text,
form .form-text,
form .text-muted {
    color: #aeb8c6 !important;
}

form button,
form input[type="submit"],
form input[type="button"],
form .btn {
    min-height: 42px;
    border-radius: 7px;
    font-weight: 600;
    cursor: pointer;
}

.alert-danger,
.alert-error,
.error {
    color: #ffd7dc !important;
    background: #35191e !important;
    border: 1px solid #8e3b47 !important;
}

.alert-success,
.success {
    color: #d8f8e5 !important;
    background: #163224 !important;
    border: 1px solid #397957 !important;
}

.alert-warning,
.warning {
    color: #ffe9b0 !important;
    background: #352b16 !important;
    border: 1px solid #80682a !important;
}

.card,
.panel,
.form-card {
    color: #e8edf5;
}

.card h1,
.card h2,
.card h3,
.card h4,
.panel h1,
.panel h2,
.panel h3,
.panel h4,
.form-card h1,
.form-card h2,
.form-card h3,
.form-card h4 {
    color: #f4f7fb !important;
}

/* ============================================================
   Fin PCCURICO-CREATE-SITE-VISIBILITY
   ============================================================ */

CSS

chown root:www-data "$CSS"
chmod 0644 "$CSS"

echo "[OK] CSS aplicado."
echo "[OK] Backup: $BACKUP/panel.css"
