<?php

/**

 Application customizations
 --------------------------
 Do not modify application.php; instead, put customizations here (or, if they
 are specific to an environment, in the appropriate environment file).

 **/

/**
 * Apache Proxy Setting
 */
$_SERVER['HTTPS']='on';
define('FORCE_SSL_ADMIN', true);


define( 'JETPACK_STAGING_MODE', true );

/**
 * Multisite
 */
define('WP_ALLOW_MULTISITE', 'true');
define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', true);
$base = '/';
//define('DOMAIN_CURRENT_SITE', getenv('WP_DOMAIN'));

// necessary to prevent redirect loops caused by session cookie confusion in wp >= 4.7
// see https://core.trac.wordpress.org/changeset/38619#file1
if ( isset( $_SERVER['HTTP_SHIB_SESSION_ID'] ) && ! empty( $_SERVER['HTTP_SHIB_SESSION_ID'] ) ) {
	define( 'COOKIEHASH', $_SERVER['HTTP_SHIB_SESSION_ID'] );
} else {
	define( 'COOKIEHASH', '' );
}
//define( 'COOKIE_DOMAIN', getenv( 'WP_DOMAIN' ) );

define('SUNRISE', 'on');
define('PLUGINDIR', 'app/plugins');

// all paths should be on the root to avoid cookies which are duplicates aside from path
define( 'COOKIEPATH', '/' );
define( 'ADMIN_COOKIE_PATH', '/' );
define( 'SITECOOKIEPATH',    '/' );

/* Because of customizer widget page error and shibboleth auto login, we will not set cookies as is usually done in wp-multi-network.
define( 'COOKIEHASH', md5( 'alpha.hcommons.org' ) );
define( 'COOKIE_DOMAIN', 'alpha.hcommons.org' );
define( 'ADMIN_COOKIE_PATH', '/' );
define( 'COOKIEPATH',        '/' );
define( 'SITECOOKIEPATH',    '/' );
define( 'TEST_COOKIE',        'wordpress_test_cookie' );
define( 'AUTH_COOKIE',        'wordpress_'          . COOKIEHASH );
define( 'USER_COOKIE',        'wordpressuser_'     . COOKIEHASH );
define( 'PASS_COOKIE',        'wordpresspass_'     . COOKIEHASH );
define( 'SECURE_AUTH_COOKIE', 'wordpress_sec_'      . COOKIEHASH );
define( 'LOGGED_IN_COOKIE',   'wordpress_logged_in' . COOKIEHASH );
*/

define('PATH_CURRENT_SITE', '/');

define('PRIMARY_NETWORK_ID', 1);
//define('SITE_ID_CURRENT_SITE', 1);
//define('BLOG_ID_CURRENT_SITE', 1);

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
 * BuddyPress Reply By Email
 */
define( 'BP_RBE_DEBUG_LOG_PATH', '/srv/www/commons/logs/bp-rbe.log');

/**
 * Akismet
 */
define('WPCOM_API_KEY', getenv('WPCOM_API_KEY'));

/**
 * Logging
 */
define('WP_LOGS_DIR', getenv('WP_LOGS_DIR'));

// W3 Total Cache
define( 'WP_CACHE', getenv( 'WP_CACHE' ) );

/**
 * Redis
 */
define('WP_CACHE_KEY_SALT', getenv('WP_CACHE_KEY_SALT'));

/**
 * Redis cache
 */
define('REDIS_HOST', getenv('REDIS_HOST'));       // wp-redis
define('WP_REDIS_HOST', getenv('WP_REDIS_HOST')); // redis-cache

/**
 * ElasticPress Elasticsearch
 */
define('EP_HOST', getenv('EP_HOST'));

/**
 * Humanities Commons
 */
define('HC_SITE_ID', getenv('HC_SITE_ID'));
define('HC_SITE_URL', getenv('HC_SITE_URL'));
define('AJS_SITE_URL', getenv('AJS_SITE_URL'));
define('ARLISNA_SITE_URL', getenv('ARLISNA_SITE_URL'));
define('ASEEES_SITE_URL', getenv('ASEEES_SITE_URL'));
define('CAA_SITE_URL', getenv('CAA_SITE_URL'));
define('MLA_SITE_URL', getenv('MLA_SITE_URL'));
define('MSU_SITE_URL', getenv('MSU_SITE_URL'));
define('SAH_SITE_URL', getenv('SAH_SITE_URL'));
define('UP_SITE_URL', getenv('UP_SITE_URL'));
define('REGISTRY_SERVER_URL', getenv('REGISTRY_SERVER_URL'));
define('SATOSA_SERVER_URL', getenv('SATOSA_SERVER_URL'));
define('HC_ENROLLMENT_URL', getenv('HC_ENROLLMENT_URL'));
define('AJS_ENROLLMENT_URL', getenv('AJS_ENROLLMENT_URL'));
define('ARLISNA_ENROLLMENT_URL', getenv('ARLISNA_ENROLLMENT_URL'));
define('ASEEES_ENROLLMENT_URL', getenv('ASEEES_ENROLLMENT_URL'));
define('CAA_ENROLLMENT_URL', getenv('CAA_ENROLLMENT_URL'));
define('MLA_ENROLLMENT_URL', getenv('MLA_ENROLLMENT_URL'));
define('MSU_ENROLLMENT_URL', getenv('MSU_ENROLLMENT_URL'));
define('SAH_ENROLLMENT_URL', getenv('SAH_ENROLLMENT_URL'));
define('UP_ENROLLMENT_URL', getenv('UP_ENROLLMENT_URL'));
define('HC_ACCOUNT_LINK_URL', getenv('HC_ACCOUNT_LINK_URL'));
define('AJS_ACCOUNT_LINK_URL', getenv('AJS_ACCOUNT_LINK_URL'));
define('ARLISNA_ACCOUNT_LINK_URL', getenv('ARLISNA_ACCOUNT_LINK_URL'));
define('ASEEES_ACCOUNT_LINK_URL', getenv('ASEEES_ACCOUNT_LINK_URL'));
define('CAA_ACCOUNT_LINK_URL', getenv('CAA_ACCOUNT_LINK_URL'));
define('MLA_ACCOUNT_LINK_URL', getenv('MLA_ACCOUNT_LINK_URL'));
define('MSU_ACCOUNT_LINK_URL', getenv('MSU_ACCOUNT_LINK_URL'));
define('SAH_ACCOUNT_LINK_URL', getenv('SAH_ACCOUNT_LINK_URL'));
define('UP_ACCOUNT_LINK_URL', getenv('UP_ACCOUNT_LINK_URL'));
define('HC_ORCID_USER_ACCOUNT_LINK_URL', getenv('HC_ORCID_USER_ACCOUNT_LINK_URL'));
define('AJS_ORCID_USER_ACCOUNT_LINK_URL', getenv('AJS_ORCID_USER_ACCOUNT_LINK_URL'));
define('ARLISNA_ORCID_USER_ACCOUNT_LINK_URL', getenv('ARLISNA_ORCID_USER_ACCOUNT_LINK_URL'));
define('ASEEES_ORCID_USER_ACCOUNT_LINK_URL', getenv('ASEEES_ORCID_USER_ACCOUNT_LINK_URL'));
define('CAA_ORCID_USER_ACCOUNT_LINK_URL', getenv('CAA_ORCID_USER_ACCOUNT_LINK_URL'));
define('MLA_ORCID_USER_ACCOUNT_LINK_URL', getenv('MLA_ORCID_USER_ACCOUNT_LINK_URL'));
define('MSU_ORCID_USER_ACCOUNT_LINK_URL', getenv('MSU_ORCID_USER_ACCOUNT_LINK_URL'));
define('SAH_ORCID_USER_ACCOUNT_LINK_URL', getenv('SAH_ORCID_USER_ACCOUNT_LINK_URL'));
define('UP_ORCID_USER_ACCOUNT_LINK_URL', getenv('UP_ORCID_USER_ACCOUNT_LINK_URL'));
define('GOOGLE_IDENTITY_PROVIDER', getenv('GOOGLE_IDENTITY_PROVIDER'));
define('TWITTER_IDENTITY_PROVIDER', getenv('TWITTER_IDENTITY_PROVIDER'));
define('HC_IDENTITY_PROVIDER', getenv('HC_IDENTITY_PROVIDER'));
define('MLA_IDENTITY_PROVIDER', getenv('MLA_IDENTITY_PROVIDER'));
define('GOOGLE_LOGIN_METHOD_SCOPE', getenv('GOOGLE_LOGIN_METHOD_SCOPE'));
define('TWITTER_LOGIN_METHOD_SCOPE', getenv('TWITTER_LOGIN_METHOD_SCOPE'));
define('HC_LOGIN_METHOD_SCOPE', getenv('HC_LOGIN_METHOD_SCOPE'));
define('MLA_LOGIN_METHOD_SCOPE', getenv('MLA_LOGIN_METHOD_SCOPE'));
define('HC_ROOT_BLOG_ID', getenv('HC_ROOT_BLOG_ID'));
define('AJS_ROOT_BLOG_ID', getenv('AJS_ROOT_BLOG_ID'));
define('ARLISNA_ROOT_BLOG_ID', getenv('ARLISNA_ROOT_BLOG_ID'));
define('ASEEES_ROOT_BLOG_ID', getenv('ASEEES_ROOT_BLOG_ID'));
define('CAA_ROOT_BLOG_ID', getenv('CAA_ROOT_BLOG_ID'));
define('MLA_ROOT_BLOG_ID', getenv('MLA_ROOT_BLOG_ID'));
define('MSU_ROOT_BLOG_ID', getenv('MSU_ROOT_BLOG_ID'));
define('SAH_ROOT_BLOG_ID', getenv('SAH_ROOT_BLOG_ID'));
define('UP_ROOT_BLOG_ID', getenv('UP_ROOT_BLOG_ID'));
define('GLOBAL_SUPER_ADMINS', getenv('GLOBAL_SUPER_ADMINS'));
define('GOOGLE_IDP_URL', getenv('GOOGLE_IDP_URL'));
define('TWITTER_IDP_URL', getenv('TWITTER_IDP_URL'));
define('MLA_IDP_URL', getenv('MLA_IDP_URL'));
define('HC_IDP_URL', getenv('HC_IDP_URL'));
define('REGISTRY_SP_URL', getenv('REGISTRY_SP_URL'));
define('HASTAC_SITE_URL', getenv('HASTAC_SITE_URL'));
define('HASTAC_ENROLLMENT_URL', getenv('HASTAC_ENROLLMENT_URL'));
define('HASTAC_ACCOUNT_LINK_URL', getenv('HASTAC_ACCOUNT_LINK_URL'));
define('HASTAC_ORCID_USER_ACCOUNT_LINK_URL', getenv('HASTAC_ORCID_USER_ACCOUNT_LINK_URL'));
define('HASTAC_ROOT_BLOG_ID', getenv('HASTAC_ROOT_BLOG_ID'));

/**
 * COMANAGE API
 */
define('COMANAGE_API_URL', getenv( 'COMANAGE_API_URL' ));
define('COMANAGE_API_USERNAME', getenv( 'COMANAGE_API_USERNAME' ));
define('COMANAGE_API_PASSWORD', getenv( 'COMANAGE_API_PASSWORD' ));

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
define('CBOX_OVERRIDE_PLUGINS', true); // help debug setup

/**
 * Plugin Monitor
 */
define( 'PLUGIN_MONITOR_ALERT_EMAILS', getenv( 'PLUGIN_MONITOR_ALERT_EMAILS' ) );

/**
 * BuddyPress
 */
define( 'BP_DEFAULT_COMPONENT', 'profile' ); // make "profile" default rather than "activity" for bp members component

/**
 * BuddyPress Reply By Email
 */
define( 'BP_RBE_SPARKPOST_WEBHOOK_TOKEN', getenv( 'BP_RBE_SPARKPOST_WEBHOOK_TOKEN' ) );

/**
 * Social Accounts
 */
define( 'TWITTER_USERNAME', getenv( 'TWITTER_USERNAME' ) );
define( 'FACEBOOK_APP_ID', getenv( 'FACEBOOK_APP_ID' ) );

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
define('CORE_DATACITE_PROTOCOL', getenv('CORE_DATACITE_PROTOCOL'));
define('CORE_DATACITE_HOST', getenv('CORE_DATACITE_HOST'));
define('CORE_DATACITE_PORT', getenv('CORE_DATACITE_PORT'));
define('CORE_DATACITE_PATH', getenv('CORE_DATACITE_PATH'));
define('CORE_DATACITE_LOGIN', getenv('CORE_DATACITE_LOGIN'));
define('CORE_DATACITE_PASSWORD', getenv('CORE_DATACITE_PASSWORD'));
define('CORE_DATACITE_PROXY', getenv('CORE_DATACITE_PROXY'));
define('CORE_DATACITE_PREFIX', getenv('CORE_DATACITE_PREFIX'));

