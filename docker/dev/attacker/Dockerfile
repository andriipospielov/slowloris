FROM php:7.2-fpm

RUN docker-php-ext-install pcntl

RUN curl -s https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin/ --filename=composer \
    && chmod +x /usr/local/bin/composer \
    && composer selfupdate -q

WORKDIR "/code"
COPY . ./
VOLUME "/code"
