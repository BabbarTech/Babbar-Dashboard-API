#!/bin/bash

# Install vendors directory and .env
echo 'Install vendor dependencies'
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs

# Handle .env file creation and laravel key generation
echo 'Generate .env file'
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer run-script  post-create-project-cmd

# Run Laravel Sail and migrate database
echo "Start docker PHP and Mysql daemons"
./vendor/bin/sail up -d
./vendor/bin/sail restart

echo "waiting start..."
sleep 30

# Migrate database
echo "Migrate database"
./vendor/bin/sail php artisan migrate --path=database/migrations/landlord --database=landlord --force
