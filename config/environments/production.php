<?php
/* Production */
ini_set('display_errors', 0);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', false);
define('DISALLOW_FILE_MODS', true); // this disables all file modifications including updates and update notifications

ini_set('log_errors', 1);
ini_set('error_log', getenv('WP_LOGS_DIR') . '/debug.log');

define('SUNRISE', 'on');
define('PLUGINDIR', 'app/plugins');
