FROM php:8.2-cli

# Install dependencies Laravel
RUN apt-get update && apt-get install -y \
    unzip \
    libpq-dev \
    libzip-dev \
    supervisor \
    && docker-php-ext-install pdo pdo_mysql zip pcntl

WORKDIR /var/www/html

# Supervisor config
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]