# Use official PHP image with necessary extensions
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy composer files first for caching
COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts --verbose

# Copy full project (including pre-built assets in /public)
COPY . .

# Permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Expose FPM port (internal only)
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
