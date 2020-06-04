FROM php:5.6-apache
ADD AIFdb /var/www/html/
RUN mkdir /var/www/html/upload/tmp
RUN chmod 777 /var/www/html/upload/tmp
RUN mkdir -p /var/www/html/tmp
RUN chmod 777 /var/www/html/tmp
RUN a2enmod rewrite
RUN a2enmod headers 
COPY php.ini /usr/local/etc/php/
RUN docker-php-ext-install mysql mysqli pdo
RUN apt-get update && apt-get install -y graphviz mysql-client
