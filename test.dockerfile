FROM falmar/php:7-fpm-dev
COPY . /var/www/html
RUN cp .env.example .env
CMD ["composer", "test"]
