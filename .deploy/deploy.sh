#!/bin/bash

exec > /root/webhooks/iwgb-arms/output.log 2>&1

git fetch --all
git checkout --force "origin/master"

rsync -a . /var/www/iwgb.org.uk --delete --exclude .git --exclude .db --exclude .deploy --exclude vendor

cd /var/repo/iwgb.org.uk-static
rsync -a . /var/www/iwgb.org.uk

cd /var/repo/iwgb-config
rsync -a . /var/www/iwgb.org.uk/public/config --exclude .git

cd /var/www/iwgb.org.uk
export COMPOSER_HOME=/usr/local/bin
composer install
composer update
mkdir var
mkdir var/doctrine
mkdir var/upload
chmod -R 777 var

cd /var/www/iwgb.org.uk/public/css
sass --update --no-cache --style compressed .:.

cd /var/www/iwgb.org.uk/public/legacy/css
sass --update --no-cache --style compressed .:.