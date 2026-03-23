FROM php:8.3-cli

WORKDIR /var/www/html

# System packages + PHP extensions needed by Laravel + Postgres
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    ca-certificates \
    gnupg \
    libpq-dev \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && rm -rf /var/lib/apt/lists/*

# Composer binary
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install PHP dependencies first (better layer cache)
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Install Node dependencies first (better layer cache)
COPY package.json package-lock.json ./
RUN npm ci

# Copy app and build assets
COPY . .
RUN npm run build

# Ensure runtime dirs exist
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache

EXPOSE 10000

# Run migration before starting app on each deploy/restart
CMD ["sh", "-c", "php artisan migrate --force && php -S 0.0.0.0:${PORT:-10000} -t public"]
