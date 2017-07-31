#!/bin/bash

set -x

domain=$(hostname)
networks=("" "ajs." "aseees." "caa." "mla.")
path="/srv/www/commons/current/web/wp"
pre_php=/tmp/__pre.php; [[ -e "$pre_php" ]] || echo "<?php error_reporting( 0 ); define( 'WP_DEBUG', false );" > "$pre_php"
wp="/srv/www/commons/current/vendor/wp-cli/wp-cli/bin/wp"

# show help & bail if no arguments passed
if [[ -z "$*" ]]
then
        echo "usage: $0 [wp command]"
        echo "  e.g. $0 plugin activate debug-bar"
        exit 1
fi

for slug in "${networks[@]}"
do
        sudo -u www-data $wp --require="$pre_php" --url="$slug$domain" --path="$path" $*
done
