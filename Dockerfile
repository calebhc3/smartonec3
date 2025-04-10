FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
	libzip-dev \
	libpng-dev \
	libicu-dev \
	default-libmysqlclient-dev \
	&& docker-php-ext-configure intl \
	&& docker-php-ext-install intl zip gd pdo pdo_mysql 


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

CMD ["php-fpm"]
