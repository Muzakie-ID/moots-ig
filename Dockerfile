FROM php:8.2-apache

# Install MySQL extension
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set PHP upload limits
RUN echo "upload_max_filesize = 20M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 25M" >> /usr/local/etc/php/conf.d/uploads.ini

# Set Apache limit
RUN echo "LimitRequestBody 20971520" >> /etc/apache2/conf-available/other.conf \
    && a2enconf other

# Set working directory
WORKDIR /var/www/html

# Copy existing files (for development, mount volume in compose instead)
COPY . .

# Expose port 80
EXPOSE 80
