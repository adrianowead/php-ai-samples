#!/bin/sh
cd /var/www/html

if [ ! -d ./vendor ] || [ ! -f ./vendor/autoload.php ]; then
    composer install
fi

if [ ! -d ./node-modules ]; then
    npm install
    npm run production
fi

if [ ! -f ./.env ]; then
    cp .env.example .env
    php artisan key:generate
fi

supervisord
