<?php
/* Development */
define('SAVEQUERIES', true);
define('WP_DEBUG', true);
define('SCRIPT_DEBUG', true);

/* Changes to vanilla Bedrock below this line */

/* Disable outgoing mail */
function wp_mail(){}
