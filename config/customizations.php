<?php

/**

 Application customizations
 --------------------------
 Do not modify application.php; instead, put customizations here (or, if they
 are specific to an environment, in the appropriate environment file).

 **/


/**
 * Multisite
 */
define('WP_ALLOW_MULTISITE', 'true');
define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', true);
$base = '/';
define('DOMAIN_CURRENT_SITE', getenv('DOMAIN_CURRENT_SITE'));
define('PATH_CURRENT_SITE', '/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);

/**
 * Redirect nonexistent blogs
 */
define('NOBLOGREDIRECT', getenv('WP_HOME'));

/**
 * Akismet
 */
define('WPCOM_API_KEY', getenv('WPCOM_API_KEY'));

/**
 * MLA Member API
 */
define('CBOX_AUTH_API_URL', getenv('CBOX_AUTH_API_URL'));
define('CBOX_AUTH_API_KEY', getenv('CBOX_AUTH_API_KEY'));
define('CBOX_AUTH_API_SECRET', getenv('CBOX_AUTH_API_SECRET'));
define('CBOX_AUTH_DEBUG', false);
define('CBOX_AUTH_DEBUG_LOG', getenv('CBOX_AUTH_DEBUG_LOG'));

/**
 * SMTP settings
 */
define('GLOBAL_SMTP_FROM', getenv('GLOBAL_SMTP_FROM'));

/**
 * CBOX plugin management
 */
define('CBOX_OVERRIDE_PLUGINS', false);
