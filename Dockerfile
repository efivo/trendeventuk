# ============================================================
#  Trend Events — single image for Coolify
#  Stage 1 builds the React site, stage 2 serves it + the PHP API with Apache.
# ============================================================
FROM node:22-alpine AS build
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
# Optional: VITE_API_URL (default "/api" = same domain, which is what we want)
ARG VITE_API_URL
ENV VITE_API_URL=${VITE_API_URL}
RUN npm run build

FROM php:8.3-apache
RUN docker-php-ext-install pdo_mysql \
 && a2enmod rewrite expires deflate headers setenvif \
 && sed -ri 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf

COPY docker/php.ini /usr/local/etc/php/conf.d/trendevents.ini
COPY docker/apache.conf /etc/apache2/conf-enabled/trendevents.conf

WORKDIR /var/www/html
COPY --from=build /app/dist/ ./
COPY api/index.php api/lib.php api/admin.php api/content.php api/.htaccess ./api/
# config.php reads everything from environment variables (set them in Coolify)
COPY docker/config.php ./api/config.php

RUN mkdir -p /var/www/html/uploads /var/www/trend_sessions \
 && chown -R www-data:www-data /var/www/html/uploads /var/www/trend_sessions \
 && chmod 700 /var/www/trend_sessions

VOLUME ["/var/www/html/uploads", "/var/www/trend_sessions"]
EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
  CMD php -r 'exit(@file_get_contents("http://localhost/api/") === false ? 1 : 0);'
