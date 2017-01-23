deps: permissions
	composer install --optimize-autoloader --no-dev --no-suggest --prefer-dist
	yarn install --production

dev-deps: permissions
	composer install --no-suggest --prefer-dist
	yarn install

test: lint phpcs phpdoccheck phpunit phpmd

build: dev-deps
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
	php vendor/bin/parallel-lint --exclude bootstrap/cache/ app/ database/ config/ resources/ tests/ public/ bootstrap/ artisan

permissions:
	chmod 777 storage/logs/ bootstrap/cache/
	chmod 777 storage/framework/cache/ storage/framework/sessions/ storage/framework/views/
	chmod 777 storage/app/mirrors/ storage/app/tmp/ storage/app/public/
	chmod 777 storage/clockwork/
	chmod 777 public/upload/ # This should be moved, laravel recommends storage/public

clean:
	rm -rf vendor/ node_modules/ bower_components/
	rm -rf storage/logs/*.log bootstrap/cache/*.php
	rm -rf storage/framework/cache/* storage/framework/sessions/* storage/framework/views/*.php
	rm -rf storage/framework/schedule-*
	rm -rf storage/clockwork/*.json
	rm -rf public/css/ public/fonts/ public/js/

reset: clean
	rm -rf public/build/
	rm -rf storage/app/mirrors/* storage/app/tmp/* storage/app/public/*
	rm -rf .env.prev
	rm -rf _ide_helper_models.php _ide_helper.php .phpstorm.meta.php
	rm -rf .php_cs.cache
