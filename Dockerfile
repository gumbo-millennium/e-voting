# vim: set ft=dockerfile :
# Configuration file for Docker

# Configuration file for Docker
FROM php:7.4-apache

# Install dependencies for extensions
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
    && rm -rf /var/cache/apt /var/lib/apt /var/log/*

# Configure and install extensions
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
        --with-xpm \
    && docker-php-ext-install mbstring \
    && docker-php-ext-install \
        bcmath \
        curl \
        dom \
        gd \
        json \
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

# Configure PHP for Cloud Run.
# Precompile PHP code with opcache.
RUN docker-php-ext-install -j "$(nproc)" opcache
RUN set -ex; \
    { \
        echo "; Cloud Run enforces memory & timeouts"; \
        echo "memory_limit = -1"; \
        echo "max_execution_time = 0"; \
        echo "; File upload at Cloud Run network limit"; \
        echo "upload_max_filesize = 32M"; \
        echo "post_max_size = 32M"; \
        echo "; Configure Opcache for Containers"; \
        echo "opcache.enable = On"; \
        echo "opcache.validate_timestamps = Off"; \
        echo "; Configure Opcache Memory (Application-specific)"; \
        echo "opcache.memory_consumption = 32"; \
    } > "$PHP_INI_DIR/conf.d/cloud-run.ini"

# Configure Apache and add brotli mods
ENV APACHE_RUN_USER=cloud APACHE_RUN_GROUP=cloud
COPY ./.cloud/apache-mods/*.conf /etc/apache2/mods-available/
RUN a2enmod \
        authz_core \
        brotli \
        expires \
        headers \
        proxy_http \
        rewrite

# Use the PORT environment variable in Apache configuration files.
# https://cloud.google.com/run/docs/reference/container-contract#port
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Change root to /var/www/laravel/public (we'll install in /var/www)
ENV APACHE_DOCUMENT_ROOT=/var/www/laravel/public
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf && \
    sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

# Add cloud user
RUN groupadd \
        --gid 1000 \
        cloud \
    && useradd \
        --home-dir /var/www \
        --no-create-home \
        --uid 1000 \
        --gid 1000 \
        --groups www-data \
        --password "" \
        cloud

# Change tempdir
RUN mkdir -p /usr/local/tmp/cloud && \
        chown cloud:cloud /usr/local/tmp/cloud
ENV TMPDIR=/usr/local/tmp/cloud

# Install code
WORKDIR /var/www/laravel
COPY --chown=cloud:cloud . ./
