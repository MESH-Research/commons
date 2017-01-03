<?php
/* Staging */
define('WP_DEBUG', true);
define('WP_DEBUG_DISPLAY', false);
define('SAVEQUERIES', true);
define('SCRIPT_DEBUG', true);
ini_set('log_errors', 1);
ini_set('error_log', getenv('WP_LOGS_DIR') . '/debug.log');

/* Changes to vanilla Bedrock below this line */

/* disabled due to self-signed ssl not working with cloudfront
define('SUNRISE', 'on');
define('PLUGINDIR', 'app/plugins');
 */

/* Disable outgoing mail */
// testing sparkpost
//function wp_mail(){}
