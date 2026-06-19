FROM php:8.2-apache

# Włącz mod_rewrite i zainstaluj curl
RUN apt-get update && \
    apt-get install -y libcurl4-openssl-dev && \
    docker-php-ext-install curl && \
    a2enmod rewrite && \
    rm -rf /var/lib/apt/lists/*

# Skopiuj pliki aplikacji
COPY . /var/www/html/

# Uprawnienia do zapisu (dla users.json)
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

EXPOSE 80
