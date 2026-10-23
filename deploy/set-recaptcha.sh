#!/usr/bin/env bash
# Define RECAPTCHA_SITE_KEY y RECAPTCHA_SECRET_KEY en el wp-config.php del servidor.
# Las claves se piden por prompt (la secreta sin eco) o vienen del entorno; no quedan en el historial.
# Las claves deben estar registradas en https://www.google.com/recaptcha/admin para el dominio de producción.
#
# Uso (en el servidor, desde la carpeta deploy/):  sudo ./set-recaptcha.sh
set -euo pipefail

[ "$(id -u)" -eq 0 ] || { echo "Ejecutar con sudo." >&2; exit 1; }

CONFIG=/var/www/mitos/wp-config.php
PHP_VERSION="$(cat /etc/mitos/php-version)"

if [ -z "${RECAPTCHA_SITE_KEY:-}" ]; then read -rp "Site key: " RECAPTCHA_SITE_KEY; fi
if [ -z "${RECAPTCHA_SECRET_KEY:-}" ]; then read -rsp "Secret key: " RECAPTCHA_SECRET_KEY; echo; fi

for k in "$RECAPTCHA_SITE_KEY" "$RECAPTCHA_SECRET_KEY"; do
  [[ "$k" =~ ^[A-Za-z0-9_-]{20,}$ ]] || { echo "Clave con formato inválido." >&2; exit 1; }
done

backup="$CONFIG.bak-$(date +%Y%m%d-%H%M%S)"
cp -p "$CONFIG" "$backup"
export RECAPTCHA_SITE_KEY RECAPTCHA_SECRET_KEY CONFIG

python3 -I - <<'PY'
import os, re
path = os.environ['CONFIG']
s = open(path).read()
s = re.sub(r"^.*RECAPTCHA_(SITE|SECRET)_KEY.*\n", "", s, flags=re.M)
block = ("define( 'RECAPTCHA_SITE_KEY', '%s' );\n"
         "define( 'RECAPTCHA_SECRET_KEY', '%s' );\n"
         % (os.environ['RECAPTCHA_SITE_KEY'], os.environ['RECAPTCHA_SECRET_KEY']))
marker = "define( 'WP_DEBUG', false );\n"
assert marker in s, "No se encontró WP_DEBUG en wp-config.php"
open(path, 'w').write(s.replace(marker, marker + block, 1))
PY

if "php$PHP_VERSION" -l "$CONFIG" >/dev/null; then
  systemctl reload "php$PHP_VERSION-fpm"
  echo "reCAPTCHA configurado. Copia de seguridad: $backup"
else
  cp -p "$backup" "$CONFIG"
  echo "wp-config.php inválido; se restauró la copia de seguridad." >&2
  exit 1
fi
