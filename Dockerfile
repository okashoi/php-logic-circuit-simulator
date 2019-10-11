FROM php:7.3.10

RUN pecl install swoole && \
    docker-php-ext-enable swoole

COPY src /usr/src/app
WORKDIR /usr/src/app

CMD ["php", "index.php"]
