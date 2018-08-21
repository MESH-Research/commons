<?php

date_default_timezone_set( 'America/New_York' );

$_POST['type'] = 'sum';
$_POST['timestamp'] = date( 'Y-m-d H:i:s' );

bpges_trigger_digest( 'sum' );
