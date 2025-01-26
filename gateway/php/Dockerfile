FROM php:8.4-fpm

# Install system dependencies efficiently
RUN apt-get update && apt-get install -y \
    g++ \
    git \
    libicu-dev \
    make \
    libzip-dev \
    zlib1g-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure zip \
    && docker-php-ext-install -j$(nproc) \
        intl \
        opcache \
        pdo \
        pdo_mysql \
        zip \
    && pecl install apcu \
    && docker-php-ext-enable apcu

# Set working directory
WORKDIR /var/www/project

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Install Symfony CLI
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash \
    && apt-get update \
    && apt-get install symfony-cli \
    && rm -rf /var/lib/apt/lists/*