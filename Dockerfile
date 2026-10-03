FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

# Instalar dependencias del sistema y extensiones de PHP para MySQL y JWT
RUN apk add --no-cache \
    bash \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    oniguruma-dev \
    linux-headers \
    $PHPIZE_DEPS \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Copiar archivos del proyecto
COPY . /var/www/html

# Exponer el puerto para el servidor integrado de desarrollo de PHP / FPM
EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
