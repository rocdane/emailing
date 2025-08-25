# -------------------------------
# Dockerfile Laravel 12 - PHP 8.3 + Node.js + Redis
# -------------------------------

# 1. Image de base PHP avec extensions nécessaires
FROM php:8.3-fpm

# 2. Installer les dépendances système
RUN apt-get update && apt-get install -y git curl zip unzip libzip-dev libonig-dev libpng-dev libxml2-dev libicu-dev libpq-dev libjpeg-dev libfreetype6-dev nodejs npm 
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd xml intl zip


# 3. Installation des extensions PHP (incluant sockets)
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl opcache sockets sodium
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp 
RUN docker-php-ext-configure intl

RUN pecl install redis && docker-php-ext-enable redis

# 4. Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Définir le répertoire de travail
WORKDIR /var/www/html

# 6. Copier les fichiers du projet
COPY . .

RUN docker-php-ext-install sockets

# 7. Installer les dépendances PHP et Node.js
RUN composer install --prefer-dist --no-interaction --optimize-autoloader --no-dev --verbose
RUN npm ci && npm run build
RUN npm ci --only=production && npm run build

# 8. Copier l’exemple d’environnement
RUN cp .env.example .env && php artisan key:generate

# 9. Permissions des dossiers storage et bootstrap
RUN mkdir -p storage/framework/{sessions,views,cache} && chmod -R 777 storage bootstrap/cache

# 10. Exposer le port PHP-FPM
EXPOSE 9000

# 11. Commande par défaut pour PHP-FPM
CMD ["php-fpm"]
