FROM composer:2 AS composer
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-scripts --ignore-platform-req=php --ignore-platform-req=ext-pcntl
COPY . .
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader --ignore-platform-req=php --ignore-platform-req=ext-pcntl

FROM node:25 AS node
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY resources ./resources
COPY vite.config.js tailwind.config.js postcss.config.js ./
RUN npm run build

FROM php:8.3-apache AS runtime
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    && docker-php-ext-install pdo_mysql bcmath pcntl intl \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
COPY --from=composer /app /var/www/html
COPY --from=node /app/public/build /var/www/html/public/build
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]
