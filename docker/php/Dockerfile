FROM php:8.3-fpm

ARG UID
ARG GID

RUN apt update \
  && apt install -y default-mysql-client zlib1g-dev libzip-dev unzip \
  && docker-php-ext-install pdo_mysql zip

RUN curl -sS https://getcomposer.org/installer | php \
  && mv composer.phar /usr/local/bin/composer \
  && composer self-update

RUN groupadd -g ${GID} taichi \
  && useradd -u ${UID} -g taichi -m taichi

USER taichi
WORKDIR /var/www