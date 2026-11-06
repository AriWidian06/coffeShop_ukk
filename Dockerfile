# --- STAGE 1: Build Frontend Assets (Vite) ---
FROM node:20-alpine AS frontend-builder
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# --- STAGE 2: Production PHP Apache Server ---
FROM php:8.2-apache

# Install dependencies sistem yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear apt cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install ekstensi PHP untuk MySQL & Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer terbaru
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy seluruh file project dari local/git
COPY . /var/www/html

# SALIN hasil build Vite dari Stage 1 ke folder public/build Laravel
COPY --from=frontend-builder /app/public/build /var/www/html/public/build

# Install dependencies PHP menggunakan Composer
RUN composer install --no-dev --no-interaction --optimize-autoloader

# Atur permission storage dan cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Buat symbolic link storage dan bersihkan cache config/view
RUN php artisan storage:link
RUN php artisan view:clear && php artisan config:clear

# Ubah document root Apache ke folder public Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Aktifkan mod_rewrite Apache untuk routing Laravel
RUN a2enmod rewrite

# Expose port 80 untuk web server
EXPOSE 80

# Jalankan migrate + seeder lalu Apache di foreground
CMD php artisan migrate --seed --force && apache2-foreground