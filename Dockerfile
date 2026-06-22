FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    gettext-base \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install pdo_pgsql mbstring zip gd bcmath \
    && a2enmod rewrite headers \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY --from=node:22 /usr/local/bin/node /usr/local/bin/node
COPY --from=node:22 /usr/local/lib/node_modules /usr/local/lib/node_modules

RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

COPY package*.json ./
RUN npm install --no-audit --no-fund

COPY . .

RUN npm run build \
    && composer dump-autoload --optimize \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache

COPY docker/apache/000-default.conf.template /etc/apache2/sites-available/000-default.conf.template
COPY render-start.sh /usr/local/bin/render-start

RUN chmod +x /usr/local/bin/render-start

CMD ["render-start"]
