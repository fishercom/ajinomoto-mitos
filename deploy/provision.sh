#!/usr/bin/env bash
# Prepara una instancia Ubuntu 24.04 limpia para servir el sitio (nginx + PHP-FPM + WordPress + RDS).
# Es idempotente: puede ejecutarse varias veces sin pisar wp-config.php ni uploads.
#
# Uso (en el servidor, desde la carpeta deploy/):
#   sudo SITE_URL=http://13.218.214.215 DB_HOST=... DB_NAME=... DB_USER=... ./provision.sh
# La contraseña de la base se solicita por prompt (o con DB_PASSWORD en el entorno).
# Opcional: PHP_VERSION (por defecto 8.3, de Ubuntu; otras versiones usan el PPA ondrej/php) y PLUGINS.
set -euo pipefail

[ "$(id -u)" -eq 0 ] || { echo "Ejecutar con sudo." >&2; exit 1; }

: "${SITE_URL:?Falta SITE_URL (ej. http://13.218.214.215)}"
: "${DB_HOST:?Falta DB_HOST}"
: "${DB_NAME:?Falta DB_NAME}"
: "${DB_USER:?Falta DB_USER}"
if [ -z "${DB_PASSWORD:-}" ]; then
  read -rsp "Contraseña de la base de datos: " DB_PASSWORD; echo
fi

PHP_VERSION="${PHP_VERSION:-8.3}"
WEBROOT=/var/www/mitos
THEME=ajinomoto-mitos
PLUGINS="${PLUGINS:-advanced-custom-fields custom-post-type-ui wp-mail-smtp wpforms-lite akismet}"
RDS_CA=/etc/ssl/certs/rds-global-bundle.pem
HERE="$(cd "$(dirname "$0")" && pwd)"

echo "==> Paquetes"
export DEBIAN_FRONTEND=noninteractive
apt-get update -y
apt-get install -y software-properties-common
# Ubuntu 24.04 solo trae PHP 8.3; otras versiones vienen del PPA de Ondřej Surý
if [ "$PHP_VERSION" != "8.3" ]; then
  add-apt-repository -y ppa:ondrej/php
  apt-get update -y
fi
apt-get install -y nginx mysql-client-core-8.0 unzip curl rsync \
  "php$PHP_VERSION-fpm" "php$PHP_VERSION-cli" "php$PHP_VERSION-mysql" "php$PHP_VERSION-curl" \
  "php$PHP_VERSION-gd" "php$PHP_VERSION-mbstring" "php$PHP_VERSION-xml" "php$PHP_VERSION-zip" \
  "php$PHP_VERSION-intl" "php$PHP_VERSION-imagick"

echo "==> Ajustes de PHP $PHP_VERSION"
cat > "/etc/php/$PHP_VERSION/fpm/conf.d/99-mitos.ini" <<'EOF'
upload_max_filesize = 64M
post_max_size = 64M
memory_limit = 256M
max_execution_time = 60
EOF

# Versión activa, usada por mitos-deploy para recargar el FPM correcto
mkdir -p /etc/mitos
echo "$PHP_VERSION" > /etc/mitos/php-version

# Desactivar otras versiones de PHP-FPM instaladas (p. ej. 8.3) para que no compitan
for svc in $(systemctl list-unit-files --no-legend 'php*-fpm.service' | awk '{print $1}'); do
  [ "$svc" = "php$PHP_VERSION-fpm.service" ] || systemctl disable --now "$svc" || true
done

echo "==> Verificando conexión a RDS (TLS)"
curl -fsSL -o "$RDS_CA" https://truststore.pki.rds.amazonaws.com/global/global-bundle.pem
MYSQL_PWD="$DB_PASSWORD" mysql --host="$DB_HOST" --user="$DB_USER" \
  --ssl-mode=VERIFY_CA --ssl-ca="$RDS_CA" "$DB_NAME" -e 'SELECT 1' >/dev/null
echo "    Conexión OK"

echo "==> WordPress core"
mkdir -p "$WEBROOT"
if [ ! -f "$WEBROOT/wp-settings.php" ]; then
  tmp="$(mktemp -d)"
  curl -fsSL https://wordpress.org/latest.tar.gz | tar -xz -C "$tmp"
  rsync -a --exclude=wp-content/themes/twenty* "$tmp/wordpress/" "$WEBROOT/"
  rm -rf "$tmp"
else
  echo "    Ya instalado, se omite"
fi

echo "==> Plugins"
for slug in $PLUGINS; do
  if [ ! -d "$WEBROOT/wp-content/plugins/$slug" ]; then
    tmp="$(mktemp -d)"
    curl -fsSL -o "$tmp/p.zip" "https://downloads.wordpress.org/plugin/$slug.latest-stable.zip"
    unzip -q "$tmp/p.zip" -d "$WEBROOT/wp-content/plugins/"
    rm -rf "$tmp"
    echo "    + $slug"
  fi
done

echo "==> wp-config.php"
if [ ! -f "$WEBROOT/wp-config.php" ]; then
  salts="$(curl -fsSL https://api.wordpress.org/secret-key/1.1/salt/)"
  php_str() { "php$PHP_VERSION" -r 'echo var_export($argv[1], true);' -- "$1"; }
  cat > "$WEBROOT/wp-config.php" <<EOF
<?php
define( 'DB_NAME', $(php_str "$DB_NAME") );
define( 'DB_USER', $(php_str "$DB_USER") );
define( 'DB_PASSWORD', $(php_str "$DB_PASSWORD") );
define( 'DB_HOST', $(php_str "$DB_HOST") );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );

// Conexión cifrada a RDS
define( 'MYSQL_CLIENT_FLAGS', MYSQLI_CLIENT_SSL );

$salts

\$table_prefix = 'wp_';

define( 'WP_HOME', $(php_str "$SITE_URL") );
define( 'WP_SITEURL', $(php_str "$SITE_URL") );
define( 'WP_DEBUG', false );
define( 'DISALLOW_FILE_EDIT', true );
define( 'FS_METHOD', 'direct' );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}
require_once ABSPATH . 'wp-settings.php';
EOF
  echo "    Creado"
else
  echo "    Ya existe, no se modifica"
fi

echo "==> Permisos"
mkdir -p "$WEBROOT/wp-content/uploads" "$WEBROOT/wp-content/themes/$THEME"
chown -R www-data:www-data "$WEBROOT"
chown root:www-data "$WEBROOT/wp-config.php"
chmod 640 "$WEBROOT/wp-config.php"
find "$WEBROOT" -type d -exec chmod 755 {} +
find "$WEBROOT" -type f ! -name wp-config.php -exec chmod 644 {} +

echo "==> Script de despliegue (invocado por SSM desde GitHub Actions)"
install -m 750 -o root -g root "$HERE/mitos-deploy.sh" /usr/local/bin/mitos-deploy

echo "==> nginx"
sed "s/@PHP_VERSION@/$PHP_VERSION/g" "$HERE/nginx-mitos.conf" > /etc/nginx/sites-available/mitos
chmod 644 /etc/nginx/sites-available/mitos
ln -sf /etc/nginx/sites-available/mitos /etc/nginx/sites-enabled/mitos
rm -f /etc/nginx/sites-enabled/default
nginx -t
systemctl enable --now "php$PHP_VERSION-fpm"
systemctl restart "php$PHP_VERSION-fpm"
systemctl reload nginx

echo "Listo. Falta: abrir puerto 80/443 en el Security Group, importar la base/uploads y lanzar el workflow Deploy."
