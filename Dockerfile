FROM wordpress:php8.2-apache

COPY wp-content /usr/src/wordpress/wp-content
COPY wp-config.php /usr/src/wordpress/wp-config.php

RUN chown -R www-data:www-data /usr/src/wordpress/wp-content /usr/src/wordpress/wp-config.php

CMD ["bash", "-lc", "rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.* && a2enmod mpm_prefork rewrite >/dev/null 2>&1 || true && apache2-foreground"]