#!/bin/bash

echo "Starting Deployment..."

# 1. Pull latest code
git pull origin main

# 2. Install dependencies SP
composer install --no-dev --optimize-autoloader
npm install
npm run build

# 3. Clear and Refresh Cache (The Solution)
php artisan optimize
php artisan view:clear
php artisan route:clear
php artisan cache:clear
php artisan config:clear

# 4. Database migrations
php artisan migrate --force

echo "Deployment Finished Successfully!"
