<?php
/* Production */
ini_set('display_errors', 0);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', false);
define('DISALLOW_FILE_MODS', true); // this disables all file modifications including updates and update notifications

// Redis
$redis_server = array(
	'host'     => 'hc-prod-redis.gdrquz.0001.use1.cache.amazonaws.com',
	'port'     => 6379,
	//'auth'     => '12345',
	//'database' => 0, // Optionally use a specific numeric Redis database. Default is 0.
);
