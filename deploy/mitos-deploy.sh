#!/usr/bin/env bash
# Se instala en /usr/local/bin/mitos-deploy (lo hace provision.sh) y lo invoca
# GitHub Actions mediante AWS SSM Run Command (como root).
# Uso: mitos-deploy <commit-sha>
set -euo pipefail

SHA="${1:?Falta el SHA del commit}"
[[ "$SHA" =~ ^[0-9a-f]{40}$ ]] || { echo "SHA inválido: $SHA" >&2; exit 1; }

REPO=fishercom/ajinomoto-mitos
THEME=ajinomoto-mitos
DEST="/var/www/mitos/wp-content/themes/$THEME"
PHP_VERSION="$(cat /etc/mitos/php-version)"

tmp="$(mktemp -d)"
trap 'rm -rf "$tmp"' EXIT

echo "==> Descargando $REPO@$SHA"
curl -fsSL "https://codeload.github.com/$REPO/tar.gz/$SHA" | tar -xz -C "$tmp"
src="$tmp/$(basename "$REPO")-$SHA/wp-content/themes/$THEME"
[ -f "$src/style.css" ] || { echo "No se encontró el tema en el commit" >&2; exit 1; }

echo "==> Sincronizando tema"
rsync -rlt --delete --itemize-changes --chmod=D755,F644 \
  --exclude='.DS_Store' "$src/" "$DEST/"
chown -R www-data:www-data "$DEST"

echo "==> Recargando PHP-FPM"
systemctl reload "php$PHP_VERSION-fpm"

# Sin -L: WordPress redirige a WP_HOME (IP pública), que puede no ser alcanzable desde el propio servidor
code="$(curl -s -m 15 -o /dev/null -w '%{http_code}' http://127.0.0.1/)"
echo "HTTP local: $code"
[ "$code" -lt 500 ]
echo "Despliegue de $SHA completado"
