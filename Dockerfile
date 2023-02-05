FROM php:8.1-cli
WORKDIR /var/www/html
# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Install PDO extension for PHP
RUN docker-php-ext-install pdo pdo_mysql

# run the php script
CMD ["composer", "install"]
