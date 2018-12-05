.SILENT:
.PHONY: build test

## Colors
COLOR_RESET   = \033[0m
COLOR_INFO    = \033[32m
COLOR_COMMENT = \033[33m

## Help
help:
	printf "${COLOR_COMMENT}Usage:${COLOR_RESET}\n"
	printf " make [target]\n\n"
	printf "${COLOR_COMMENT}Available targets:${COLOR_RESET}\n"
	awk '/^[a-zA-Z\-\_0-9\.@]+:/ { \
		helpMessage = match(lastLine, /^## (.*)/); \
		if (helpMessage) { \
			helpCommand = substr($$1, 0, index($$1, ":")); \
			helpMessage = substr(lastLine, RSTART + 3, RLENGTH); \
			printf " ${COLOR_INFO}%-16s${COLOR_RESET} %s\n", helpCommand, helpMessage; \
		} \
	} \
	{ lastLine = $$0 }' $(MAKEFILE_LIST)

###############
# COMMANDE #
###############

## to clear the cache
app-clear-cache:
	rm -rf cache/*

## to install app
app-install:
	docker-compose up -d
	docker-compose exec php composer install
	docker-compose exec php chown -R www-data:www-data var/cache && rm -rf var/cache/*
	docker-compose exec php chown -R www-data:www-data var/logs
	docker-compose exec php php bin/console doctrine:schema:update --force 2>/dev/null; true
	docker-compose exec php php bin/console cache:clear 2>/dev/null; true

## to create and start all the containers
infra-up:
	docker-compose up --build -d

## to stop all the containers
infra-stop:
	docker-compose stop

## to open a bash session in the php_fpm container
infra-shell-php-fpm:
	docker-compose exec php bash

## to open a bash session in the backend container
infra-shell-backend:
	docker-compose exec apache bash

## to stop and remove containers, networks, images
infra-clean:
	docker-compose down --rmi all

## to clean and up all
infra-rebuild:
	make infra-clean infra-up

## Fixes permission on cache & logs
app-permissions-fix:
	docker-compose exec php chmod -R 777 ./
