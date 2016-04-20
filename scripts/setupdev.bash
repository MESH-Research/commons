#!/bin/bash
set -ex

# tmp file to disable extraneous output from php when running wp-cli
# http://wordpress.stackexchange.com/a/145313/66711
pre_php=/tmp/__pre.php
echo "<?php error_reporting( 0 ); define( 'WP_DEBUG', false );" > "$pre_php"

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

$wp plugin activate debug-bar
$wp plugin activate debug-bar-actions-and-filters-addon
$wp plugin activate wordpress-debug-bar-template-trace
$wp plugin activate user-switching
