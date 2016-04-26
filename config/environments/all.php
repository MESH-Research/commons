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
define('DOMAIN_CURRENT_SITE', getenv('WP_DOMAIN'));
define('PATH_CURRENT_SITE', '/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);

/**
 * Domain Mapping Plugin
define('SUNRISE', 'on');
define('PLUGINDIR', 'app/plugins');
 */

/**
 * Redirect nonexistent blogs
 */
define('NOBLOGREDIRECT', getenv('WP_HOME'));

/**
 * Akismet
 */
define('WPCOM_API_KEY', getenv('WPCOM_API_KEY'));

/**
 * Logging
 */
define('WP_LOGS_DIR', getenv('WP_LOGS_DIR'));

/**
 * MLA Member API
 */
define('CBOX_AUTH_API_URL', getenv('CBOX_AUTH_API_URL'));
define('CBOX_AUTH_API_KEY', getenv('CBOX_AUTH_API_KEY'));
define('CBOX_AUTH_API_SECRET', getenv('CBOX_AUTH_API_SECRET'));

/**
 * SMTP settings
 */
define('GLOBAL_SMTP_FROM', getenv('GLOBAL_SMTP_FROM'));

/**
 * CBOX plugin management
 */
define('CBOX_OVERRIDE_PLUGINS', false);

/**
 * Humanities CORE
 */
define('CORE_HTTP_DEBUG', getenv('CORE_HTTP_DEBUG'));
define('CORE_ERROR_LOG', getenv('CORE_ERROR_LOG'));
define('CORE_HUMCORE_NAMESPACE', getenv('CORE_HUMCORE_NAMESPACE'));
define('CORE_HUMCORE_TEMP_DIR', getenv('CORE_HUMCORE_TEMP_DIR'));
define('CORE_HUMCORE_COLLECTION_PID', getenv('CORE_HUMCORE_COLLECTION_PID'));
define('CORE_FEDORA_PROTOCOL', getenv('CORE_FEDORA_PROTOCOL'));
define('CORE_FEDORA_HOST', getenv('CORE_FEDORA_HOST'));
define('CORE_FEDORA_PORT', getenv('CORE_FEDORA_PORT'));
define('CORE_FEDORA_PATH', getenv('CORE_FEDORA_PATH'));
define('CORE_FEDORA_LOGIN', getenv('CORE_FEDORA_LOGIN'));
define('CORE_FEDORA_PASSWORD', getenv('CORE_FEDORA_PASSWORD'));
define('CORE_SOLR_PROTOCOL', getenv('CORE_SOLR_PROTOCOL'));
define('CORE_SOLR_HOST', getenv('CORE_SOLR_HOST'));
define('CORE_SOLR_PORT', getenv('CORE_SOLR_PORT'));
define('CORE_SOLR_PATH', getenv('CORE_SOLR_PATH'));
define('CORE_SOLR_CORE', getenv('CORE_SOLR_CORE'));
define('CORE_EZID_PROTOCOL', getenv('CORE_EZID_PROTOCOL'));
define('CORE_EZID_HOST', getenv('CORE_EZID_HOST'));
define('CORE_EZID_PORT', getenv('CORE_EZID_PORT'));
define('CORE_EZID_PATH', getenv('CORE_EZID_PATH'));
define('CORE_EZID_LOGIN', getenv('CORE_EZID_LOGIN'));
define('CORE_EZID_PASSWORD', getenv('CORE_EZID_PASSWORD'));
define('CORE_EZID_PREFIX', getenv('CORE_EZID_PREFIX'));
