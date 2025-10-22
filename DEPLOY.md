Quick deploy guide for Absensi

Goal: make deployments reproducible and fast. Two options are provided:

1. CI/CD (recommended): push to `main`, GitHub Actions will build artifact and copy to server (see `.github/workflows/deploy.yml`).

2. Manual deploy (quick): create a tarball from repo and run `deploy/deploy.sh` on server.

Server prerequisites

-   PHP 8.4 (match local), Composer, tar, unzip, systemd for php-fpm
-   `www-data` user (or adapt scripts to your web user)
-   A directory for releases, e.g. `/var/www/absensi` with subdir `releases` and `current` symlink

Manual deploy steps

1. On local build server or CI:

```bash
composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader
tar -czf release.tar.gz --exclude vendor --exclude node_modules .
```

2. Copy `release.tar.gz` to server `DEPLOY_PATH/releases/` (e.g. `/var/www/absensi/releases/`)
3. SSH into server and run:

```bash
sudo bash /var/www/absensi/deploy/deploy.sh /var/www/absensi/releases/release.tar.gz
```

Post-deploy

-   Check logs: `tail -n 200 storage/logs/laravel.log`
-   If you changed `.env`, run `php artisan config:clear` or `php artisan config:cache` after verifying
-   Restart workers `php artisan queue:restart` and reload php-fpm if needed

Notes about `.env` and config caching

-   If you `php artisan config:cache` the app will ignore runtime changes to `.env` until you clear/regenerate cache.
-   To apply a changed `.env` on the server do:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache   # optional
```

Security

-   Use SSH deploy key with restricted scope.
-   Store secrets as GitHub Actions secrets and _do not_ commit `.env` to repo.

Rollback

-   The deploy script keeps releases by timestamp. You can roll back by re-pointing the `current` symlink to older release and running `php artisan migrate:rollback` if necessary.
