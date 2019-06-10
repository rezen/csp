FROM php:7.2.2-apache
ENV PORT 3000
RUN  sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf
RUN apt-get update && \
    apt-get install -y --no-install-recommends git zip

RUN curl --silent --show-error https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer
RUN ln -s /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/rewrite.load
RUN mkdir -p /var/www/html/logs
COPY ./vendor /var/www/html/
COPY ./assets ./inc ./views  /var/www/html/
COPY about.php \
    ajax.php \
    index.php \
    exercise.php \
    x.php \
    xss.php \ 
    report.php \ 
    composer.json \ 
    composer.lock \
    tabnab.html /var/www/html/
WORKDIR /var/www/html/
CMD sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf && docker-php-entrypoint apache2-foreground