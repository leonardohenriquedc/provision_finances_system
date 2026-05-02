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
    
    
    if [ "${AUTO_MIGRATE:-false}" = "true" ]; then 
        until php artisan db:monitor; do 
            echo "inaccessible database"
            sleep 2
        done
        
        echo "entrypoint listen migrate..."
        php artisan migrate:fresh --seed
    fi
fi

# if [ "$(id -u)" = "0" ]; then 
#     chown -R www-data:www-data storage bootstrap/cache || true 
# fi

echo "entrypoint init commands: $*"

exec "$@" 