FROM php:8.0-fpm

# Set working directory
WORKDIR /var/www/html

# Install container dependencies
RUN apt-get update \
    && apt-get install -y \
        git \
        nginx \
        sudo \
        supervisor \
        zip \
    && apt-get clean \
    && rm -rf /var/cache/apt /var/lib/apt /var/log/{dpkg.log,apt}

# Install Composer
RUN curl \
        --location \
        --show-error \
        --output /tmp/composer-setup.php \
        https://getcomposer.org/installer \
    && php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm /tmp/composer-setup.php

# Install extension dependencies
RUN apt-get update \
    && apt-get install -y \
        automake \
        bzip2 \
        git \
        libc6-dev \
        libcurl4-gnutls-dev \
        libfreetype6-dev \
        libgd-dev \
        libmcrypt-dev \
        libmcrypt4 \
        libonig-dev \
        libtool \
        libwebp-dev \
        libxml2-dev \
        libxslt1-dev \
        libzip-dev \
        libzip4 \
        unzip \
        wget \
        zip \
        zlib1g-dev \
    && apt-get clean \
    && rm -rf /var/cache/apt /var/lib/apt /var/log/{dpkg.log,apt}

# Configure and install extensions
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
        --with-xpm \
    && docker-php-ext-install \
        bcmath \
        ctype \
        curl \
        dom \
        gd \
        mbstring \
        mysqli \
        pcntl \
        pdo \
        pdo_mysql \
        simplexml \
        xml \
        zip \
    && docker-php-source delete

# Install Redis as native extension
RUN pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /tmp/pear

# Use production PHP
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Enable nginx
RUN sed -i "s/worker_processes /daemon off;\nworker_processes /" /etc/nginx/nginx.conf
COPY ./.cloud/docker/nginx/default.conf /etc/nginx/sites-available/default.conf

#  Install Supervisor configs
COPY ./.cloud/docker/supervisor/*.conf /etc/supervisor/conf.d/
COPY ./.cloud/docker/entrypoint.sh /bin/entrypoint.sh

# Add source code files to WORKDIR
COPY --chown=www-data:www-data . .
RUN cp .env.example .env

# Install composer dependencies
RUN sudo -u www-data -- composer install \
    --classmap-authoritative \
    --ignore-platform-reqs \
    --no-dev \
    --no-interaction \
    --no-plugins \
    --no-progress

# Application port (optional)
EXPOSE 80

# Container start command
ENTRYPOINT ["/bin/entrypoint.sh"]
