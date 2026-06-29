FROM wordpress:php8.2-apache

RUN rm -f /etc/apache2/mods-enabled/mpm_event.load \
    /etc/apache2/mods-enabled/mpm_event.conf \
    /etc/apache2/mods-enabled/mpm_worker.load \
    /etc/apache2/mods-enabled/mpm_worker.conf \
    && a2enmod mpm_prefork rewrite

COPY wp-content /usr/src/wordpress/wp-content
COPY wp-config.php /usr/src/wordpress/wp-config.php

RUN chown -R www-data:www-data /usr/src/wordpress/wp-content /usr/src/wordpress/wp-config.php