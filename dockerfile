# Define the base image
FROM teguh02/laravel-filament:latest

# Set user to root
USER root

# Install system dependencies first
RUN apt-get update && apt-get install -y \
    default-mysql-client \
    curl \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# Install Composer (verificar si ya existe)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Node.js and npm (LTS version)
RUN curl -fsSL https://deb.nodesource.com/setup_lts.x | bash - \
    && apt-get install -y nodejs

# Change the working directory
WORKDIR /var/www

# Remove all files in /var/www/html directory
RUN rm -rf /var/www/html/*

# Copy composer files first (for Docker layer caching)
COPY composer.json composer.lock* ./

# Install PHP dependencies
RUN composer install --no-dev --no-scripts --no-autoloader --optimize-autoloader

# Copy package files for Node.js
COPY package*.json ./

# Install Node dependencies
RUN npm ci --only=production

# Copy the entire project
COPY . /var/www

# Complete composer setup
RUN composer dump-autoload --optimize

# Build assets for production
RUN npm run build

# Copy public directory to web root
RUN cp -r /var/www/public/* /var/www/html/

# Create necessary Laravel directories
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache

# Set proper permissions (SECURE - no 777!)
RUN chown -R www-data:www-data /var/www \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

# Configure PHP for PRODUCTION (not development!)
RUN sed -i 's/display_errors = On/display_errors = Off/g' /etc/php/8.3/fpm/php.ini \
    && sed -i 's/display_errors = On/display_errors = Off/g' /etc/php/8.3/cli/php.ini \
    && sed -i 's/error_reporting = E_ALL/error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT/g' /etc/php/8.3/fpm/php.ini

# Clean up
RUN npm cache clean --force \
    && rm -rf node_modules \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Create startup script
RUN echo '#!/bin/bash\n\
# Generate APP_KEY if not set\n\
if [ -z "$APP_KEY" ]; then\n\
    cd /var/www && php artisan key:generate --force\n\
fi\n\
# Cache configurations for production\n\
cd /var/www\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan view:cache\n\
# Run migrations if enabled\n\
if [ "$RUN_MIGRATIONS" = "true" ]; then\n\
    php artisan migrate --force\n\
fi\n\
# Ensure proper permissions\n\
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache\n\
# Start services (adjust based on your base image)\n\
exec supervisord -c /etc/supervisor/conf.d/supervisord.conf' > /usr/local/bin/start-laravel.sh \
    && chmod +x /usr/local/bin/start-laravel.sh

# Set working directory back to Laravel root
WORKDIR /var/www

# Use the startup script
CMD ["/usr/local/bin/start-laravel.sh"]
