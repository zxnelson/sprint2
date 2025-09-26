# Usar imagen oficial de PHP con Apache
FROM php:8.3-apache

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm \
    default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP necesarias para Laravel
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Habilitar mod_rewrite de Apache
RUN a2enmod rewrite

# Configurar Apache para Laravel
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos de configuración primero (para cache de Docker)
COPY composer.json composer.lock package.json package-lock.json ./

# Instalar dependencias PHP
RUN composer install --no-dev --no-scripts --no-autoloader --optimize-autoloader

# Instalar dependencias Node.js
RUN npm ci --only=production

# Copiar el resto del código
COPY . .

# Completar instalación de Composer
RUN composer dump-autoload --optimize

# Compilar assets para producción
RUN npm run build

# Crear directorios necesarios y establecer permisos seguros
RUN mkdir -p storage/logs storage/framework/{cache,sessions,views} bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Configurar PHP para producción
RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

# Limpiar archivos innecesarios
RUN npm cache clean --force \
    && rm -rf node_modules \
    && rm -rf /tmp/*

# Exponer puerto 80
EXPOSE 80

# Script de inicio
RUN echo '#!/bin/bash\n\
# Generar APP_KEY si no existe\n\
if [ -z "$APP_KEY" ]; then\n\
    php artisan key:generate --force\n\
fi\n\
# Optimizar para producción\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan view:cache\n\
# Ejecutar migraciones si es necesario\n\
if [ "$RUN_MIGRATIONS" = "true" ]; then\n\
    php artisan migrate --force\n\
fi\n\
# Iniciar Apache\n\
apache2-foreground' > /usr/local/bin/start.sh \
    && chmod +x /usr/local/bin/start.sh

# Comando por defecto
CMD ["/usr/local/bin/start.sh"]
