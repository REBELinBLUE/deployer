.PHONY: deps

test: lint phpcs phpdoccheck phpunit phpmd

build:
	composer install --no-suggest --prefer-dist
	yarn install
	gulp

deps:
	composer install -o --no-dev --no-suggest --prefer-dist
	yarn install --production

dev-deps:
	composer install --no-suggest --prefer-dist
	yarn install

phpcs:
	./vendor/bin/phpcs -n --standard=phpcs.xml

phpmd:
	./vendor/bin/phpmd app text phpmd.xml

phpunit:
	./vendor/bin/phpunit --no-coverage

phpdoccheck:
	./vendor/bin/phpdoccheck --directory=app

lint:
	./vendor/bin/parallel-lint app/ database/ config/ resources/ tests/ public/

clean:
	rm -rf ./{vendor,node_modules,bower_components}/
	rm -rf ./storage/logs/*.log
	rm -rf ./storage/framework/{cache,sessions,views}/*
	rm -rf ./public/{css,fonts,js
	rm -rf ./_ide_helper{_models,}.php
	rm -rf ./.phpstorm.meta.php
	rm -rf ./.php_cs.cache

reset: clean
	rm -rf ./public/{build}
	rm -rf ./storage/app/{mirrors,tmp}/*
