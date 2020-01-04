FROM composer

FROM php:7-fpm-alpine

COPY --from=composer /usr/bin/composer /usr/local/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV APCU_VERSION 5.1.18
ENV XDEBUG_VERSION 2.8.0

RUN apk add --no-cache \
        ca-certificates \
		acl \
		file \
		gettext \
        icu-libs \
        git \
        unzip \
		libgmpxx \
        postgresql-client \
        libzip \
        zlib-dev
RUN apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
		gmp-dev \
		libzip-dev \
		postgresql-dev \
        zlib-dev \
        icu-dev
RUN docker-php-ext-configure zip;
RUN docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        intl \
		bcmath \
		gmp \
        zip \
        pdo_mysql
RUN pecl install apcu-${APCU_VERSION}
RUN pecl install xdebug-${XDEBUG_VERSION}
RUN pecl clear-cache
RUN docker-php-ext-enable apcu
RUN docker-php-ext-enable opcache
RUN docker-php-ext-enable xdebug
RUN echo "short_open_tag = off" >> /usr/local/etc/php/php.ini
RUN echo "xdebug.remote_enable=1" >> /usr/local/etc/php/php.ini
RUN echo "xdebug.remote_port=9000" >> /usr/local/etc/php/php.ini
RUN echo "date.timezone = Europe/Paris" >> /usr/local/etc/php/conf.d/symfony.ini
RUN echo "opcache.max_accelerated_files = 20000" >> /usr/local/etc/php/conf.d/symfony.ini
RUN echo "realpath_cache_size=4096K" >> /usr/local/etc/php/conf.d/symfony.ini
RUN echo "memory_limit=-1" >> /usr/local/etc/php/conf.d/symfony.ini
RUN echo "realpath_cache_ttl=600" >> /usr/local/etc/php/conf.d/symfony.ini

RUN runDeps="$( \
		scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
			| tr ',' '\n' \
			| sort -u \
			| awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
	)";
RUN apk add --no-cache --virtual .api-phpexts-rundeps $runDeps;

RUN apk del .build-deps
RUN apk add gosu --update --no-cache --repository http://dl-3.alpinelinux.org/alpine/edge/testing/ --allow-untrusted
RUN addgroup bar
RUN adduser -D -h /home -s /bin/sh -G bar foo

ADD entrypoint.sh /entrypoint

ENTRYPOINT ["/entrypoint"]
