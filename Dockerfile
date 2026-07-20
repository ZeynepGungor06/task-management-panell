FROM php:8.2-apache

# Laravel'in ihtiyaç duyduğu sistem kütüphanelerini kuruyoruz
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git

# PHP eklentilerini (veritabanı bağlantısı vb.) aktif ediyoruz
RUN docker-php-ext-install pdo pdo_mysql zip

# Apache'nin mod_rewrite modülünü açıyoruz (Laravel routing için şart)
RUN a2enmod rewrite

# Apache'nin başlangıç klasörünü Laravel'in 'public' klasörü yapıyoruz
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Backend için Composer'ı kuruyoruz
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer