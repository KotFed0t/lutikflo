FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
      apt-utils \
      libpq-dev \
      libpng-dev \
      libzip-dev \
      zip unzip \
      curl \
#      nodejs \
#      npm \
      libfreetype-dev \
      libjpeg62-turbo-dev \
      git && \
      docker-php-ext-install pdo && \
      docker-php-ext-install pdo_mysql && \
      docker-php-ext-install pdo_pgsql && \
      docker-php-ext-install bcmath && \
      docker-php-ext-configure gd --with-freetype --with-jpeg && \
      docker-php-ext-install -j$(nproc) gd &&  \
      docker-php-ext-install zip && \
      apt-get clean && \
      rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- \
    --filename=composer \
    --install-dir=/usr/local/bin

WORKDIR /var/www/lutikflo
RUN chown -R www-data:www-data /var/www
