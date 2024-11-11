FROM php:8.2-fpm-alpine as dev

EXPOSE 80

ENV PHP_DISPLAY_ERRORS 0
ENV PHP_ERROR_REPORTING 22527
ENV COMPOSER_ALLOW_SUPERUSER 1

WORKDIR /var/www/
RUN apk add --update nginx supervisor busybox-suid

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS curl-dev && \
    docker-php-ext-configure bcmath --enable-bcmath && \
    docker-php-ext-configure opcache --enable-opcache && \
    docker-php-ext-install -j$(nproc) bcmath opcache curl && \
    apk del -f .build-deps && \
    rm -rf /tmp/* /var/cache/apk/* \

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY backend/build/docker/app/configs /
COPY main/build/docker/app/configs /

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
COPY backend/composer.json backend/composer.lock backend/symfony.lock /var/www/
RUN composer install --no-scripts --prefer-dist --no-autoloader

COPY backend/ /var/www
RUN composer dump-autoload --optimize
RUN bin/console assets:install public && \
    bin/console nelmio:apidoc:dump
RUN rm -rf /var/www/var/cache/* && bin/console cache:warmup --env=prod

RUN composer clear-cache && \
    rm /var/www/.env.*

COPY /frontend/dist /usr/share/nginx/html