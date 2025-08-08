#!/bin/sh
set -ex

cd /var/www

# Install composer dependencies if vendor folder doesn't exist
if command -v composer >/dev/null 2>&1; then
  if [ ! -d "vendor" ]; then
    echo "Running composer install..."
    composer install --no-interaction --prefer-dist --optimize-autoloader || { echo "ERROR: Composer install failed"; exit 255; }
  else
    echo "Composer dependencies already installed, skipping composer install."
  fi
else
  echo "Composer not found, skipping composer install."
fi

# Install npm dependencies if node_modules folder doesn't exist
if command -v npm >/dev/null 2>&1; then
  if [ ! -d "node_modules" ]; then
    echo "Running npm install..."
    npm install || { echo "ERROR: npm install failed"; exit 255; }
  else
    echo "npm dependencies already installed, skipping npm install."
  fi

  echo "Building frontend assets..."
  npm run build || { echo "ERROR: npm build failed"; exit 255; }
else
  echo "npm not found, skipping npm install and build."
fi

# Laravel optimize commands
if command -v php >/dev/null 2>&1; then
  echo "Running Laravel optimize commands..."

  php artisan config:cache || { echo "ERROR: config:cache failed"; exit 255; }
  php artisan route:cache || { echo "ERROR: route:cache failed"; exit 255; }
  php artisan view:cache || { echo "ERROR: view:cache failed"; exit 255; }

  echo "Running Laravel migrations..."
  # Run wait-for-mysql.sh with error check
  wait-for-mysql.sh db "$DB_USERNAME" "$DB_PASSWORD" php artisan migrate --force || { echo "ERROR: Migration failed"; exit 255; }

  SEED_FLAG_FILE="/var/www/.seeded"

  if [ ! -f "$SEED_FLAG_FILE" ]; then
    echo "Seeding database for the first time..."
    php artisan db:seed --force || { echo "ERROR: Database seeding failed"; exit 255; }
    touch "$SEED_FLAG_FILE"
  else
    echo "Database seeding already done. Skipping."
  fi
else
  echo "PHP not found, skipping Laravel commands."
fi

exec "$@"
