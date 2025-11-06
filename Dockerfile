# =========================
# Stage 1: Build frontend + backend
# =========================
FROM php:8.2-fpm AS build

# Install system deps + Node
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql \
    && curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs

# Copy composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy app
COPY . .

# Install PHP deps
RUN composer install --no-dev --optimize-autoloader

# Install JS deps and build Vite
RUN npm install && npm run build

# =========================
# Stage 2: Runtime image
# =========================
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev zip git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

WORKDIR /var/www

# Copy only built app from build stage
COPY --from=build /var/www /var/www

RUN chown -R www-data:www-data storage bootstrap/cache

CMD ["php-fpm"]
