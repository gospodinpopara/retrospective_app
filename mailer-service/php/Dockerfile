FROM php:8.4-fpm

# Install system dependencies efficiently
RUN apt-get update && apt-get install -y --no-install-recommends \
    g++ \
    git \
    libicu-dev \
    make \
    libzip-dev \
    zlib1g-dev \
    librabbitmq-dev \
    acl \
    curl \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean

# Install Node.js (version 22)
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean

# Install PHP extensions
RUN docker-php-ext-configure zip \
    && docker-php-ext-install -j$(nproc) \
        intl \
        opcache \
        pdo \
        pdo_mysql \
        zip

RUN pecl install apcu \
    && docker-php-ext-enable apcu \
    && pecl install amqp \
    && docker-php-ext-enable amqp

# Set working directory
WORKDIR /var/www/project

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Install Symfony CLI
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash \
    && apt-get update \
    && apt-get install -y symfony-cli \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean