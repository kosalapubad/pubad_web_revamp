FROM wordpress:php8.2-apache

RUN a2dismod mpm_event mpm_worker || true \
    && a2enmod mpm_prefork rewrite

COPY wp-content /var/www/html/wp-content
COPY wp-config.php /var/www/html/wp-config.php

RUN chown -R www-data:www-data /var/www/html/wp-content /var/www/html/wp-config.php