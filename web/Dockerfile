# Changes needed on the Dockerfile from PHP 7.3 to 7.4 :
# - gd configure options changed (from "--with-jpeg-dir=/usr/include/" to "--with-jpeg")
# - it is now required to install oniguruma lib for mbstring

# Pull base image.
FROM php:8.0.1-apache

RUN apt-get clean
RUN apt-get update

RUN apt-get install --fix-missing \
  wget \
  apt-transport-https \
  lsb-release \
  ca-certificates \
  gnupg2 \
  ruby-dev \
  rubygems \
  imagemagick \
  graphviz \
  memcached \
  libmemcached-tools \
  libmemcached-dev \
  libjpeg62-turbo-dev \
  libmcrypt-dev \
  libxml2-dev \
  libxslt1-dev \
  default-mysql-client \
  sudo \
  git \
  vim \
  zip \
  wget \
  htop \
  iputils-ping \
  dnsutils \
  linux-libc-dev \
  libyaml-dev \
  libpng-dev \
  zlib1g-dev \
  libzip-dev \
  libicu-dev \
  libpq-dev \
  bash-completion \
  libldap2-dev \
  libssl-dev \
  libonig-dev \
  libbz2-dev \
  rsync \
  curl \
  -y

# postgresql-client-9.5
#RUN wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | sudo apt-key add - && echo "deb https://apt.postgresql.org/pub/repos/apt/ buster-pgdg main" >> /etc/apt/sources.list && apt-get update && apt-get install -y postgresql-client-11

RUN pecl install mcrypt-1.0.4 && \
  docker-php-ext-enable mcrypt

# Create new web user for apache and grant sudo without password
RUN useradd web -d /var/www -g www-data -s /bin/bash
RUN usermod -aG sudo web
RUN echo 'web ALL=(ALL) NOPASSWD:ALL' >> /etc/sudoers

# Install YAML extension
RUN pecl install yaml-2.2.1 && echo "extension=yaml.so" > /usr/local/etc/php/conf.d/ext-yaml.ini

# Install APCu extension
RUN pecl install apcu

# Installation of Composer
RUN cd /usr/src && curl -sS https://getcomposer.org/installer | php
RUN cd /usr/src && mv composer.phar /usr/bin/composer

# Apache2 config
COPY ./web/config/apache2.conf /etc/apache2
COPY ./web/core/envvars /etc/apache2
COPY ./web/core/other-vhosts-access-log.conf /etc/apache2/conf-enabled/
RUN rm /etc/apache2/sites-enabled/000-default.conf

#added for AH00111 Error
ENV APACHE_RUN_USER  www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR   /var/log/apache2
ENV APACHE_PID_FILE  /var/run/apache2/apache2.pid
ENV APACHE_RUN_DIR   /var/run/apache2
ENV APACHE_LOCK_DIR  /var/lock/apache2
ENV APACHE_LOG_DIR   /var/log/apache2

# Install php extensions + added mysqli install

RUN docker-php-ext-install bcmath
RUN docker-php-ext-install calendar
RUN docker-php-ext-install ctype
RUN apt-get install libcurl4-openssl-dev
RUN docker-php-ext-install curl
#RUN docker-php-ext-install date
RUN docker-php-ext-install dom
RUN docker-php-ext-install exif
RUN docker-php-ext-install fileinfo
RUN docker-php-ext-install filter
RUN docker-php-ext-install ftp
RUN docker-php-ext-install gd
RUN docker-php-ext-install gettext
RUN docker-php-ext-install gmp
#RUN docker-php-ext-install hash
RUN docker-php-ext-install iconv
RUN docker-php-ext-install intl
#RUN docker-php-ext-install json
RUN docker-php-ext-install ldap
#RUN docker-php-ext-install libxml
RUN docker-php-ext-install mbstring
RUN docker-php-ext-install mysqli
#RUN docker-php-ext-install mysqlnd
#RUN docker-php-ext-install openssl
RUN docker-php-ext-install pcntl
#RUN docker-php-ext-install pcre
RUN docker-php-ext-install pdo
RUN docker-php-ext-install pdo_mysql

RUN apt-get install -y libbz2-dev sqlite3 libsqlite3-dev libssl-dev libcurl4-openssl-dev libjpeg-dev libonig-dev libreadline-dev libtidy-dev libxslt-dev libzip-dev
RUN apt-get install libsqlite3-dev

RUN docker-php-ext-install pdo_sqlite
RUN docker-php-ext-install phar
RUN docker-php-ext-install posix

RUN apt-get update
#RUN apt-get install libedit

#RUN docker-php-ext-install readline
#RUN docker-php-ext-install reflection
RUN docker-php-ext-install session
RUN docker-php-ext-install simplexml
RUN docker-php-ext-install soap
RUN docker-php-ext-install sockets

RUN apt install libsodium-dev

RUN docker-php-ext-install sodium
#RUN docker-php-ext-install spl
#RUN docker-php-ext-install sqlite3

#RUN apt-get install libargon2 libargon2-devel

#RUN docker-php-ext-install standard
RUN docker-php-ext-install tidy
RUN docker-php-ext-install tokenizer
RUN docker-php-ext-install xml
#RUN docker-php-ext-install xmlreader
RUN docker-php-ext-install xmlwriter
RUN docker-php-ext-install xsl
RUN docker-php-ext-install opcache
RUN docker-php-ext-install zip
#RUN docker-php-ext-install zlib

RUN docker-php-ext-configure gd --with-jpeg
RUN docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/

# Custom Opcache
RUN ( \
  echo "opcache.enable=1"; \
  echo "opcache.jit_buffer_size=256M"; \
  echo "opcache.memory_consumption=128"; \
  echo "opcache.interned_strings_buffer=8"; \
  echo "opcache.max_accelerated_files=20000"; \
  echo "opcache.revalidate_freq=5"; \
  echo "opcache.fast_shutdown=1"; \
  echo "opcache.enable_cli=1"; \
  ) >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini

# Apache encore de la config
RUN rm -rf /var/www/html && \
  mkdir -p /var/lock/apache2 /var/run/apache2 /var/log/apache2 /var/www/html && \
  chown -R web:www-data /var/lock/apache2 /var/run/apache2 /var/log/apache2 /var/www/html
RUN a2enmod rewrite expires ssl && service apache2 restart

# install msmtp
RUN set -x \
    && DEBIAN_FRONTEND=noninteractive \
    && apt-get update && apt-get install -y --no-install-recommends msmtp && rm -r /var/lib/apt/lists/*
ADD ./web/core/msmtprc.conf /usr/local/etc/msmtprc
ADD ./web/core/php-smtp.ini /usr/local/etc/php/conf.d/php-smtp.ini

# #install ssmtp
# RUN DEBIAN_FRONTEND=noninteractive apt-get install --fix-missing -y ssmtp && rm -r /var/lib/apt/lists/*
# ADD core/ssmtp.conf /etc/ssmtp/ssmtp.conf
# ADD core/php-smtp.ini /usr/local/etc/php/conf.d/php-smtp.ini

# Install Drupal Console for Drupal 8
#RUN curl https://drupalconsole.com/installer -L -o drupal.phar && mv drupal.phar /usr/local/bin/drupal && chmod +x /usr/local/bin/drupal


RUN apt-get update
RUN apt-get install openssl build-essential libssl-dev libxrender-dev git-core libx11-dev libxext-dev libfontconfig1-dev libfreetype6-dev fontconfig -y

# Install WKHTMLTOPDF
#RUN apt-get update
#RUN apt-get install openssl build-essential libssl-dev libxrender-dev git-core libx11-dev libxext-dev libfontconfig1-dev libfreetype6-dev fontconfig -y
#RUN mkdir /var/wkhtmltopdf
#RUN cd /var/wkhtmltopdf && wget https://github.com/wkhtmltopdf/wkhtmltopdf/releases/download/0.12.4/wkhtmltox-0.12.4_linux-generic-amd64.tar.xz && tar xf wkhtmltox-0.12.4_linux-generic-amd64.tar.xz
#RUN cp /var/wkhtmltopdf/wkhtmltox/bin/wkhtmltopdf /bin/wkhtmltopdf && cp /var/wkhtmltopdf/wkhtmltox/bin/wkhtmltoimage /bin/wkhtmltoimage
#RUN chown -R www-data:www-data /var/wkhtmltopdf
#RUN chmod +x /bin/wkhtmltopdf && chmod +x /bin/wkhtmltoimage

# Our apache volume
VOLUME /var/www/html

# create directory for ssh keys
RUN mkdir /var/www/.ssh/
RUN chown -R web:www-data /var/www/

# Set timezone to Europe/Paris
RUN echo "Europe/Prague" > /etc/timezone && dpkg-reconfigure -f noninteractive tzdata

# Expose 80,443 for apache + 9000 pour xdebug
EXPOSE 80 443 9000

# Add web .bashrc config
COPY ./web/config/bashrc /var/www/
RUN mv /var/www/bashrc /var/www/.bashrc
RUN chown www-data:www-data /var/www/.bashrc
RUN echo "source .bashrc" >> /var/www/.profile ;\
    chown www-data:www-data /var/www/.profile

# Add web and root .bashrc config
# When you "docker exec -it" into the container, you will be switched as web user and placed in /var/www/html
RUN echo "exec su - web" > /root/.bashrc && \
    echo ". .profile" > /var/www/.bashrc && \
    echo "alias ll='ls -al'" > /var/www/.profile && \
    echo "cd /var/www/html" >> /var/www/.profile

# Custom php.ini
COPY ./web/config/php.ini /usr/local/etc/php/

# install npm and node.js
RUN curl -sL https://deb.nodesource.com/setup_17.x | sudo bash -
RUN apt-get install -y nodejs

# Set and run a custom entrypoint
COPY ./web/core/docker-entrypoint.sh /
RUN chmod 777 /docker-entrypoint.sh && chmod +x /docker-entrypoint.sh
ENTRYPOINT ["/docker-entrypoint.sh"]

# CMD ["cat", "/etc/apache2/envvars"]
# CMD ["sed", "-n", "39p", "/etc/apache2/apache2.conf"]