FROM php:8.2-apache

# Instalar extensiones necesarias de MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Habilitar mod_rewrite si usas reescritura de URLs
RUN a2enmod rewrite

# Copiar el código del proyecto al directorio de Apache
COPY . /var/www/html/

# Asignar permisos al servidor web
RUN chown -R www-data:www-data /var/www/html

# Exponer el puerto estándar que usa Railway
EXPOSE 80