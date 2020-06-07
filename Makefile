.DEFAULT_GOAL := help
.PHONY: help
.SILENT:

GREEN    := $(shell tput -Txterm setaf 2)
WHITE    := $(shell tput -Txterm setaf 7)
YELLOW   := $(shell tput -Txterm setaf 3)
RESET    := $(shell tput -Txterm sgr0)
COMPOSER := $(shell command -v composer 2> /dev/null)

# COMPOSER_HOME & COMPOSER_CACHE_DIR are environment variables which can be used to
# override the home and cache dir so if it isn't set then set it to the default
ifndef COMPOSER_HOME
COMPOSER_HOME := ~/.composer
endif

ifndef COMPOSER_CACHE_DIR
COMPOSER_CACHE_DIR := $(COMPOSER_HOME)/cache
endif

composer: ##@production Install composer locally
ifndef COMPOSER
	curl --silent https://getcomposer.org/installer | php -- --quiet
endif

permissions: ##@production Fix permissions
	chmod 777 storage/logs/ bootstrap/cache/
	chmod 777 storage/framework/cache/ storage/framework/sessions/ storage/framework/views/
	chmod 777 storage/app/mirrors/ storage/app/tmp/ storage/app/public/

migrate: ##@production Migrate the database
	@echo "${GREEN}Migrate the database${RESET}"
	@docker-compose exec php ./artisan migrate

install: composer ##@production Install dependencies
	@$(MAKE) permissions
ifndef COMPOSER
	php composer.phar install --optimize-autoloader --no-dev --prefer-dist --no-interaction --no-suggest
else
	composer install --optimize-autoloader --no-dev  --prefer-dist --no-interaction --no-suggest
endif
	npm install --production

install-dev: ##@development Install dev dependencies
	@$(MAKE) permissions
	@$(MAKE) docker-install-dev

update-deps: ##@development Update dependencies
	@docker-compose run -v $(COMPOSER_CACHE_DIR):/tmp/cache --rm composer update --no-interaction --no-suggest --prefer-dist --no-suggest
	@docker-compose exec node npm upgrade

clean: ##@development Clean cache, logs and other temporary files
	@$(MAKE) stop
	rm -rf storage/logs/*.log bootstrap/cache/*.php storage/framework/schedule-* storage/clockwork/*.json
	rm -rf storage/framework/cache/* storage/framework/sessions/* storage/framework/views/*.php
	rm -rf database/backups/*.gz

rollback: ##@development Rollback the previous database migration
	@echo "${GREEN}Rollback the database${RESET}"
	@docker-compose exec php ./artisan migrate:rollback

seed: #@development Seed the database
	@echo "${GREEN}Seed the database${RESET}"
	@docker-compose exec php ./artisan migrate:fresh --seed

lint: ##@tests PHP Parallel Lint
	@echo "${GREEN}PHP Parallel Lint${RESET}"
	@rm -rf bootstrap/cache/*.php
	@docker-compose run --no-deps --rm composer test:lint

lines: ##@tests PHP Lines of Code
	@echo "${GREEN}Lines of Code Statistics${RESET}"
	@docker-compose run --no-deps --rm composer test:loc

phpcs: ##@tests PHP Coding Standards (PSR-2)
	@echo "${GREEN}PHP Code Sniffer${RESET}"
	@docker-compose run --no-deps --rm composer test:phpcs

fix: ##@tests PHP Coding Standards Fixer
	@docker-compose run --no-deps --rm composer test:phpcs:fix

phpmd: ##@tests PHP Mess Detector
	@echo "${GREEN}PHP Mess Detector${RESET}"
	@if [ -f phpmd.xml ]; then docker-compose run --no-deps --rm composer vendor/bin/phpmd app text phpmd.xml; fi
	@if [ ! -f phpmd.xml ]; then docker-compose run --no-deps --rm composer test:phpmd; fi

coverage: ##@tests Test Coverage HTML
	@echo "${GREEN}All tests with coverage${RESET}"
	@docker-compose exec php vendor/bin/phpunit --coverage-php=storage/app/tmp/unit.cov --testsuite "Unit Tests" --log-junit=storage/app/tmp/unit.junit.xml --exclude-group slow
	@docker-compose exec php vendor/bin/phpunit --coverage-php=storage/app/tmp/slow.cov --testsuite "Unit Tests" --log-junit=storage/app/tmp/slow.junit.xml --exclude-group default
	@docker-compose exec php vendor/bin/phpunit --coverage-php=storage/app/tmp/integration.cov --log-junit=storage/app/tmp/integration.junit.xml --testsuite "Integration Tests"
	@docker-compose exec php vendor/bin/phpcov merge storage/app/tmp/ --html storage/app/tmp/coverage/ --clover storage/app/tmp/coverage.xml
	@docker-compose exec php vendor/bin/phpjunitmerge --names="*.junit.xml" storage/app/tmp/ storage/app/tmp/junit.xml
	@rm -f storage/app/tmp/*.cov storage/app/tmp/*.junit.xml

phpunit: ##@tests Unit Tests
	@echo "${GREEN}Unit tests${RESET}"
	@docker-compose run --no-deps --rm composer test:unit

integration: ##@tests Integration Tests
	@echo "${GREEN}Integration tests${RESET}"
	@docker-compose run --rm composer test:integration

quicktest: ##@shortcuts Runs fast tests; these exclude PHPMD, slow unit tests & integration tests
	@$(MAKE) lint
	@$(MAKE) phpcs

test: ##@shortcuts Runs most tests; but excludes integration tests
	@$(MAKE) quicktest
	@$(MAKE) phpunit
	@$(MAKE) phpmd

fulltest: ##@shortcuts Runs all tests
	@$(MAKE) quicktest
	@$(MAKE) phpunit
	@$(MAKE) integration
	@$(MAKE) phpmd

run: ##@docker Runs the containers
	@docker-compose up -d --remove-orphans

dev: ##@docker Runs the containers with dev options
	@docker-compose -f docker-compose.yml -f docker-compose.dev.yml up -d --remove-orphans

stop: ##@docker Stops the containers
	@docker-compose down

build: ##@docker Builds the application
	@$(MAKE) run
	@cp -f ./docker/laravel_env .env
	@$(MAKE) docker-install
	@$(MAKE) docker-migrate
	@sed -i "s/JWT_SECRET=changeme/JWT_SECRET=$(shell date +%s | sha256sum | base64 | head -c 32; echo)/g" .env
	@docker-compose exec php ./artisan key:generate --force
	@docker-compose exec php ./artisan deployer:create-user admin admin@example.com changeme --no-email

docker-migrate: ##@docker Runs the migrations inside the container
	@docker-compose exec php ./artisan migrate

docker-install:
	@docker-compose run -v $(COMPOSER_CACHE_DIR):/tmp/cache --rm composer install --optimize-autoloader --no-dev --prefer-dist --no-interaction --no-suggest --ignore-platform-reqs
	@docker-compose exec node npm install --production

docker-install-dev:
	@docker-compose run -v $(COMPOSER_CACHE_DIR):/tmp/cache --rm composer install --no-interaction --no-suggest --prefer-dist --no-suggest --ignore-platform-reqs
	@docker-compose exec node npm install

# --------------------------------------------------------- #
# ----- The targets below should not be shown in help ----- #
# --------------------------------------------------------- #

# Clean everything (cache, logs, compiled assets, dependencies, etc)
reset: clean
	rm -rf vendor/ node_modules/
	rm -rf storage/app/mirrors/* storage/app/tmp/* storage/app/public/*  storage/app/*.tar.gz
	rm -rf .env.prev _ide_helper_models.php _ide_helper.php .phpstorm.meta.php .php_cs.cache
	-rm database/database.sqlite
	-rm database/backups/*

## Generates helper files for IDEs
#ide:
#	php artisan clear-compiled
#	php artisan ide-helper:generate
#	php artisan ide-helper:meta
#	php artisan ide-helper:models --nowrite

# Update all dependencies (also git add lockfiles)
#update-deps: permissions
#	composer update --no-suggest --prefer-dist --no-scripts
#	rm package-lock.json
#	npm update
#	git add composer.lock package-lock.json

#release: test
#	@/usr/local/bin/create-release

HELP_FUN = %help; \
	while(<>) { push @{$$help{$$2 // 'options'}}, [$$1, $$3] \
	if /^([a-zA-Z\-]+)\s*:.*\#\#(?:@([a-zA-Z\-]+))?\s(.*)$$/ }; \
	for (sort keys %help) { \
	print "${WHITE}$$_${RESET}\n"; \
	for (@{$$help{$$_}}) { \
	$$sep = " " x (32 - length $$_->[0]); \
	print "  ${YELLOW}$$_->[0]${RESET}$$sep${GREEN}$$_->[1]${RESET}\n"; \
	}; \
	print "\n"; }

# Prints the help
help:
	@echo "\nUsage: make ${YELLOW}<target>${RESET}\n\nThe following targets are available:\n";
	@perl -e '$(HELP_FUN)' $(MAKEFILE_LIST)
