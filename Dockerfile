# Étape de construction
FROM composer:2.7 as builder

WORKDIR /app
COPY . .
RUN composer install --prefer-dist --no-dev --optimize-autoloader --no-interaction

# Étape d'exécution
FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

# Installer les dépendances système
RUN apk add --no-cache nginx supervisor libpng-dev libzip-dev zip unzip mysql-client nodejs npm

# Installer les extensions PHP
RUN docker-php-ext-install pdo pdo_mysql gd zip bcmath

# Configurer Nginx et PHP-FPM
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/fpm-pool.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copier l'application
COPY --from=builder /app .
COPY . .

# Configurer les permissions
RUN chown -R www-data:www-data /var/www/html/storage
RUN chown -R www-data:www-data /var/www/html/bootstrap/cache

# Installer les dépendances frontend et compiler les assets
RUN npm install && npm run build

EXPOSE 80
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]