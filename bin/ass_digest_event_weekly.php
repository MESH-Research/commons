<?php

date_default_timezone_set( 'America/New_York' );

define( 'BPGES_DEBUG_LOG_PATH', '/srv/www/commons/logs/bpges.log' );
define( 'BPGES_DEBUG', true );

$_POST['type'] = 'sum';
$_POST['timestamp'] = date( 'Y-m-d H:i:s' );

bpges_trigger_digest( 'sum' );
