.DEFAULT_GOAL := help
.PHONY: help
.SILENT:

# Colours
GREEN  := $(shell tput -Txterm setaf 2)
WHITE  := $(shell tput -Txterm setaf 7)
YELLOW := $(shell tput -Txterm setaf 3)
RESET  := $(shell tput -Txterm sgr0)

build: ##@development Frontend build
	@$(MAKE) install-dev
	@-rm -rf public/build
	gulp --silent
	@rm -rf public/css public/fonts public/js

clean: ##@development Clean cache, logs and other temporary files
	rm -rf storage/logs/*.log bootstrap/cache/*.php storage/framework/schedule-* storage/clockwork/*.json
	rm -rf storage/framework/cache/* storage/framework/sessions/* storage/framework/views/*.php
	rm -rf database/backups/*.gz
	-@rm -rf public/css/ public/fonts/ public/js/ # temporary storage of compiled assets

fix: ##@development PHP Coding Standards Fixer
	@php vendor/bin/php-cs-fixer --no-interaction fix

install: ##@production Install dependencies
	@$(MAKE) permissions
	composer install --optimize-autoloader --no-dev --no-suggest --prefer-dist
	yarn install --production

install-dev: ##@development Install dev dependencies
	@$(MAKE) permissions
	composer install --no-suggest --prefer-dist
	yarn install

lint: ##@tests PHP Parallel Lint
	@echo "${GREEN}PHP Parallel Lint${RESET}"
	@rm -rf bootstrap/cache/*.php
	@php vendor/bin/parallel-lint app/ database/ config/ resources/ tests/ public/ bootstrap/ artisan

lines: ##@tests PHP Lines of Code
	@echo "${GREEN}Lines of Code Statistics${RESET}"
	@php vendor/bin/phploc --count-tests app/ database/ resources/ tests/

migrate: ##@production Migrate the database
	@echo "${GREEN}Migrate the database${RESET}"
	@php artisan migrate

rollback: ##@development Rollback the previous database migration
	@echo "${GREEN}Rollback the database${RESET}"
	@php artisan migrate:rollback

seed: #@development Seed the database
	@echo "${GREEN}Seed the database${RESET}"
	@php artisan db:seed

permissions: ##@production Fix permissions
	chmod 777 storage/logs/ bootstrap/cache/ storage/clockwork/
	chmod 777 storage/framework/cache/ storage/framework/sessions/ storage/framework/views/
	chmod 777 storage/app/mirrors/ storage/app/tmp/ storage/app/public/

phpcs: ##@tests PHP Coding Standards (PSR-2)
	@echo "${GREEN}PHP Code Sniffer${RESET}"
	@php vendor/bin/phpcs

phpdoc-check: ##@tests PHPDoc Checker
	@php vendor/bin/phpdoccheck --directory=app --files-per-line 60

phpstan: ##@tests PHPStan
	php vendor/bin/phpstan analyse -l 0 -c phpstan.neon app/

phpmd: ##@tests PHP Mess Detector
	@echo "${GREEN}PHP Mess Detector${RESET}"
	@if [ -f phpmd.xml ]; then php vendor/bin/phpmd app text phpmd.xml; fi
	@if [ ! -f phpmd.xml ]; then php vendor/bin/phpmd app text phpmd.xml.dist; fi

phpcpd: ##@tests PHP Copy/Paste Detector
	@echo "${GREEN}PHP Copy/Paste Detector${RESET}"
	@php vendor/bin/phpcpd --progress app/

dusk: ##@tests Dusk Browser Tests
	@echo "${GREEN}Dusk${RESET}"
	@php artisan dusk

coverage: ##@tests Test Coverage HTML
	@echo "${GREEN}All tests with coverage${RESET}"
	@phpdbg -qrr vendor/bin/phpunit --coverage-text=/dev/null --coverage-php=storage/app/tmp/unit.cov \
			--testsuite "Unit Tests" --log-junit=storage/app/tmp/unit.junit.xml --exclude-group slow
	@phpdbg -qrr vendor/bin/phpunit --coverage-text=/dev/null --coverage-php=storage/app/tmp/slow.cov \
			--testsuite "Unit Tests" --log-junit=storage/app/tmp/slow.junit.xml --exclude-group default
	@phpdbg -qrr vendor/bin/phpunit --coverage-text=/dev/null --coverage-php=storage/app/tmp/integration.cov \
			--log-junit=storage/app/tmp/integration.junit.xml --testsuite "Integration Tests"
	@phpdbg -qrr vendor/bin/phpcov merge storage/app/tmp/ \
			--html storage/app/tmp/coverage/ --clover storage/app/tmp/coverage.xml
	@php vendor/bin/phpjunitmerge --names="*.junit.xml" storage/app/tmp/ storage/app/tmp/junit.xml
	@rm -f storage/app/tmp/*.cov storage/app/tmp/*.junit.xml

phpunit: ##@tests Unit Tests
	@echo "${GREEN}Unit tests${RESET}"
	@php vendor/bin/phpunit --no-coverage --testsuite "Unit Tests"

integration: ##@tests Integration Tests
	@echo "${GREEN}Integration tests${RESET}"
	@php vendor/bin/phpunit --no-coverage --testsuite "Integration Tests"

quicktest: ##@shortcuts Runs fast tests; these exclude PHPMD, slow unit tests, integration & dusk tests
	@$(MAKE) install-dev
	@$(MAKE) lint
	@$(MAKE) phpcs
	@$(MAKE) phpdoc-check
	@$(MAKE) phpcpd

test: ##@shortcuts Runs most tests; but excludes integration & dusk tests
	@$(MAKE) install-dev
	@$(MAKE) lint
	@$(MAKE) phpcs
	@$(MAKE) phpdoc-check
	@$(MAKE) phpunit
	@$(MAKE) phpcpd
	@$(MAKE) phpmd
	@$(MAKE) phpstan

fulltest: ##@shortcuts Runs all tests
	@$(MAKE) lint
	@$(MAKE) phpcs
	@$(MAKE) phpdoc-check
	@$(MAKE) phpunit
	@$(MAKE) integration
	@$(MAKE) phpcpd
	@$(MAKE) phpstan
	@$(MAKE) phpmd
	@$(MAKE) dusk

# ----------------------------------------------------------------------------------------------------------- #
# ----- The targets below won't show in help because the descriptions only have 1 hash at the beginning ----- #
# ----------------------------------------------------------------------------------------------------------- #

# Clean everything (cache, logs, compiled assets, dependencies, etc)
reset: clean
	rm -rf vendor/ node_modules/ bower_components/
	rm -rf pstorage/app/mirrors/* storage/app/tmp/* storage/app/public/*  storage/app/*.tar.gz
	rm -rf .env.prev _ide_helper_models.php _ide_helper.php .phpstorm.meta.php .php_cs.cache
	-rm database/database.sqlite
	-rm database/backups/*
	-rm .phpunit-cas.db

# Generates helper files for IDEs
ide:
	php artisan clear-compiled
	php artisan ide-helper:generate
	php artisan ide-helper:meta
	php artisan ide-helper:models --nowrite

# Update all dependencies (also git add lockfiles)
update-deps: permissions
	composer update --no-suggest --prefer-dist
	yarn upgrade
	git add composer.lock yarn.lock

# Create release
release: test
	@/usr/local/bin/create-release

# Create the databases for Travis CI
ifeq "$(DB)" "sqlite"
travis:
	@sed -i 's/DB_CONNECTION=mysql/DB_CONNECTION=sqlite/g' .env
	@sed -i 's/DB_DATABASE=deployer//g' .env
	@sed -i 's/DB_USERNAME=travis//g' .env
	@touch $(TRAVIS_BUILD_DIR)/database/database.sqlite
else ifeq "$(DB)" "pgsql"
travis:
	@sed -i 's/DB_CONNECTION=mysql/DB_CONNECTION=pgsql/g' .env
	@sed -i 's/DB_USERNAME=travis/DB_USERNAME=postgres/g' .env
	@psql -c 'CREATE DATABASE deployer;' -U postgres;
else
travis:
	@mysql -e 'CREATE DATABASE deployer;'
endif

# PHPUnit for Travis
ifeq "$(TRAVIS_PHP_VERSION)" "7.1.0"
phpunit-ci: coverage
else
phpunit-ci: phpunit integration
endif

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
