#!/bin/bash
set -e
echo "Step 1: Creating laravel project..."
composer create-project --prefer-dist laravel/laravel host-app

cd host-app
echo "Step 2: Adding skills package as a local repository and installing it..."
composer config minimum-stability dev
composer config prefer-stable true
composer config repositories.skills path ../skills
composer require sonysarkis/skills *@dev

echo "Step 3: Publishing configuration and Vue.js interface..."
php artisan vendor:publish --tag=quotes-config --force
php artisan vendor:publish --tag=quotes-assets --force

echo "All set! Starting server at http://localhost:8080/quotes-ui"
php artisan serve --host=0.0.0.0 --port=8080