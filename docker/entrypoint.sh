#!/usr/bin/env bash

set -e

cd /var/www/html

export COMPOSER_ALLOW_SUPERUSER=1

git config --global --add safe.directory /var/www/html || true

if [ "${AUTOCOMPOSER_INSTALL:-false}" = "true" ]; then
    echo "entrypoint verify dependencies of the composer..."
    flock /tmp/fp_app-composer.lock composer install --no-interaction --prefer-dist --no-progress --no-scripts
fi

if [ -f artisan ]; then

    if [ -z "$(grep '^APP_KEY=' .env | cut -d '=' -f2)" ] || [ "$(grep '^APP_KEY=' .env | cut -d '=' -f2)" = "" ]; then
        echo "entrypoint generating APP_KEY..."
        php artisan key:generate --force
    else
        echo "APP_KEY already set, skipping key generation"
    fi

    if [ "${AUTO_MIGRATE:-false}" = "true" ]; then
        until php artisan tinker --execute="DB::connection()->getPdo();" >/dev/null 2>&1; do
            echo "inaccessible database"
            sleep 2
        done

        echo "entrypoint listen migrate..."
        php artisan migrate --seed
    fi
fi

if [ "$(id -u)" = "0" ]; then
    chown -R www-data:www-data storage bootstrap/cache || true
    chmod -R 775 storage bootstrap/cache
fi

echo "entrypoint init commands: $*"

exec "$@"