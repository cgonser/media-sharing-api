FROM php:8.0-fpm-alpine

ADD ./docker/php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf
ADD ./docker/php-fpm/xdebug.ini.disabled /usr/local/etc/php/conf.d/xdebug.ini.disabled

RUN mkdir -p /etc/nginx/conf.d
ADD ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf
VOLUME /etc/nginx/conf.d

RUN set -e; \
         apk add --no-cache \
                acl \
                coreutils \
                curl-dev \
                cyrus-sasl-dev \
                freetype-dev \
                git \
                icu \
                icu-dev \
                imap-dev \
                jpeg-dev \
                libgcrypt-dev \
                libintl \
                libjpeg-turbo \
                libjpeg-turbo-dev \
                libmemcached-dev \
                libmemcached-libs \
                libpng-dev \
                libxml2-dev \
                libxslt-dev \
                libzip-dev \
                postgresql-dev \
                zlib-dev

ENV MEMCACHED_DEPS zlib-dev libmemcached-dev cyrus-sasl-dev

RUN CFLAGS="$CFLAGS -D_GNU_SOURCE" docker-php-ext-install sockets \
    && apk add --no-cache --update rabbitmq-c-dev \
    && apk add --no-cache --update --virtual .phpize-deps $PHPIZE_DEPS \
    && for i in $(seq 1 3); do echo yes | pecl install -o "xdebug" && s=0 && break || s=$? && sleep 1; done; (exit $s) \
    # && pecl install -o -f amqp \
    && echo -ne '\n' | pecl install -f https://github.com/0x450x6c/php-amqp/raw/7323b3c9cc2bcb8343de9bb3c2f31f6efbc8894b/amqp-1.10.3.tgz \
    && docker-php-ext-enable amqp \
    && pecl install memcached \
    && echo "extension=memcached.so" > /usr/local/etc/php/conf.d/20_memcached.ini

ARG MPDECIMAL_VERSION=2.5.1

RUN set -eux; \
        cd /tmp/; \
                curl -sSL -O https://www.bytereef.org/software/mpdecimal/releases/mpdecimal-${MPDECIMAL_VERSION}.tar.gz; \
                tar -xzf mpdecimal-${MPDECIMAL_VERSION}.tar.gz; \
                        cd mpdecimal-${MPDECIMAL_VERSION}; \
                        ./configure; \
                        make; \
                        make install

RUN pecl install decimal \
    && docker-php-ext-enable decimal

RUN rm -rf /usr/share/php8 \
    && rm -rf /tmp/*
#    && apk del .memcached-deps .phpize-deps

RUN mkdir -p /var/log/newrelic /var/run/newrelic && \
    touch /var/log/newrelic/php_agent.log /var/log/newrelic/newrelic-daemon.log && \
    chmod -R g+ws /tmp /var/log/newrelic/ /var/run/newrelic/ && \
    chown -R 1001:0 /tmp /var/log/newrelic/ /var/run/newrelic/ && \
    rm -rf /etc/dpkg && \

    # Download and install Newrelic binary
    export NEWRELIC_VERSION=$(curl -sS https://download.newrelic.com/php_agent/release/ | sed -n 's/.*>\(.*linux-musl\).tar.gz<.*/\1/p') && \
    cd /tmp && curl -sS "https://download.newrelic.com/php_agent/release/${NEWRELIC_VERSION}.tar.gz" | gzip -dc | tar xf - && \
    cd "${NEWRELIC_VERSION}" && \
    NR_INSTALL_SILENT=true ./newrelic-install install && \
    rm -f /var/run/newrelic-daemon.pid && \
    rm -f /tmp/.newrelic.sock

ADD ./docker/php-fpm/newrelic.ini /usr/local/etc/php/conf.d/newrelic.ini

RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd pdo_pgsql intl exif zip bcmath

RUN apk del .phpize-deps

WORKDIR /app
ARG APP_ENV=prod

COPY composer.json composer.lock symfony.lock ./
COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN set -eux; \
    composer install --no-dev --no-scripts ; \
    composer clear-cache

COPY .env.dist ./.env
COPY bin bin/
COPY config config/
COPY docker docker/
COPY public public/
COPY migrations migrations/
COPY src src/
COPY templates templates/

RUN set -eux; \
	mkdir -p var/cache var/log; \
	composer dump-autoload --classmap-authoritative --no-dev; \
	composer run-script --no-dev post-install-cmd; \
	chmod +x bin/console; \
    bin/console cache:clear; \
    bin/console assets:install; \
	sync

VOLUME /app

COPY docker/php-fpm/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint
ENTRYPOINT ["docker-entrypoint"]

CMD ["php-fpm"]
