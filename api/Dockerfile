FROM php:5-fpm

RUN apt-get update 

RUN apt-get install -y php-pear 
RUN apt-get install -y git 
RUN apt-get install -y vim 
RUN apt-get install -y tree

RUN docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd && \
      docker-php-ext-configure mysql --with-mysql=mysqlnd && \
      docker-php-ext-configure mysqli --with-mysqli=mysqlnd && \
      docker-php-ext-install pdo_mysql && \
      docker-php-ext-install mysqli && \
      docker-php-ext-install mysql

RUN apt-get install -y libz-dev libmemcached-dev gcc \
    && pecl install memcached \
    && docker-php-ext-enable memcached

COPY php.ini /usr/local/etc/php/conf.d/php.ini

RUN mkdir -p -m 0777 /var/log/silex

COPY src /var/www/src
COPY html /var/www/html
COPY vendor /var/www/vendor

COPY composer.* /var/www/
RUN cd /var/www && php composer.phar install

