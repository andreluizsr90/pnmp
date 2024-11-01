# Docker PHP-FPM 8.3 & Nginx 1.26 & MSSQL on Alpine Linux

Repository Reference: https://github.com/TrafeX/docker-php-nginx
Repository Reference: https://github.com/Namoshek/docker-php-mssql


# Instructions

Create a TAG

docker build --tag php-nginx:8.3-ms .

Use Tag

docker run -p 80:8080 -d php-nginx:8.3-ms