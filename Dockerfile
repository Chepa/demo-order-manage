FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    icu-dev \
    libpq-dev \
    oniguruma-dev \
    $PHPIZE_DEPS \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) \
        intl \
        opcache \
        pdo \
        pdo_pgsql \
        pgsql \
        mbstring \
        pcntl \
    && pecl install redis \
    && docker-php-ext-enable redis

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
