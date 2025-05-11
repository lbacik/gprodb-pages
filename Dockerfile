FROM php:8.4-apache

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get -y update \
    && apt-get -y install git unzip librabbitmq-dev libssl-dev \
    && apt-get -y clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
    && docker-php-ext-install pdo_mysql \
    && pecl install amqp \
    && docker-php-ext-enable amqp

COPY . /opt/app
WORKDIR /opt/app

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-interaction --optimize-autoloader

RUN rm -drf /var/www/html \
    && ln -s /opt/app/public /var/www/html \
    && chown -R www-data:www-data /opt/app/var \
    && a2enmod rewrite
