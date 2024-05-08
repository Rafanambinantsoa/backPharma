#!/usr/bin/env bash
echo "Running composer"
composer install --no-dev --working-dir=/var/www/html

echo "Running migrations..."
php artisan migrate --force
echo "Running seeds..."
php artisan db:seed AdminUser --force
php artisan db:seed UserSeed --force
echo "Runnings schedule..."
php artisan schedule:run --force

#echo "Running seeders..."
#php artisan db:seed

#echo "Running vite..."
#npm install
#npm run build
