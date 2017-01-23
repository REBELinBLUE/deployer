test: lint phpcs phpdoccheck phpunit phpmd loc

install: permissions
	composer install --optimize-autoloader --no-dev --no-suggest --prefer-dist
	yarn install --production

install-dev: permissions
	composer install --no-suggest --prefer-dist
	yarn install

update-deps: permissions
	composer update
	yarn upgrade
	git add composer.lock yarn.lock

build: install-dev
	gulp

phpcs:
	php vendor/bin/phpcs -n --standard=phpcs.xml

fix:
	php vendor/bin/php-cs-fixer --no-interaction fix

phpmd:
	php vendor/bin/phpmd app text phpmd.xml

phpunit:
	php vendor/bin/phpunit --no-coverage

phpunit-coverage:
	php vendor/bin/phpunit --coverage-clover=coverage.xml

phpdoccheck:
	php vendor/bin/phpdoccheck --directory=app

lint:
	rm -rf bootstrap/cache/*.php
	php vendor/bin/parallel-lint app/ database/ config/ resources/ tests/ public/ bootstrap/ artisan

loc:
	php vendor/bin/phploc --count-tests app/ database/ resources/ tests/

permissions:
	chmod 777 storage/logs/ bootstrap/cache/ storage/clockwork/
	chmod 777 storage/framework/cache/ storage/framework/sessions/ storage/framework/views/
	chmod 777 storage/app/mirrors/ storage/app/tmp/ storage/app/public/
	chmod 777 public/upload/ # This should be removed, laravel recommends storage/public

clean:
	rm -rf storage/logs/*.log bootstrap/cache/*.php storage/framework/schedule-* storage/clockwork/*.json
	rm -rf storage/framework/cache/* storage/framework/sessions/* storage/framework/views/*.php
	rm -rf public/css/ public/fonts/ public/js/

reset: clean
	rm -rf vendor/ node_modules/ bower_components/
	rm -rf public/build/ storage/app/mirrors/* storage/app/tmp/* storage/app/public/*
	rm -rf .env.prev _ide_helper_models.php _ide_helper.php .phpstorm.meta.php .php_cs.cache
