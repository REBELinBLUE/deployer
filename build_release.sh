#!/usr/bin/env bash

git checkout master
git branch -D release
git stash
git pull
git checkout release
git merge --no-edit -q -X theirs master

# set version
sed -i 's/-dev//' VERSION

git add VERSION
git commit -m "Incrementing version"

git rm -rf public/build/*
rm -rf public/build/
npm install --production
composer install --no-dev -o
bower install
gulp --production

git add -f public/build/rev-manifest.json
git add -f public/build/css/*
git add -f public/build/js/*
git add -f public/build/*
git commit -am "Updating assets"
git push

git checkout master
git stash pop
