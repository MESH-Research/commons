<?php
/**
 * intended to facilitate logging of daily & weekly digest test fire for review
 * run with wp eval-file
 * e.g. all_networks_wp.bash eval-file bin/log_digests.php
 */

// this makes digest dry run without sending anything
$_GET['sum'] = true;

// open new log file for writing
$filename = sprintf( '/srv/www/commons/logs/digest_%s_%s.html',
	date( 'Y_m_d' ),
	Humanities_Commons::$society_id
);
$fp = fopen( $filename, 'w' );

// fire test
ob_start();
echo "<h2>".__('DAILY DIGEST:','bp-ass')."</h2>";
ass_digest_fire( 'dig' );
echo "<h2 style='margin-top:150px'>".__('WEEKLY DIGEST:','bp-ass')."</h2>";
ass_digest_fire( 'sum' );

// log output
fwrite( $fp, ob_get_clean() );
fclose( $fp );
