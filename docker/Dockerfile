#=======================#
# composer já instalado #
#=======================#
FROM composer:latest AS composer

#==============#
# imagem final #
#==============#
FROM php:8.0.8-apache-buster

WORKDIR /var/www/html

# definindo timezone para UTC (global)
RUN apt-get update \
    && apt-get install -y gnupg tzdata \
    && echo "UTC" > /etc/timezone \
    && dpkg-reconfigure -f noninteractive tzdata

RUN apt-get install -y \
    libpng-dev \
    zlib1g-dev \
    libxml2-dev \
    libzip-dev \
    libonig-dev \
    libssl-dev \
    pkg-config \
    zip \
    curl \
    unzip \
    git \
    supervisor \
    vim \
    && docker-php-ext-install zip \
    && docker-php-ext-install sockets \
    && docker-php-ext-install pcntl \
    && docker-php-source delete

# instalando extensão PHP swoole à partir do fonte
RUN printf "yes\n" | pecl install -f swoole \
    && echo "extension=swoole.so" > /usr/local/etc/php/conf.d/swoole.ini

# nodejs
RUN curl -sL https://deb.nodesource.com/setup_14.x -o "node.sh" \
    && chmod +x node.sh \
    && ./node.sh \
    && rm node.sh

RUN apt-get install -y \
    nodejs

COPY docker/apache/vhost.conf /etc/apache2/sites-available/000-default.conf
COPY docker/apache/ports.conf /etc/apache2/ports.conf

# copiando config do supervisor
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN chown -R www-data:www-data /var/www/html \
    && a2enmod rewrite \
    && a2enmod proxy \
    && a2enmod proxy_http \
    && a2enmod proxy_ajp \
    && a2enmod ssl \
    && a2enmod deflate \
    && a2enmod headers \
    && a2enmod proxy_balancer \
    && a2enmod proxy_connect \
    && a2enmod proxy_html \
    && echo "ServerName 127.0.0.1" | tee -a /etc/apache2/apache2.conf

# instalar composer a partir do outro container
COPY --from=composer /usr/bin/composer /usr/bin/composer

COPY docker/dockerrun.sh /usr/local/bin/dockerrun.sh
RUN chmod +x /usr/local/bin/dockerrun.sh

# abrindo porta 80
EXPOSE 80

# iniciando supervisor
CMD ["dockerrun.sh"]
