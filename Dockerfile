FROM wordpress:php8.2-apache

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html

CMD ["bash", "-lc", "rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.* && a2enmod mpm_prefork rewrite >/dev/null 2>&1 || true && apache2-foreground"]