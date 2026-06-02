FROM php:8.3-fpm-alpine

# Installiere die notwendigen System-Tools und den PostgreSQL-Treiber für PHP
RUN apk add --no-cache postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql
