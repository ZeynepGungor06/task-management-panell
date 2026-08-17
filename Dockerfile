FROM php:8.2-apache


RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git


RUN docker-php-ext-install pdo pdo_mysql zip


RUN a2enmod rewrite
RUN apt-get update && apt-get install -y \
    autoconf \
    zlib1g-dev \
    && pecl install grpc \
    && docker-php-ext-enable grpc


ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer