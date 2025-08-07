#!/bin/sh
set -e

cd /var/www

# Install composer dependencies if vendor folder doesn't exist
if command -v composer >/dev/null 2>&1; then
  if [ ! -d "vendor" ]; then
    echo "Running composer install..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
  fi
else
  echo "Composer not found, skipping composer install."
fi

# Install npm dependencies if node_modules folder doesn't exist
if command -v npm >/dev/null 2>&1; then
  if [ ! -d "node_modules" ]; then
    echo "Running npm install..."
    npm install
  fi

  echo "Building frontend assets..."
  npm run build
else
  echo "npm not found, skipping npm install and build."
fi

# Laravel optimize commands
if command -v php >/dev/null 2>&1; then
  echo "Running Laravel optimize commands..."
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache

  echo "Running Laravel migrations..."
  wait-for-mysql.sh db php artisan migrate --force

  echo "Seeding database..."
  php artisan db:seed --force
else
  echo "PHP not found, skipping Laravel commands."
fi

exec "$@"
