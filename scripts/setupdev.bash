#!/bin/bash
set -ex

# sync images etc.
sudo aws s3 sync s3://mla-backup/commons/www/sync/blogs.dir /srv/www/commons/shared/blogs.dir --delete --exclude '*pdf'
sudo chown -R www-data:www-data /srv/www/commons/shared/blogs.dir

# tmp file to disable extraneous output from php when running wp-cli
# http://wordpress.stackexchange.com/a/145313/66711
pre_php=/tmp/__pre.php
[[ -e "$pre_php" ]] || echo "<?php error_reporting( 0 ); define( 'WP_DEBUG', false );" > "$pre_php"

wp="wp --path=/srv/www/commons/current/web/wp --require=$pre_php"

aliases=()
aliases+=("wp='$wp'")

for alias in "${aliases[@]}"
do
	# add alias only if it doesn't already exist
	grep "$alias" ~/.bashrc || echo "alias $alias" >> ~/.bashrc
done

# disable xdebug for php cli to speed up composer
sudo sed -i '/^zend_extension/s/^/;/' /etc/php/7.0/cli/conf.d/20-xdebug.ini

$wp plugin deactivate --network wordpress-mu-domain-mapping

$wp plugin activate --network debug-bar
$wp plugin activate --network debug-bar-actions-and-filters-addon
$wp plugin activate --network wordpress-debug-bar-template-trace
$wp plugin activate --network user-switching
