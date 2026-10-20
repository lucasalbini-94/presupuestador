FROM php:8.2-apache

# Instalar extensiones necesarias de MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Habilitar mod_rewrite si usas reescritura de URLs
RUN a2enmod rewrite

# Copiar el código del proyecto al directorio de Apache
COPY . /var/www/html/

# Asignar permisos al servidor web
RUN chown -R www-data:www-data /var/www/html

# 5. Iniciar Apache sustituyendo el puerto 80 por el $PORT que asigna Railway
CMD sed -i "s/80/${PORT:-80}/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf && apache2-foreground