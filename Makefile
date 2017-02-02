.DEFAULT_GOAL := help
.PHONY: help
.SILENT:

## Frontend build
build: install-dev localise
	@-rm -rf public/build
	gulp
	@rm -rf public/css public/fonts public/js

## Clean cache, logs and other temporary files
clean:
	rm -rf storage/logs/*.log bootstrap/cache/*.php storage/framework/schedule-* storage/clockwork/*.json
	rm -rf storage/framework/cache/* storage/framework/sessions/* storage/framework/views/*.php
	-rm -rf public/css/ public/fonts/ public/js/ # temporary storage of compiled assets

## PHP Coding Standards Fixer
fix:
	@php vendor/bin/php-cs-fixer --no-interaction fix

## Generates helper files for IDEs
ide:
	php artisan clear-compiled
	php artisan ide-helper:generate
	php artisan ide-helper:meta
	php artisan ide-helper:models --nowrite

## Install dependencies
install: permissions
	composer install --optimize-autoloader --no-dev --no-suggest --prefer-dist
	yarn install --production

## Install dev dependencies
install-dev: permissions
	composer install --no-suggest --prefer-dist
	yarn install

## PHP Parallel Lint
lint:
	@rm -rf bootstrap/cache/*.php
	@php vendor/bin/parallel-lint app/ database/ config/ resources/ tests/ public/ bootstrap/ artisan

## PHP Lines of Code
lines:
	@php vendor/bin/phploc --count-tests app/ database/ resources/ tests/

## Runs the artisan js localisation refresh command
localise:
	@php artisan js-localization:refresh

## Fix permissions
permissions:
	chmod 777 storage/logs/ bootstrap/cache/ storage/clockwork/
	chmod 777 storage/framework/cache/ storage/framework/sessions/ storage/framework/views/
	chmod 777 storage/app/mirrors/ storage/app/tmp/ storage/app/public/

## PHP Coding Standards (PSR-2)
phpcs:
	@php vendor/bin/phpcs -n --standard=phpcs.xml

## PHPDoc Checker
phpdoc-check:
	@php vendor/bin/phpdoccheck --directory=app

## PHP Mess Detector
phpmd:
	@php vendor/bin/phpmd app text phpmd.xml

## PHPUnit Tests
phpunit:
	@php vendor/bin/phpunit --no-coverage --testsuite "Unit Tests"

## PHPUnit Coverage
phpunit-coverage:
	@php vendor/bin/phpunit --coverage-clover=coverage.xml --coverage-text=/dev/null --testsuite "Unit Tests"

# Create release
release: test
	@/usr/local/bin/create-release

## Clean everything (cache, logs, compiled assets, dependencies, etc)
reset: clean
	rm -rf vendor/ node_modules/ bower_components/
	rm -rf public/build/ storage/app/mirrors/* storage/app/tmp/* storage/app/public/*  storage/app/*.tar.gz
	rm -rf .env.prev _ide_helper_models.php _ide_helper.php .phpstorm.meta.php .php_cs.cache
	-rm database/database.sqlite
	-rm database/backups/*
	-git checkout -- public/build/ 2> /dev/null # Exists on the release branch

## Install dependencies and runs tests
test: install-dev lint phpcs phpdoc-check phpunit #phpmd - Disabled for now

## Update all dependencies (also git add lockfiles)
update-deps: permissions
	composer update
	yarn upgrade
	git add composer.lock yarn.lock

coverage: phpunit-coverage

## Prints this help :D
help:
	@awk -v skip=1 \
		'/^##/ { sub(/^[#[:blank:]]*/, "", $$0); doc_h=$$0; doc=""; skip=0; next } \
		 skip  { next } \
		 /^#/  { doc=doc "\n" substr($$0, 2); next } \
		 /:/   { sub(/:.*/, "", $$0); printf "\033[34m%-30s\033[0m\033[1m%s\033[0m %s\n", $$0, doc_h, doc; skip=1 }' \
		$(MAKEFILE_LIST)
