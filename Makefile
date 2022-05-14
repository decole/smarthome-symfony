up:
	docker-compose up -d

down:
	docker-compose down

build:
	docker-compose build

down-clear:
	docker-compose down -v --remove-orphans

console-in:
	docker-compose exec php-fpm bash

env:
	docker-compose exec php-fpm cp -n .env.local.example .env.local
	docker-compose exec php-fpm cp -n .env.local.example .env.test.local

composer-install:
	docker-compose exec php-fpm composer install

migration:
	docker-compose exec php-fpm php bin/console d:m:m --no-interaction

new-migration:
	docker-compose exec php-fpm php bin/console d:m:diff

fixture:
	docker-compose exec php-fpm php bin/console d:f:l --no-interaction --purge-with-truncate

test-clean-output:
	docker-compose exec php-fpm php bin/codecept clean

ps:
	docker-compose ps

perm:
	sudo chown -R ${USER}:${USER} var
	sudo chown -R ${USER}:${USER} vendor
	sudo chown -R ${USER}:${USER} tests
	sudo chown -R ${USER}:${USER} .psalm
