#!/bin/bash

set -ex

# Prepare instance

docker-compose run -T php-fpm composer install --no-dev --optimize-autoloader
docker-compose run -T commands php bin/console d:m:m --no-interaction
docker-compose run -T commands chmod 777 -R ./var
docker-compose run -T commands php bin/console cache:warmup