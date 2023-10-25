FROM ubuntu:22.04

LABEL maintainer="Taylor Otwell"

ARG WWWGROUP
ARG NODE_VERSION=16
ARG POSTGRES_VERSION=14

WORKDIR /var/www/html

ENV DEBIAN_FRONTEND noninteractive
ENV TZ=UTC

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Add cron
RUN apt-get update \
    && apt-get install -y cron

RUN apt-get update \
    && apt-get install -y gnupg gosu curl ca-certificates zip unzip git supervisor sqlite3 libcap2-bin libpng-dev python2 nano systemctl \
    && mkdir -p ~/.gnupg \
    && chmod 600 ~/.gnupg \
    && echo "disable-ipv6" >> ~/.gnupg/dirmngr.conf \
    && echo "keyserver hkp://keyserver.ubuntu.com:80" >> ~/.gnupg/dirmngr.conf \
    && gpg --recv-key 0x14aa40ec0831756756d7f66c4f4ea0aae5267a6c \
    && gpg --export 0x14aa40ec0831756756d7f66c4f4ea0aae5267a6c > /usr/share/keyrings/ppa_ondrej_php.gpg \
    && echo "deb [signed-by=/usr/share/keyrings/ppa_ondrej_php.gpg] https://ppa.launchpadcontent.net/ondrej/php/ubuntu jammy main" > /etc/apt/sources.list.d/ppa_ondrej_php.list \
    && apt-get update \
    && apt-get install -y php8.1-cli php8.1-dev \
       php8.1-pgsql php8.1-sqlite3 php8.1-gd \
       php8.1-curl \
       php8.1-imap php8.1-mysql php8.1-mbstring \
       php8.1-xml php8.1-zip php8.1-bcmath php8.1-soap \
       php8.1-intl php8.1-readline \
       php8.1-ldap \
       php8.1-msgpack php8.1-igbinary php8.1-redis php8.1-swoole \
       php8.1-memcached php8.1-pcov php8.1-xdebug \
    && php -r "readfile('https://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer \
    && curl -sLS https://deb.nodesource.com/setup_$NODE_VERSION.x | bash - \
    && apt-get install -y nodejs \
    && curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | gpg --dearmor | tee /usr/share/keyrings/yarn.gpg >/dev/null \
    && echo "deb [signed-by=/usr/share/keyrings/yarn.gpg] https://dl.yarnpkg.com/debian/ stable main" > /etc/apt/sources.list.d/yarn.list \
    && curl -sS https://www.postgresql.org/media/keys/ACCC4CF8.asc | gpg --dearmor | tee /usr/share/keyrings/pgdg.gpg >/dev/null \
    && echo "deb [signed-by=/usr/share/keyrings/pgdg.gpg] http://apt.postgresql.org/pub/repos/apt jammy-pgdg main" > /etc/apt/sources.list.d/pgdg.list \
    && apt-get update \
    && apt-get install -y yarn \
    && apt-get install -y mysql-client \
    && apt-get install -y postgresql-client-$POSTGRES_VERSION \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN setcap "cap_net_bind_service=+ep" /usr/bin/php8.1

RUN groupadd --force -g 1330 sail
RUN useradd -ms /bin/bash --no-user-group -g 1330 -u 1337 sail

COPY docker/prod/start-container /usr/local/bin/start-container
COPY docker/prod/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/prod/php.ini /etc/php/8.1/cli/conf.d/99-sail.ini
RUN chmod +x /usr/local/bin/start-container

# Add cron
COPY docker/prod/scheduler /etc/cron.d/scheduler

RUN chmod 0644 /etc/cron.d/scheduler \
    && crontab /etc/cron.d/scheduler

RUN chmod -Rf 777 /var/www

COPY app /var/www/html/app
COPY bootstrap /var/www/html/bootstrap
COPY config /var/www/html/config
COPY database /var/www/html/database
COPY lang /var/www/html/lang
COPY public /var/www/html/public
COPY resources /var/www/html/resources
COPY routes /var/www/html/routes
COPY artisan /var/www/html/artisan
COPY composer.json /var/www/html/composer.json
COPY package.json /var/www/html/package.json
COPY phpcs.xml /var/www/html/phpcs.xml
COPY phpstan.neon /var/www/html/phpstan.neon
COPY webpack.mix.js /var/www/html/webpack.mix.js

COPY docker/prod/env-prod /var/www/html/.env

RUN mkdir -p storage/app/public
RUN mkdir -p storage/framework/cache/data
RUN mkdir -p storage/framework/sessions
RUN mkdir -p storage/framework/testing
RUN mkdir -p storage/framework/views
RUN mkdir -p storage/logs

RUN composer install --ignore-platform-reqs
RUN php artisan key:generate

RUN chown -R sail:sail *
RUN chown sail:sail .*

EXPOSE 80

ENTRYPOINT ["start-container"]
