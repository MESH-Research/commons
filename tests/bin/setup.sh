#!/bin/sh
host=localhost

if [ ! -f .env ]; then
  cp .env.travis .env
  host=travis.dev
fi

if [ ! -f wp-cli.phar ]; then
  curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
fi

mysql -e "DROP DATABASE IF EXISTS wordpress_tests;" -e "CREATE DATABASE wordpress_tests;" -uroot

php wp-cli.phar core multisite-install --quiet --path=web/wp --url=$host:8000 --title=WP --admin_user=test --admin_password=test --admin_email=user@example.com

php -t web -S $host:8000 >/dev/null 2>&1 &
