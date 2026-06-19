FROM php:8.2-apache

# Włącz mod_rewrite i cURL
RUN a2enmod rewrite && docker-php-ext-install curl

# Skopiuj pliki aplikacji
COPY . /var/www/html/

# Uprawnienia do zapisu (dla users.json)
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

EXPOSE 80
