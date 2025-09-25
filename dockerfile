# Define the base image
FROM teguh02/laravel-filament:latest

# Set user to root
USER root

# Change the working directory
WORKDIR /var/www

# Remove all files in /var/www/html directory
RUN rm -rf /var/www/html/*

# Copy only the public directory into /var/www/html
COPY ./public /var/www/html

# Copy the entire project (including vendor and node_modules)
COPY . /var/www

# Set directory permissions
RUN chmod -R 777 /var/www

# Install MySQL client
RUN apt-get update && apt-get install -y default-mysql-client curl unzip git

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Node.js and npm (LTS version)
RUN curl -fsSL https://deb.nodesource.com/setup_lts.x | bash - \
    && apt-get install -y nodejs

# ⚡ Opcional: si quieres correr build de Vite dentro del contenedor
RUN composer install
RUN npm install
# RUN npm run build

# Configure PHP (display errors)
RUN sed -i 's/display_errors = Off/display_errors = On/g' /etc/php/8.3/fpm/php.ini
RUN sed -i 's/display_errors = Off/display_errors = On/g' /etc/php/8.3/cli/php.ini
RUN sed -i 's/error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT/error_reporting = E_ALL/g' /etc/php/8.3/fpm/php.ini


