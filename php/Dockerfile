FROM php:8.1-fpm

#php instalacija
RUN apt-get update && apt-get install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip \
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip

WORKDIR /var/www/project

#composer instalacija
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

#symfony
RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony

#nodejs
RUN curl -fsSL https://deb.nodesource.com/setup_16.x | bash -
RUN apt-get install -y nodejs
RUN npm install --global yarn

# PCNTL extension for CHRON
ADD https://raw.githubusercontent.com/mlocati/docker-php-extension-installer/master/install-php-extensions /usr/local/bin/
RUN chmod uga+x /usr/local/bin/install-php-extensions && sync && install-php-extensions pcntl

#ARG DEBIAN_FRONTEND=noninteractive
#ENV TZ=Europe/Belgrade
#RUN apt-get install -y tzdata
RUN apt-get update -qq && apt-get install -y \
    # packages required by wkhtmlto*:
    xfonts-base \
    xfonts-75dpi \
    pdftk # -> only if needed... \
    # ...other custom packages...

RUN curl -L#o wkhtmltopdf.deb https://github.com/wkhtmltopdf/packaging/releases/download/0.12.6-1/wkhtmltox_0.12.6-1.buster_amd64.deb
RUN dpkg -i wkhtmltopdf.deb; apt-get install -y -f

# Remember to clean your package manager cache to reduce your custom image size...
RUN apt-get clean all \
    && rm -rvf /var/lib/apt/lists/
