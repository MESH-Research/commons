<?php

date_default_timezone_set( 'America/New_York' );

define( 'BPGES_DEBUG', true );

$_POST['type'] = 'dig';
$_POST['timestamp'] = date( 'Y-m-d H:i:s' );

bpges_trigger_digest( 'dig' );
