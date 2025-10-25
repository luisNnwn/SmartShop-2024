# Imagen base oficial de PHP 8.2
FROM php:8.2-cli

# Instalar extensiones necesarias (PDO + MySQL)
RUN docker-php-ext-install pdo pdo_mysql

# Definir el directorio de trabajo dentro del contenedor
WORKDIR /var/www/html

# Copiar el contenido del proyecto al contenedor
COPY . .

# Exponer el puerto que Render usa
EXPOSE 10000

# Iniciar el servidor PHP embebido
CMD ["php", "-S", "0.0.0.0:10000", "-t", "."]
