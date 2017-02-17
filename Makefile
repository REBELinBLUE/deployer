.DEFAULT_GOAL := help
.PHONY: help
.SILENT:

build: ##@development Frontend build
build: install-dev
	@-rm -rf public/build
	gulp --silent
	@rm -rf public/css public/fonts public/js

clean: ##@development Clean cache, logs and other temporary files
	rm -rf storage/logs/*.log bootstrap/cache/*.php storage/framework/schedule-* storage/clockwork/*.json
	rm -rf storage/framework/cache/* storage/framework/sessions/* storage/framework/views/*.php
	-@rm -rf public/css/ public/fonts/ public/js/ # temporary storage of compiled assets

fix: ##@development PHP Coding Standards Fixer
	@php vendor/bin/php-cs-fixer --no-interaction fix

install: ##@production Install dependencies
install: permissions
	composer install --optimize-autoloader --no-dev --no-suggest --prefer-dist
	yarn install --production

install-dev: ##@development Install dev dependencies
install-dev: permissions
	composer install --no-suggest --prefer-dist
	yarn install

lint: ##@tests PHP Parallel Lint
	@echo "\033[32mPHP Parallel Lint\033[39m"
	@rm -rf bootstrap/cache/*.php
	@php vendor/bin/parallel-lint app/ database/ config/ resources/ tests/ public/ bootstrap/ artisan

lines: ##@tests PHP Lines of Code
	@echo "\033[32mLines of Code Statistics\033[39m"
	@php vendor/bin/phploc --count-tests app/ database/ resources/ tests/

migrate: ##@production Migrate the database
	@echo "\033[32mMigrate the database\033[39m"
	@php artisan migrate

rollback: ##@development Rollback the previous database migration
	@echo "\033[32mRollback the database\033[39m"
	@php artisan migrate:rollback

seed: #@development Seed the database
	@echo "\033[32mSeed the database\033[39m"
	@php artisan db:seed

permissions: ##@production Fix permissions
	chmod 777 storage/logs/ bootstrap/cache/ storage/clockwork/
	chmod 777 storage/framework/cache/ storage/framework/sessions/ storage/framework/views/
	chmod 777 storage/app/mirrors/ storage/app/tmp/ storage/app/public/

phpcs: ##@tests PHP Coding Standards (PSR-2)
	@echo "\033[32mPHP Code Sniffer\033[39m"
	@php vendor/bin/phpcs

phpdoc-check: ##@tests PHPDoc Checker
	@php vendor/bin/phpdoccheck --directory=app --files-per-line 60

phpmd: ##@tests PHP Mess Detector
	@echo "\033[32mPHP Mess Detector\033[39m"
	@if [ -f phpmd.xml ]; then php vendor/bin/phpmd app text phpmd.xml; fi
	@if [ ! -f phpmd.xml ]; then php vendor/bin/phpmd app text phpmd.xml.dist; fi

phpcpd: ##@tests PHP Copy/Paste Detector
	@echo "\033[32mPHP Copy/Paste Detector\033[39m"
	@php vendor/bin/phpcpd --progress app/

dusk: ##@tests Dusk Browser Tests
	@echo "\033[32mDusk\033[39m"
	@php artisan dusk

coverage: ##@tests Test Coverage HTML
	@echo "\033[32mAll tests with coverage\033[39m"
	@mkdir -p tmp/
	@php vendor/bin/phpunit --coverage-php=tmp/unit.cov --testsuite "Unit Tests" --exclude-group slow
	@php vendor/bin/phpunit --coverage-php=tmp/slow.cov --testsuite "Unit Tests" --exclude-group default
	@php vendor/bin/phpunit --coverage-php=tmp/integration.cov --testsuite "Integration Tests"
	@php vendor/bin/phpcov merge tmp/ --html storage/app/tmp/coverage/
	@rm -rf tmp/

phpunit-fast: ##@tests Unit Tests - Excluding slow model tests which touch the database
	@echo "\033[32mFast unit tests\033[39m"
	@php vendor/bin/phpunit --no-coverage --testsuite "Unit Tests" --exclude-group slow

phpunit: ##@tests Unit Tests
	@echo "\033[32mUnit tests\033[39m"
	@php vendor/bin/phpunit --no-coverage --testsuite "Unit Tests"

integration: ##@tests Integration Tests
	@echo "\033[32mIntegration tests\033[39m"
	@php vendor/bin/phpunit --no-coverage --testsuite "Integration Tests"

quicktest: ##@shortcuts Runs fast tests; these exclude PHPMD, slow unit tests, integration & dusk tests
quicktest: install-dev lint phpcs phpdoc-check phpcpd phpunit-fast

test: ##@shortcuts Runs most tests; but excludes integration & dusk tests
test: install-dev lint phpcs phpdoc-check phpunit phpcpd phpmd

fulltest: ##@shortcuts Runs all tests
fulltest: build lint phpcs phpdoc-check phpunit integration phpcpd phpmd dusk

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
	-rm .phpunit-cas.db
	-git checkout -- public/build/ 2> /dev/null # Exists on the release branch

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
	@echo "\033[32mFast Unit Tests with coverage\033[39m"
	@php vendor/bin/phpunit --coverage-php=tmp/unit.cov --testsuite "Unit Tests" --exclude-group slow
	@echo "\033[32mSlow Unit Tests with coverage\033[39m"
	@php vendor/bin/phpunit --coverage-php=tmp/slow.cov --testsuite "Unit Tests" --exclude-group default
	@php vendor/bin/phpunit --coverage-php=tmp/integration.cov --testsuite "Integration Tests"
	@echo "\033[32mMerging coverage\033[39m"
	@php vendor/bin/phpcov merge tmp/ --clover coverage.xml
else ifeq "$(DB)" "sqlite"
	@$(MAKE) phpunit
	@$(MAKE) integration
else
	@$(MAKE) phpunit-fast
endif

# Create release
release: test
	@/usr/local/bin/create-release

# Colors
GREEN  := $(shell tput -Txterm setaf 2)
WHITE  := $(shell tput -Txterm setaf 7)
YELLOW := $(shell tput -Txterm setaf 3)
RESET  := $(shell tput -Txterm sgr0)

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
	# @awk -v skip=1 \
	# 	'/^##/ { sub(/^[#[:blank:]]*/, "", $$0); doc_h=$$0; doc=""; skip=0; next } \
	# 	 skip  { next } \
	# 	 /^#/  { doc=doc "\n" substr($$0, 2); next } \
	# 	 /:/   { sub(/:.*/, "", $$0); printf "\033[34m%-30s\033[0m\033[1m%s\033[0m %s\n", $$0, doc_h, doc; skip=1 }' \
	# 	$(MAKEFILE_LIST)
	@echo "\nUsage: make ${YELLOW}<target>${RESET}\n\nThe following targets are available:\n";
	@perl -e '$(HELP_FUN)' $(MAKEFILE_LIST)

