FROM wordpress:php8.2-apache

COPY wp-content /var/www/html/wp-content
COPY wp-config.php /var/www/html/wp-config.php

<<<<<<< HEAD
RUN chown -R www-data:www-data /var/www/html/wp-content
=======
RUN chown -R www-data:www-data /var/www/html/wp-content
>>>>>>> ea3d117e7ccdf2046142d7e936088b8761a0144c
