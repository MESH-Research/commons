<?php
/* Development */

/* Debug log */
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_DISPLAY', false );
define( 'SAVEQUERIES', true );
define( 'SCRIPT_DEBUG', true );
ini_set( 'log_errors', 1 );
ini_set( 'error_log', getenv( 'WP_LOGS_DIR' ) . '/debug.log' );

/* Changes to vanilla Bedrock below this line */

/* Disable outgoing mail */
function wp_mail(){}

// Redis
$redis_server = array(
	'host' => defined( 'REDIS_HOST' ) ? REDIS_HOST : 'localhost',
	'port' => 11211,
	// 'auth'     => '12345',
	// 'database' => 0, // Optionally use a specific numeric Redis database. Default is 0.
);
