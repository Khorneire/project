FROM php:8.3-fpm-bullseye

# Combine apt installs
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        curl gnupg2 ca-certificates \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y --no-install-recommends \
        git unzip zip libpq-dev libxml2-dev libzip-dev libpng-dev libonig-dev \
        libjpeg-dev libfreetype6-dev libicu-dev libxslt1-dev libsqlite3-dev libreadline-dev libmagickwand-dev \
        build-essential default-mysql-client nodejs \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip intl xsl sockets

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www

COPY wait-for-mysql.sh docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/wait-for-mysql.sh /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]
