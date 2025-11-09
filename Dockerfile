# Imagen base de PHP 8.2 CLI
FROM php:8.2-cli

# Configurar DNS alternativo para evitar fallos de resolución en Render
RUN echo "Acquire::ForceIPv4 \"true\";" > /etc/apt/apt.conf.d/99force-ipv4 && \
    echo "Acquire::http::Proxy \"false\";" > /etc/apt/apt.conf.d/99disable-proxy

# ✅ Instalar dependencias del sistema (incluye cURL y certificados actualizados)
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev \
    libcurl4-openssl-dev ca-certificates \
 && docker-php-ext-install pdo pdo_mysql zip curl \
 && update-ca-certificates --fresh

# Copiar Composer desde la imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definir el directorio de trabajo dentro del contenedor
WORKDIR /var/www/html

# Copiar el contenido del proyecto
COPY . .

# Instalar dependencias PHP (PHPMailer, etc.)
RUN composer install --no-dev --optimize-autoloader

# Exponer el puerto que Render usa
EXPOSE 10000

# Comando para iniciar el servidor embebido de PHP
CMD ["php", "-S", "0.0.0.0:10000", "-t", "."]
