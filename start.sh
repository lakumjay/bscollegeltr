#!/bin/bash
# Use Replit domain if available, else fallback to localhost
REPLIT_URL="https://${REPLIT_DEV_DOMAIN}"
FINAL_APP_URL="${REPLIT_URL:-http://localhost:5000}"

# Generate .env file from environment variables
cat > .env << EOF
APP_NAME="${APP_NAME:-Power College}"
APP_ENV=${APP_ENV:-local}
APP_KEY=${APP_KEY}
APP_DEBUG=${APP_DEBUG:-true}
APP_URL=${FINAL_APP_URL}

LOG_CHANNEL=${LOG_CHANNEL:-stack}
LOG_DEPRECATIONS_CHANNEL=${LOG_DEPRECATIONS_CHANNEL:-null}
LOG_LEVEL=${LOG_LEVEL:-debug}

DB_CONNECTION=${DB_CONNECTION:-pgsql}
DB_HOST=${DB_HOST:-helium}
DB_PORT=${DB_PORT:-5432}
DB_DATABASE=${DB_DATABASE:-heliumdb}
DB_USERNAME=${DB_USERNAME:-postgres}
DB_PASSWORD=${PGPASSWORD}

BROADCAST_DRIVER=${BROADCAST_DRIVER:-log}
CACHE_DRIVER=${CACHE_DRIVER:-file}
FILESYSTEM_DRIVER=${FILESYSTEM_DRIVER:-local}
QUEUE_CONNECTION=${QUEUE_CONNECTION:-sync}
SESSION_DRIVER=${SESSION_DRIVER:-file}
SESSION_LIFETIME=${SESSION_LIFETIME:-120}

MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=null
MAIL_FROM_NAME="\${APP_NAME}"
EOF

# Clear config cache
php artisan config:clear 2>/dev/null || true

# Run migrations
php artisan migrate --force 2>&1

# Clear and cache config
php artisan cache:clear 2>/dev/null || true

# Start the PHP built-in server on port 5000 with multiple workers
# PHP_CLI_SERVER_WORKERS allows concurrent requests (needed for AJAX calls)
export PHP_CLI_SERVER_WORKERS=4
php artisan serve --host=0.0.0.0 --port=5000
