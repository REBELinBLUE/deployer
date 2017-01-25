.DEFAULT_GOAL := help
.PHONY: help
.SILENT:

## Install dependencies and runs tests
test: install-dev lint phpcs phpdoccheck phpunit #phpmd - Disabled for now

## Install dependencies
install: permissions
	composer install --optimize-autoloader --no-dev --no-suggest --prefer-dist
	yarn install --production

## Install dev dependencies
install-dev: permissions
	composer install --no-suggest --prefer-dist
	yarn install

## Update all dependencies (also git add lockfiles)
update-deps: permissions
	composer update
	yarn upgrade
	git add composer.lock yarn.lock

## Frontend build
build: install-dev localise
	gulp

## Runs the artisan js localisation refresh command
localise:
	@php artisan js-localization:refresh

# TBD
docs:
	@echo "Nothing here yet"

# Create release
release: test
	@/usr/local/bin/create-release

## PHP Coding Standards (CodeSniffer)
phpcs:
	@php vendor/bin/phpcs -n --standard=phpcs.xml

## PHP Coding Standards Fixer
fix:
	@php vendor/bin/php-cs-fixer --no-interaction fix

## PHP Mess Detector
phpmd:
	@php vendor/bin/phpmd app text phpmd.xml

## PHPUnit tests
phpunit:
	@php vendor/bin/phpunit --no-coverage

coverage: phpunit-coverage

## PHPUnit coverage
phpunit-coverage:
	@php vendor/bin/phpunit --coverage-clover=coverage.xml --coverage-text=/tmp/coverage.txt

## PHPDoccheck
phpdoccheck:
	@php vendor/bin/phpdoccheck --directory=app

## PHP Parallel Lint
lint:
	@rm -rf bootstrap/cache/*.php
	@php vendor/bin/parallel-lint app/ database/ config/ resources/ tests/ public/ bootstrap/ artisan

## PHP Lines of Code
loc:
	@php vendor/bin/phploc --count-tests app/ database/ resources/ tests/

## Fix permissions
permissions:
	chmod 777 storage/logs/ bootstrap/cache/ storage/clockwork/
	chmod 777 storage/framework/cache/ storage/framework/sessions/ storage/framework/views/
	chmod 777 storage/app/mirrors/ storage/app/tmp/ storage/app/public/
	chmod 777 public/upload/ # This should be removed, laravel recommends storage/public

## Clean cache, logs and compiled assets
clean:
	rm -rf storage/logs/*.log bootstrap/cache/*.php storage/framework/schedule-* storage/clockwork/*.json
	rm -rf storage/framework/cache/* storage/framework/sessions/* storage/framework/views/*.php
	rm -rf public/css/ public/fonts/ public/js/

## Clean everything (cache, logs, compiled assets, dependencies, etc)
reset: clean
	rm -rf vendor/ node_modules/ bower_components/
	rm -rf public/build/ storage/app/mirrors/* storage/app/tmp/* storage/app/public/*
	rm -rf .env.prev _ide_helper_models.php _ide_helper.php .phpstorm.meta.php .php_cs.cache

## Generates helper files for IDEs
ide:
	php artisan clear-compiled
	php artisan ide-helper:generate
	php artisan ide-helper:meta
	php artisan ide-helper:models --nowrite

## Prints this help :D
help:
	@awk -v skip=1 \
		'/^##/ { sub(/^[#[:blank:]]*/, "", $$0); doc_h=$$0; doc=""; skip=0; next } \
		 skip  { next } \
		 /^#/  { doc=doc "\n" substr($$0, 2); next } \
		 /:/   { sub(/:.*/, "", $$0); printf "\033[34m%-30s\033[0m\033[1m%s\033[0m %s\n\n", $$0, doc_h, doc; skip=1 }' \
		$(MAKEFILE_LIST)
