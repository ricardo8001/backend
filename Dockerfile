# Escolha uma imagem oficial do PHP com Apache
FROM php:8.2-apache

# Copia os arquivos para o diretório do servidor web
COPY . /var/www/html/

# Instala as dependências do PHP, se necessário
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd

# Expõe a porta 80
EXPOSE 80

# Comando para rodar o Apache em primeiro plano
CMD ["apache2-foreground"]
