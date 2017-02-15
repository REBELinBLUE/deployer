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
	-@rm -rf public/css/ public/fonts/ public/js/ # temporary storage of compiled assets

## PHP Coding Standards Fixer
fix:
	@php vendor/bin/php-cs-fixer --no-interaction fix

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
	@echo "\033[32mPHP Parallel Lint\033[39m"
	@rm -rf bootstrap/cache/*.php
	@php vendor/bin/parallel-lint app/ database/ config/ resources/ tests/ public/ bootstrap/ artisan

## PHP Lines of Code
lines:
	@echo "\033[32mLines of Code Statistics\033[39m"
	@php vendor/bin/phploc --count-tests app/ database/ resources/ tests/

## Runs the artisan js localisation refresh command
localise:
	@php artisan js-localization:refresh

## Migrate the database
migrate:
	@echo "\033[32mMigrate the database\033[39m"
	@php artisan migrate

## Rollback the previous database migration
rollback:
	@echo "\033[32mRollback the database\033[39m"
	@php artisan migrate:rollback

## Fix permissions
permissions:
	chmod 777 storage/logs/ bootstrap/cache/ storage/clockwork/
	chmod 777 storage/framework/cache/ storage/framework/sessions/ storage/framework/views/
	chmod 777 storage/app/mirrors/ storage/app/tmp/ storage/app/public/

## PHP Coding Standards (PSR-2)
phpcs:
	@echo "\033[32mPHP Code Sniffer\033[39m"
	@php vendor/bin/phpcs --standard=phpcs.xml

## PHPDoc Checker
phpdoc-check:
	@echo "\033[32mPHPDocblock Checker\033[39m"
	@php vendor/bin/phpdoccheck --directory=app --files-per-line 60

## PHP Mess Detector
phpmd:
	@echo "\033[32mPHP Mess Detector\033[39m"
	@php vendor/bin/phpmd app text phpmd.xml

## PHP Copy/Paste Detector
phpcpd:
	@echo "\033[32mPHP Copy/Paste Detector\033[39m"
	@php vendor/bin/phpcpd --progress app/

## Dusk Browser Tests
dusk:
	@echo "\033[32mDusk\033[39m"
	@php artisan dusk

## Test Coverage
phpunit-coverage:
	@echo "\033[32mTests with Code Coverage\033[39m"
	@php vendor/bin/phpunit --coverage-clover=coverage.xml --coverage-text=/dev/null

## Unit Tests - Excluding slow model tests which touch the database
phpunit-fast:
	@echo "\033[32mFast unit tests\033[39m"
	@php vendor/bin/phpunit --no-coverage --testsuite "Unit Tests" --exclude-group slow

## Unit Tests
phpunit:
	@echo "\033[32mUnit tests\033[39m"
	@php vendor/bin/phpunit --no-coverage --testsuite "Unit Tests"

## Integration Tests
integration:
	@echo "\033[32mIntegration tests\033[39m"
	@php vendor/bin/phpunit --no-coverage --testsuite "Integration Tests"

## Runs fast tests; these exclude PHPMD, slow unit tests, integration & dusk tests
quicktest: install-dev lint phpcs phpdoc-check phpcpd phpunit-fast

## Runs most tests; but excludes integration & dusk tests
test: install-dev lint phpcs phpdoc-check phpunit phpcpd phpmd

## Runs all tests
fulltest: install-dev lint phpcs phpdoc-check phpunit integration phpcpd phpmd dusk

## Prints this help :D
help:
	@echo "\nUsage: make <target>\n\nThe following targets are available:\n";
	@awk -v skip=1 \
		'/^##/ { sub(/^[#[:blank:]]*/, "", $$0); doc_h=$$0; doc=""; skip=0; next } \
		 skip  { next } \
		 /^#/  { doc=doc "\n" substr($$0, 2); next } \
		 /:/   { sub(/:.*/, "", $$0); printf "\033[34m%-30s\033[0m\033[1m%s\033[0m %s\n", $$0, doc_h, doc; skip=1 }' \
		$(MAKEFILE_LIST)

# ----------------------------------------------------------------------------------------------------------- #
# ----- The targets below won't show in help because the descriptions only have 1 hash at the beginning ----- #
# ----------------------------------------------------------------------------------------------------------- #

# Clean everything (cache, logs, compiled assets, dependencies, etc)
reset: clean
	rm -rf vendor/ node_modules/ bower_components/
	rm -rf public/build/ storage/app/mirrors/* storage/app/tmp/* storage/app/public/*  storage/app/*.tar.gz
	rm -rf .env.prev _ide_helper_models.php _ide_helper.php .phpstorm.meta.php .php_cs.cache
	-rm database/database.sqlite
	-rm database/backups/*
	-git checkout -- public/build/ 2> /dev/null # Exists on the release branch

# Alias for phpunit-coverage
coverage: phpunit-coverage

# Seed the database
seed:
	@echo "\033[32mSeed the database\033[39m"
	@php artisan db:seed

# Generates helper files for IDEs
ide:
	php artisan clear-compiled
	php artisan ide-helper:generate
	php artisan ide-helper:meta
	php artisan ide-helper:models --nowrite

# Update all dependencies (also git add lockfiles)
update-deps: permissions
	composer update
	yarn upgrade
	git add composer.lock yarn.lock

# Create the .env file for Travis CI
ci:
	@cp -f $(TRAVIS_BUILD_DIR)/tests/.env.travis $(TRAVIS_BUILD_DIR)/.env
ifeq "$(DB)" "sqlite"
	@sed -i "s/DB_CONNECTION=mysql/DB_CONNECTION=sqlite/g" .env
	@sed -i 's/DB_DATABASE=deployer//g' .env
	@sed -i 's/DB_USERNAME=travis//g' .env
	@touch $(TRAVIS_BUILD_DIR)/database/database.sqlite
else ifeq "$(DB)" "pgsql"
	@sed -i "s/DB_CONNECTION=mysql/DB_CONNECTION=pgsql/g" .env
	@sed -i "s/DB_USERNAME=travis/DB_USERNAME=postgres/g" .env
	@psql -c 'CREATE DATABASE deployer;' -U postgres;
else
	@mysql -e 'CREATE DATABASE deployer;'
endif

# Run the PHPUnit tests for Travis CI
phpunit-ci:
ifeq "$(TRAVIS_PHP_VERSION)" "7.0"
	@$(MAKE) phpunit-coverage
else ifeq "$(DB)" "sqlite"
	@$(MAKE) phpunit
	@$(MAKE) integration
else
	@$(MAKE) phpunit-fast
endif

# Create release
release: test
	@/usr/local/bin/create-release
