FROM dunglas/frankenphp:latest AS base

# System dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    acl \
    git \
    unzip \
    curl \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions
RUN install-php-extensions \
    pdo_mysql \
    intl \
    opcache \
    apcu \
    redis \
    amqp \
    zip \
    gd \
    bcmath \
    sockets

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# -----------------------------------------------------------
# DEV stage
# -----------------------------------------------------------
FROM base AS dev

RUN install-php-extensions xdebug

# PHP dev config
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

COPY docker/frankenphp/conf.d/20-app.dev.ini $PHP_INI_DIR/conf.d/20-app.ini
COPY docker/frankenphp/conf.d/30-xdebug.ini $PHP_INI_DIR/conf.d/30-xdebug.ini

ENV APP_ENV=dev
ENV FRANKENPHP_CONFIG="worker ./public/index.php"
ENV SERVER_NAME=:80
ENV XDEBUG_MODE=off

COPY docker/frankenphp/Caddyfile /etc/caddy/Caddyfile

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]

# -----------------------------------------------------------
# PROD stage
# -----------------------------------------------------------
FROM base AS prod

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY docker/frankenphp/conf.d/20-app.prod.ini $PHP_INI_DIR/conf.d/20-app.ini

ENV APP_ENV=prod
ENV APP_DEBUG=0
ENV FRANKENPHP_CONFIG="worker ./public/index.php"
ENV SERVER_NAME=:80

COPY docker/frankenphp/Caddyfile /etc/caddy/Caddyfile

# Copy app source
COPY . /app

RUN composer install --no-dev --optimize-autoloader --classmap-authoritative \
    && composer dump-env prod \
    && php bin/console cache:warmup \
    && chown -R www-data:www-data var/

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
