#!/usr/bin/env bash
set -euo pipefail

# Simple deploy script to extract release, install deps and switch symlink
RELEASE_TAR="$1"
DEPLOY_PATH="/var/www/absensi"
RELEASE_DIR="$DEPLOY_PATH/releases/$(date +%s)"

mkdir -p "$RELEASE_DIR"

echo "Extracting $RELEASE_TAR to $RELEASE_DIR"
tar -xzf "$RELEASE_TAR" -C "$RELEASE_DIR"

cd "$RELEASE_DIR"

# install composer deps
composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader

# set permissions
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R ug+rwx storage bootstrap/cache || true

# switch current
ln -nfs "$RELEASE_DIR" "$DEPLOY_PATH/current"

cd "$DEPLOY_PATH/current"

php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart || true

# reload php-fpm gracefully
if systemctl is-active --quiet php8.4-fpm; then
  sudo systemctl reload php8.4-fpm || true
fi

echo "Deployed $RELEASE_DIR"
