PROJECT_NAME=itinair-api
SERVICE_TARGET := php

# export such that its passed to shell functions for Docker to pick up.
export PROJECT_NAME

.PHONY: help start stop build jwt-generate-keys test test-docker test-api test-integration

help:
	@echo ''
	@echo 'Usage: make [TARGET] [EXTRA_ARGUMENTS]'
	@echo 'Targets:'
	@echo '  start				start services'
	@echo '  stop				stop services'
	@echo '  build				build docker image'
	@echo '  jwt-generate-keys	generate JWT keys'
	@echo '  test				run all tests'
	@echo '  test-docker		run all tests inside the docker PHP container'
	@echo '  test-api			run API tests'
	@echo '  test-integration	run Integration tests'
	@echo ''

start:
	docker-compose -p $(PROJECT_NAME) up -d

stop:
	docker-compose -p $(PROJECT_NAME) down

build:
	docker-compose build --no-cache $(SERVICE_TARGET)

jwt-generate-keys:
	docker-compose -p $(PROJECT_NAME) exec $(SERVICE_TARGET) bin/console lexik:jwt:generate-keypair --overwrite -n

test:
	php bin/console --env=test doctrine:database:drop --force -q
	php bin/console --env=test doctrine:database:create -q
	php bin/console --env=test doctrine:schema:create -q
	php bin/console --env=test lexik:jwt:generate-keypair --overwrite -n
	XDEBUG_MODE=off php ./vendor/bin/phpunit

test-docker:
	docker-compose exec -e XDEBUG_MODE=off $(SERVICE_TARGET) bin/console --env=test doctrine:database:drop --force -q
	docker-compose exec -e XDEBUG_MODE=off $(SERVICE_TARGET) bin/console --env=test doctrine:database:create -q
	docker-compose exec -e XDEBUG_MODE=off $(SERVICE_TARGET) bin/console --env=test doctrine:schema:create -q
	docker-compose exec -e XDEBUG_MODE=off $(SERVICE_TARGET) bin/console --env=test lexik:jwt:generate-keypair --overwrite -n
	docker-compose exec -e XDEBUG_MODE=off $(SERVICE_TARGET) vendor/bin/phpunit

test-integration:
	php bin/console --env=test doctrine:database:drop --force -q
	php bin/console --env=test doctrine:database:create -q
	php bin/console --env=test doctrine:schema:create -q
	php bin/console --env=test lexik:jwt:generate-keypair --overwrite -n
	XDEBUG_MODE=off php ./vendor/bin/phpunit tests/Integration

test-api:
	php bin/console --env=test doctrine:database:drop --force -q
	php bin/console --env=test doctrine:database:create -q
	php bin/console --env=test doctrine:schema:create -q
	php bin/console --env=test lexik:jwt:generate-keypair --overwrite -n
	XDEBUG_MODE=off php ./vendor/bin/phpunit tests/Api
