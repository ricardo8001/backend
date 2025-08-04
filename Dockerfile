# Usando a imagem oficial do PHP com Apache
FROM php:8.2-apache

# Copia os arquivos do repositório para o diretório do servidor web
COPY . /var/www/html/

# Instala dependências adicionais, se necessário (exemplo para extensões de imagem)
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd

# Expõe a porta 80 para acesso HTTP
EXPOSE 80

# Inicia o Apache em primeiro plano
CMD ["apache2-foreground"]

