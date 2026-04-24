# Deployment Notes

Target the same PHP version as production before building dependencies. Hostinger currently uses PHP 8.1, so run Composer on PHP 8.1 locally or run `composer install` directly on the server.

## Build Upload Zip

```bash
./deploy.sh
```

The archive excludes `vendor/`, `node_modules/`, `.env`, macOS metadata, logs, and cached framework files.

## Server Install

```bash
cd /home/u528691413/domains/bana.ma/public_html/dev
mkdir -p tmp

# Important: remove any old or partial vendor folder before installing.
# Keeping a stale vendor folder can leave Composer autoload files pointing
# to package files that no longer exist.
rm -rf vendor

TMPDIR=./tmp composer install --no-dev --optimize-autoloader
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
```

If Hostinger has multiple PHP versions, confirm the CLI PHP version before
running Composer:

```bash
php -v
composer -V
```

Use the same PHP major/minor version as the website is configured to use.

The Composer install automatically runs `scripts/patch-portable-ascii.php` for the Laravel 8 `voku/portable-ascii` PHP 8.1 patch.

## Session/Login Loop Checklist

Make sure the production `.env` has a stable app key and file sessions:

```env
APP_KEY=base64:your-existing-production-key
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

If login still loops after a deployment:

```bash
php artisan cache:clear
php artisan config:clear
rm -rf storage/framework/sessions/*
```
