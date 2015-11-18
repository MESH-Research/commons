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

/**
 * Humanities CORE
 */
define('HUMCORE_NAMESPACE', getenv('CORE_HUMCORE_NAMESPACE'));
define('HUMCORE_TEMP_DIR', getenv('CORE_HUMCORE_TEMP_DIR'));
define('HUMCORE_COLLECTION_PID', getenv('CORE_HUMCORE_COLLECTION_PID'));
define('FEDORA_PROTOCOL', getenv('CORE_FEDORA_PROTOCOL'));
define('FEDORA_HOST', getenv('CORE_FEDORA_HOST'));
define('FEDORA_PORT', getenv('CORE_FEDORA_PORT'));
define('FEDORA_PATH', getenv('CORE_FEDORA_PATH'));
define('FEDORA_LOGIN', getenv('CORE_FEDORA_LOGIN'));
define('FEDORA_PASSWORD', getenv('CORE_FEDORA_PASSWORD'));
define('SOLR_PROTOCOL', getenv('CORE_SOLR_PROTOCOL'));
define('SOLR_HOST', getenv('CORE_SOLR_HOST'));
define('SOLR_PORT', getenv('CORE_SOLR_PORT'));
define('SOLR_PATH', getenv('CORE_SOLR_PATH'));
define('SOLR_CORE', getenv('CORE_SOLR_CORE'));
define('EZID_PROTOCOL', getenv('CORE_EZID_PROTOCOL'));
define('EZID_HOST', getenv('CORE_EZID_HOST'));
define('EZID_PORT', getenv('CORE_EZID_PORT'));
define('EZID_PATH', getenv('CORE_EZID_PATH'));
define('EZID_LOGIN', getenv('CORE_EZID_LOGIN'));
define('EZID_PASSWORD', getenv('CORE_EZID_PASSWORD'));
define('EZID_PREFIX', getenv('CORE_EZID_PREFIX'));
