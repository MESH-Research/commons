#!/bin/bash
set -e

#
# Index (or optionally setup, then index) elasticpress content.
#
# To setup, pass the string "setup" as the first parameter to this script.
# e.g.
# bash bin/index_elasticpress.bash setup
#
# Otherwise, with no parameters, existing content is reindexed without deleting anything.
# e.g.
# bash bin/index_elasticpress.bash
#

wp="sudo -u www-data /usr/local/bin/wp --path=/srv/www/commons/current/web/wp --url=$(hostname)"
all_networks_wp=/home/ubuntu/all_networks_wp.bash

if [[ "$1" = "setup" ]]
then
  $all_networks_wp elasticpress index --setup
else
  $all_networks_wp elasticpress index
fi
$wp elasticpress-buddypress index_from_all_networks --post-type=humcore_deposit
$all_networks_wp elasticpress-buddypress index
