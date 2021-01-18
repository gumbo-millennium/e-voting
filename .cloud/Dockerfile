FROM php:8.0-fpm AS main

# Install container dependencies
RUN apt-get update \
    && apt-get install -y \
        git \
        moreutils \
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
    && docker-php-ext-install -j "$( nproc )" \
        bcmath \
        ctype \
        curl \
        dom \
        gd \
        intl \
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

# Copy PHP-FPM config and self test
COPY ./.cloud/docker/fpm/docker-pool.conf $PHP_INI_DIR/../php-fpm.d/zz-docker.conf
RUN php-fpm --test

# Enable nginx and self test
RUN sed -i "s/worker_processes /daemon off;\nworker_processes /" /etc/nginx/nginx.conf
COPY ./.cloud/docker/nginx/default.conf /etc/nginx/sites-available/default
RUN nginx -t

#  Install Supervisor configs
COPY ./.cloud/docker/supervisor/*.conf /etc/supervisor/conf.d/
COPY ./.cloud/docker/entrypoint.sh /bin/entrypoint.sh

# Set working directory and assign proper permissions
WORKDIR /var/www/laravel
RUN chown www-data:www-data /var/www/laravel

# Add files and directories to image
COPY --chown=www-data:www-data . .

# Copy a default environment file
RUN cp .env.example .env

# Install composer dependencies
RUN sudo -u www-data -- composer install \
    --ignore-platform-req=php \
    --classmap-authoritative \
    --no-dev \
    --no-interaction \
    --no-plugins \
    --no-progress

# Application port (optional)
EXPOSE 80

# Container start command
ENTRYPOINT ["/bin/entrypoint.sh"]

FROM main AS google-cloud-run

# Configure Google flag
ENV GOOGLE_CLOUD=run

# Configure app
RUN APP_NAME=e-voting \
    APP_ENV=prod \
    APP_DEBUG=false \
    APP_BETA=false \
    APP_URL=http://localhost

# Logging config
ENV LOG_CHANNEL=stackdriver \
    LOG_LEVEL=info

# MySQL config
ENV DB_CONNECTION=mysql

# Driver config
ENV BROADCAST_DRIVER=log \
    CACHE_DRIVER=database \
    QUEUE_CONNECTION=database \
    SESSION_DRIVER=database \
    SESSION_LIFETIME=300

# Mail config
ENV MAIL_MAILER=smtp \
    MAIL_HOST=smtp-relay.gmail.com \
    MAIL_PORT=587

# Google Cloud Run
ENV APP_STORAGE=/tmp \
    VIEW_COMPILED_PATH=/tmp

# Remove the environment file
RUN rm .env

# Install opcache
RUN docker-php-ext-install -j "$( nproc )" opcache

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
