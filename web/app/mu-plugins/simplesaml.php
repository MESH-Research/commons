<?php
/**
 * SimpleSAML-related functions.
 *
 * @package Commons
 */

/**
 * COOKIE_DOMAIN is defined by wordpress-mu-domain-mapping's sunrise.php for sites using mapped domains.
 * For all other sites, use the domain of the root blog on the root network.
 */
if ( ! defined( 'COOKIE_DOMAIN' ) ) {
	$main_network_id = 2; // TODO This is HC's ID. Reconcile with PRIMARY_NETWORK_ID, which is still MLA.

	if ( function_exists( 'network_exists' ) && network_exists( $main_network_id ) ) {
		$main_network = get_network( $main_network_id );

		if ( is_a( $main_network, 'WP_Network' ) ) {
			define( 'COOKIE_DOMAIN', $main_network->cookie_domain );
		}
	}
}

/**
 * Set WP SAML Auth configuration options.
 *
 * @param mixed  $value       Configuration value.
 * @param string $option_name Configuration option name.
 */
function hcommons_wpsa_filter_option( $value, string $option_name ) {
	$defaults = array(
		'connection_type'        => 'simplesamlphp',
		'simplesamlphp_autoload' => '/srv/www/simplesamlphp/lib/_autoload.php',
		'auth_source'            => 'default-sp',
		'auto_provision'         => true,
		'permit_wp_login'        => false,
		'get_user_by'            => 'login',
		'user_login_attribute'   => 'employeeNumber',
		'user_email_attribute'   => 'mail',
		'display_name_attribute' => 'cn',
		'first_name_attribute'   => 'givenName',
		'last_name_attribute'    => 'sn',
		'default_role'           => get_option( 'default_role' ),
	);
	$value    = isset( $defaults[ $option_name ] ) ? $defaults[ $option_name ] : $value;
	return $value;
}
add_filter( 'wp_saml_auth_option', 'hcommons_wpsa_filter_option', 10, 2 );

/**
 * Load WP_SAML_Auth early on bp_init so that BuddyPress has correct session data when loading.
 */
function hcommons_bootstrap_wp_saml_auth() {
	remove_action( 'init', [ WP_SAML_Auth::get_instance(), 'action_init' ] );
	WP_SAML_Auth::get_instance()->action_init();
}
add_action( 'bp_init', 'hcommons_bootstrap_wp_saml_auth', 2 );

/**
 * Populate $_SERVER with attributes from SimpleSAML for backwards compatibility.
 *
 * Use WP_SAML_Auth::get_instance()->get_provider()->getAttributes() instead of $_SERVER when possible.
 */
function hcommons_set_env_saml_attributes() {
	// This requires wp-saml-auth to be active.
	if ( ! class_exists( 'WP_SAML_Auth' ) ) {
		return;
	}

	$attributes = WP_SAML_Auth::get_instance()->get_provider()->getAttributes();

	if ( empty( $attributes ) ) {
		return;
	}

	// Most attributes are assigned literally: 'sn' => 'HTTP_SN'. The rest are mapped here.
	$map = [
		// These come from the IDP, not the AA, and are mostly redundant vs. AA attributes.
		'urn:oid:2.16.840.1.113730.3.1.241' => 'HTTP_IDP_DISPLAYNAME',
		'urn:oid:0.9.2342.19200300.100.1.3' => 'HTTP_IDP_MAIL',
		'urn:oid:1.3.6.1.4.1.5923.1.1.1.6'  => 'HTTP_IDP_UID',

		// These are provided by SATOSA but not Shibboleth.
		//TODO this are are supplied by shib and we shoud getv them. see https://hcommons.org/Shibboleth.sso/Session
		'urn:oid:1.3.6.1.4.1.49574.110.10'  => 'HTTP_META_DISPLAYNAME',
		'urn:oid:1.3.6.1.4.1.49574.110.11'  => 'HTTP_META_ORGANIZATIONDISPLAYNAME',
		'urn:oid:1.3.6.1.4.1.49574.110.12'  => 'HTTP_META_ORGANIZATIONNAME',
	];

	$mapped = [];

	foreach ( $attributes as $attribute => $value ) {
		if ( 1 === count( $value ) ) {
			// Legacy code expects single values to be strings, not arrays.
			$value = $value[0];
		} else {
			// Accommodate Humanities_Commons::hcommons_get_user_memberships().
			$value = implode( ';', $value );
		}

		if ( isset( $map[ $attribute ] ) ) {
			$mapped[ $map[ $attribute ] ] = $value;
		} else {
			$mapped[ 'HTTP_' . strtoupper( $attribute ) ] = $value;
		}
	}

	foreach ( $mapped as $k => $v ) {
		$_SERVER[ $k ] = $v;
	}

	if ( ! isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ) {
		$_SERVER['HTTP_X_FORWARDED_HOST'] = $_SERVER['HTTP_HOST'];
	}

	$_SERVER['HTTP_SHIB_SESSION_ID'] = $_COOKIE['SimpleSAML'];
	// TODO https://github.com/mlaa/humanities-commons/commit/764f6f41511a7813109c5b95a8b2fcfd444c6662
	$_SERVER['HTTP_SHIB_IDENTITY_PROVIDER'] = null;
};
// After WP_SAML_Auth->action_init().
add_action( 'bp_init', 'hcommons_set_env_saml_attributes', 4 );

/**
 * Automatically log in to WordPress with an existing SimpleSAML session.
 */
function hcommons_auto_login() {
	// Do nothing for WP_CLI.
	if ( defined( 'WP_CLI' ) && constant( 'WP_CLI' ) ) {
		return;
	}

	// This requires wp-saml-auth to be active.
	if ( ! class_exists( 'WP_SAML_Auth' ) ) {
		return;
	}

	// Do nothing without a SimpleSAML session.
	if ( ! WP_SAML_Auth::get_instance()->get_provider()->isAuthenticated() ) {
		return;
	}

	// Do nothing for existing sessions.
	if ( is_user_logged_in() ) {
		return;
	}

	// At this point, we know there's a SimpleSAML session but no WordPress session, so try authenticating.
	error_log( sprintf( '%s: authenticating token %s', __METHOD__, $_COOKIE['SimpleSAMLAuthToken'] ) );
	$result = WP_SAML_Auth::get_instance()->do_saml_authentication();

	if ( is_a( $result, 'WP_User' ) ) {
		error_log( sprintf( '%s: successfully authenticated %s', __METHOD__, $result->user_login ) );

		// Make sure this user is a member of the current site.
		$memberships      = Humanities_Commons::hcommons_get_user_memberships();
		$member_societies = (array) $memberships['societies'];
		if ( ! in_array( Humanities_Commons::$society_id, $member_societies ) ) {
			hcommons_write_error_log( 'info', '****CHECK_USER_SITE_MEMBERSHIP_FAIL****-' . var_export( $memberships['societies'], true ) . var_export( Humanities_Commons::$society_id, true ) . var_export( $result, true ) );
			error_log( '****CHECK_USER_SITE_MEMBERSHIP_FAIL****-' . var_export( $memberships['societies'], true ) . var_export( Humanities_Commons::$society_id, true ) . var_export( $result, true ) );
			error_log( sprintf( '%s: %s is not a member of %s', __METHOD__, $result->user_login, Humanities_Commons::$society_id ) );
			return;
		}

		// If we made it this far, we know this user is a member of the current site and has an existing session.
		wp_set_current_user( $result->ID );
	} else {
		if ( is_wp_error( $result ) ) {
			error_log( '%s: %s', __METHOD__, $result->get_error_message() );
		} else {
			error_log( sprintf( '%s: failed to authenticate', __METHOD__ ) );
		}
	}
}
// After hcommons_set_env_saml_attributes().
add_action( 'bp_init', 'hcommons_auto_login', 6 );
