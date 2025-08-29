FROM php:8.1-fpm-alpine

RUN apk add --no-cache \
    git \
    curl \
    zip \
    unzip \
    icu-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    libzip-dev \
    oniguruma-dev \
    bash \
    autoconf \
    gcc \
    g++ \
    make   # Thêm gcc, g++, make để biên dịch Redis extension

RUN docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install pdo_mysql bcmath intl gd zip opcache


RUN pecl install redis && docker-php-ext-enable redis


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


WORKDIR /var/www/html
COPY . .
RUN composer install --prefer-dist --no-progress --no-interaction --optimize-autoloader

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

CMD ["php-fpm", "-y", "/usr/local/etc/php-fpm.conf", "-R"]
