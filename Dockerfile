FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libzip-dev

RUN docker-php-ext-install zip
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www
COPY . /var/www/skills
RUN sed -i 's/\r$//' /var/www/skills/entrypoint.sh && chmod +x /var/www/skills/entrypoint.sh
EXPOSE 8080
ENTRYPOINT ["/var/www/skills/entrypoint.sh"]