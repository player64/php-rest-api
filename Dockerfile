FROM php:8.1-cli

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Install PDO extension for PHP
RUN docker-php-ext-install pdo pdo_mysql

# run the php script
CMD ["php", "-S", "0.0.0.0:8080", "-t", "/var/www/html/"]
