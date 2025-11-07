FROM php:8.2-apache

RUN docker-php-ext-install pdo
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install mysqli
RUN a2enmod rewrite
RUN service apache2 restart