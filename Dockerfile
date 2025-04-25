FROM php:8.4-fpm

# Corrige fontes do APT
RUN if [ -f /etc/apt/sources.list ]; then \
        sed -i 's|http://deb.debian.org|https://deb.debian.org|g' /etc/apt/sources.list; \
    fi \
    && if [ -f /etc/apt/sources.list.d/debian.sources ]; then \
        sed -i 's|http://deb.debian.org|https://deb.debian.org|g' /etc/apt/sources.list.d/debian.sources; \
    fi \
    && apt-get update \
    && apt-get install -y \
        libzip-dev \
        libpng-dev \
        libicu-dev \
        default-libmysqlclient-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl zip gd pdo pdo_mysql

# 2. Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Diretório de trabalho
WORKDIR /var/www

# 4. Comando de inicialização padrão
CMD ["php-fpm"]
