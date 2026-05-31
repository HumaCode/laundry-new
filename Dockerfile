FROM dunglas/frankenphp:1-php8.3

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN install-php-extensions \
    pcntl \
    gd \
    intl \
    pdo_mysql \
    zip \
    opcache \
    bcmath \
    exif

# Set Caddy server name
ENV SERVER_NAME=":8000"

# Set working directory
WORKDIR /app

# Copy the application code
COPY . /app

# Set permissions
RUN chmod -R 775 storage bootstrap/cache

# Expose port
EXPOSE 8000

# Entrypoint to start Laravel built-in server (as Octane is not installed by default in this project)
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
