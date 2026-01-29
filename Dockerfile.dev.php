FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    && apt-get clean

# Для отладки (XDEBUG)
RUN pecl install xdebug && docker-php-ext-enable xdebug
COPY ./for_docker/php/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN sed -i "s/^user = www-data/user = root/" /usr/local/etc/php-fpm.d/www.conf && \
    sed -i "s/^group = www-data/group = root/" /usr/local/etc/php-fpm.d/www.conf

# COPY ./for_docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./for_docker/php/php.ini /usr/local/etc/php
COPY ./site /var/www/html

RUN mkdir -p /var/lib/php/sessions && chmod 777 /var/lib/php/sessions

# От имени www-data
# RUN usermod -aG docker www-data
# RUN chown -R www-data:www-data /var/www/html

CMD ["php-fpm", "-R"]