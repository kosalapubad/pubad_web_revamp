FROM wordpress:php8.2-apache

RUN rm -f /etc/apache2/mods-enabled/mpm_*.load \
          /etc/apache2/mods-enabled/mpm_*.conf \
    && a2enmod mpm_prefork rewrite

COPY wp-content /usr/src/wordpress/wp-content
COPY wp-config.php /usr/src/wordpress/wp-config.php