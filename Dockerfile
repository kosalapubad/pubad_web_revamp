FROM wordpress:php8.2-apache

RUN rm -f /etc/apache2/mods-enabled/mpm_event.* \
          /etc/apache2/mods-enabled/mpm_worker.* \
    && a2dismod mpm_event mpm_worker || true \
    && a2enmod mpm_prefork rewrite

COPY wp-content /usr/src/wordpress/wp-content
COPY wp-config.php /usr/src/wordpress/wp-config.php