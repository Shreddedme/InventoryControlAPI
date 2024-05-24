# Используем базовый образ
FROM php:fpm-alpine

ARG USER_ID=1000
ARG GROUP_ID=1000
ARG USER=www-data
ARG GROUP=www-data

# Устанавливаем необходимые пакеты
RUN apk add --no-cache curl git build-base zlib-dev oniguruma-dev autoconf icu-dev bash libpq-dev

# Устанавливаем расширения для PHP
RUN docker-php-ext-install pdo_pgsql intl

# Устанавливаем Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Устанавливаем Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash -s -- --install-dir=/usr/local/bin \
    && chmod a+x /usr/local/bin/symfony

WORKDIR /var/www

# Копируем файлы проекта, устанавливая владельца www-data
COPY --chown=$USER:$USER . /var/www/

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-interaction
RUN chown -R $USER:$USER /var/www

USER $USER

# Запускаем php-fpm
CMD ["php-fpm"]

# Открываем порт
EXPOSE 9000