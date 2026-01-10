FROM php:8.2-fpm-alpine as dev

EXPOSE 80 9000

ENV PHP_DISPLAY_ERRORS 0
ENV PHP_ERROR_REPORTING 22527
ENV COMPOSER_ALLOW_SUPERUSER 1

WORKDIR /var/www/
RUN apk add --update nginx supervisor git busybox-suid postgresql-dev

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS curl-dev && \
#    apk add libzip-dev libonig-dev && \
    docker-php-ext-configure bcmath --enable-bcmath && \
    docker-php-ext-configure opcache --enable-opcache && \
    docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql && \
#    docker-php-ext-configure mbstring --enable-mbstring && \
#    pecl install xdebug && \
#    docker-php-ext-enable xdebug && \
    docker-php-ext-install -j$(nproc) bcmath opcache curl pdo_pgsql pgsql && \
    apk del -f .build-deps && \
    rm -rf /tmp/* /var/cache/apk/* \

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Copy custom configs
COPY build/docker/app/configs /

# composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisord.conf"]


FROM dev as test

COPY composer.json composer.lock symfony.lock /var/www/
RUN composer install --no-scripts --prefer-dist --no-autoloader
COPY . /var/www
# Finish composer
RUN composer dump-autoload --optimize
# Install assets
RUN bin/console assets:install public && \
    bin/console nelmio:apidoc:dump
RUN chown www-data:www-data /var/www/ -R


FROM test as prod

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Warmup cache
RUN rm -rf /var/www/var/cache/* && bin/console cache:warmup --env=prod
RUN chown www-data:www-data /var/www/var/ -R

# cleanup
RUN composer clear-cache && \
    rm /var/www/.env.* && \
#    rm /usr/local/bin/composer && \
    apk del git
#    pecl uninstall xdebug && \
#    rm /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
#    rm /usr/local/etc/php-fpm.d/xdebug.ini
