FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev libpq-dev libsqlite3-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql pdo_sqlite mbstring exif pcntl bcmath gd xml \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set Apache document root to Laravel's public directory
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Allow .htaccess overrides
RUN sed -i '/<Directory ${APACHE_DOCUMENT_ROOT}>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf || true
RUN echo '<Directory /var/www/html/public>\n    AllowOverride All\n    Require all granted\n</Directory>' > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Cache bust to force rebuild
ARG CACHEBUST=1

# Copy application
COPY . .

# Install PHP dependencies (no dev)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Create production .env from scratch (not from .env.example to avoid any local leaks)
RUN printf 'APP_NAME=JobAggregator\n\
APP_ENV=production\n\
APP_DEBUG=false\n\
APP_KEY=base64:uSuP9wcJG3/LRCHglTi1ExORd4f86FqswSJgHWehE/U=\n\
APP_URL=http://localhost\n\
DB_CONNECTION=sqlite\n\
DB_DATABASE=/var/www/html/database/database.sqlite\n\
SESSION_DRIVER=file\n\
SESSION_LIFETIME=120\n\
CACHE_STORE=file\n\
QUEUE_CONNECTION=sync\n\
LOG_CHANNEL=stderr\n\
FILESYSTEM_DISK=local\n\
BROADCAST_CONNECTION=log\n\
' > .env

# Create SQLite database and set permissions
RUN mkdir -p database storage/app/public storage/framework/cache/data \
    storage/framework/sessions storage/framework/views storage/logs \
    bootstrap/cache \
    && touch database/database.sqlite \
    && chown -R www-data:www-data storage bootstrap/cache database \
    && chmod -R 775 storage bootstrap/cache database

# Generate app key, run migrations, seed
RUN php artisan key:generate --force \
    && php artisan migrate --force --seed

# Scrape real jobs (non-blocking — build succeeds even if scraping fails)
RUN php artisan jobs:scrape || echo "Warning: scrape failed, will retry via cron"

# Pre-cache views only (config/routes cached at boot with runtime env vars)
RUN php artisan view:cache

# Final permissions — world-writable so any user can access SQLite + storage
RUN chmod -R 777 storage bootstrap/cache database

EXPOSE 80

# At startup: fix permissions, run migrations, start Apache
CMD chmod -R 777 database storage bootstrap/cache \
    && php artisan migrate --force \
    && apache2-foreground
