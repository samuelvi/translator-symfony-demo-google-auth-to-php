# Use -f to specify the path to the docker-compose file
DOCKER_COMPOSE = docker-compose -f docker/docker-compose.yaml

.PHONY: help build up down restart shell composer-install console test test-coverage rector rector-dry lint cache-clear translate

help:
	@echo "Available commands:"
	@echo ""
	@echo "Docker commands:"
	@echo "  make build             Build or rebuild the Docker images"
	@echo "  make up                Start the services in the background"
	@echo "  make down              Stop and remove the services"
	@echo "  make restart           Restart the services"
	@echo "  make shell             Access the PHP container shell"
	@echo ""
	@echo "Development commands:"
	@echo "  make install           Install Composer dependencies"
	@echo "  make console           Run a Symfony console command (e.g., make console list)"
	@echo "  make cache-clear       Clear the Symfony cache"
	@echo ""
	@echo "Testing commands:"
	@echo "  make test              Run PHPUnit tests"
	@echo "  make test-coverage     Run tests with coverage report (HTML)"
	@echo ""
	@echo "Code quality commands:"
	@echo "  make rector            Run Rector to refactor code"
	@echo "  make rector-dry        Run Rector in dry-run mode (preview changes)"
	@echo "  make lint              Run all code quality checks"
	@echo ""
	@echo "Application commands:"
	@echo "  make translate         Run the translator command (frontend/common)"

build:
	$(DOCKER_COMPOSE) build --no-cache

up:
	$(DOCKER_COMPOSE) up -d

down:
	$(DOCKER_COMPOSE) down

restart: down up

shell:
	$(DOCKER_COMPOSE) exec php-atic-gap-pe bash

install:
	composer install

# Allows running any Symfony command, e.g., `make console list`
console:
	bin/console $(filter-out $@,$(MAKECMDGOALS))

cache-clear:
	bin/console cache:clear

# Testing commands
test:
	bin/phpunit

test-coverage:
	bin/phpunit --coverage-html coverage
	@echo "Coverage report generated in coverage/index.html"

# Code quality commands
rector:
	bin/rector process

rector-dry:
	bin/rector process --dry-run

lint: rector-dry test
	@echo "All code quality checks passed!"

# Application-specific commands
translate:
	bin/console atico:demo:translator --sheet-name=common --book-name=frontend

# Docker-based commands (prefixed with docker-)
docker-install:
	$(DOCKER_COMPOSE) exec php-atic-gap-pe composer install

docker-console:
	$(DOCKER_COMPOSE) exec php-atic-gap-pe bin/console $(filter-out $@,$(MAKECMDGOALS))

docker-test:
	$(DOCKER_COMPOSE) exec php-atic-gap-pe bin/phpunit

docker-rector:
	$(DOCKER_COMPOSE) exec php-atic-gap-pe bin/rector process

docker-translate:
	$(DOCKER_COMPOSE) exec php-atic-gap-pe bin/console atico:demo:translator --sheet-name=common --book-name=frontend

# This is needed to pass arguments to the console command
%:
	@: