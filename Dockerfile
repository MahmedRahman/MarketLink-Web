FROM php:8.2-apache

WORKDIR /var/www/html

# 1) أدوات ونبني امتدادات PHP المطلوبة (SQLite + ZIP + GD)
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    && docker-php-ext-install pdo_sqlite bcmath zip \
    # تهيئة وبناء GD مع دعم JPEG و FreeType
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install gd \
    && rm -rf /var/lib/apt/lists/*

# 2) Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 3) نسخ composer files أولاً
COPY composer.json composer.lock ./

# 4) تثبيت الباكدجات
RUN composer install --no-interaction --prefer-dist --no-dev --optimize-autoloader

# 5) نسخ باقي الكود
COPY . .

# 6) تحضير قاعدة بيانات SQLite والصلاحيات
RUN mkdir -p database \
    && touch database/database.sqlite \
    && chown -R www-data:www-data storage bootstrap/cache database

# 7) تشغيل Laravel بالسيرفر المدمج
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]

EXPOSE 8000