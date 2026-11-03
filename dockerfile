FROM php:8.2-apache

# Install dependencies yang dibutuhkan Laravel, Node.js, dan npm sekaligus
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy semua file project ke dalam container
COPY . /var/www/html

# Install dependencies PHP menggunakan Composer
RUN composer install --no-dev --optimize-autoloader

# Install dependencies Node.js dan build aset Vite (CSS/JS)
RUN npm run build

# Buat symbolic link storage Laravel agar gambar produk bisa diakses
RUN php artisan storage:link
RUN php artisan view:clear && php artisan config:clear

# Atur permission untuk storage dan bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Ubah document root Apache ke folder public Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Aktifkan mod_rewrite Apache untuk routing Laravel
RUN a2enmod rewrite

# Expose port 80 untuk web server
EXPOSE 80

# Jalankan Apache di foreground
CMD ["apache2-foreground"]