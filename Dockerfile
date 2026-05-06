FROM php:8.2-apache

# install mysqli
RUN docker-php-ext-install mysqli

# enable apache mod rewrite (optional tapi bagus)
RUN a2enmod rewrite