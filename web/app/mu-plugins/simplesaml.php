<?php

/**
 * wordpress-mu-domain-mapping's sunrise.php defines COOKIE_DOMAIN for sites using mapped domains.
 * For all other sites, use the domain of the root blog on the root network.
 */
if ( ! defined( 'COOKIE_DOMAIN' ) ) {
	// TODO reconcile with PRIMARY_NETWORK_ID, which is still MLA
	$main_network_id = 2; // HC
	$main_network = get_network( $main_network_id );

	define( 'COOKIE_DOMAIN', $main_network->cookie_domain );
}

/**
 * Map attributes from urns to human-readable keys.
 *
 * <Attribute name="urn:oid:2.5.4.4" id="sn"/>
 * <Attribute name="urn:oid:2.5.4.42" id="givenName"/>
 * <Attribute name="urn:oid:0.9.2342.19200300.100.1.3" id="mail"/>
 * <Attribute name="urn:oid:2.16.840.1.113730.3.1.3" id="employeeNumber"/>
 * <Attribute name="urn:oid:1.3.6.1.4.1.5923.1.5.1.1" id="isMemberOf"/>
 * <Attribute name="urn:oid:2.5.4.12" id="title"/>
 * <Attribute name="urn:oid:2.5.4.10" id="o"/>
 * <Attribute name="urn:oid:0.9.2342.19200300.100.1.1" id="uid"/>
 * <Attribute name="urn:oid:1.3.6.1.4.1.5923.1.1.1.16" id="eduPersonOrcid"/>
 * <!-- for satosa -->
 * <Attribute name="urn:oid:1.3.6.1.4.1.49574.110.10" id="Meta-displayName"/>
 * <Attribute name="urn:oid:1.3.6.1.4.1.49574.110.11" id="Meta-organizationDisplayName"/>
 * <Attribute name="urn:oid:1.3.6.1.4.1.49574.110.12" id="Meta-organizationName"/>
 *
 * @param array $attributes SAML URN attributes.
 * @return array Mapped attributes.
 */
function hcommons_map_saml_attributes( array $attributes ) {
	$mapped = [];
	$mapped['HTTP_SN'] = implode( ';', $attributes['urn:oid:2.5.4.4'] );
	$mapped['HTTP_GIVENNAME'] = implode( ';', $attributes['urn:oid:2.5.4.42'] );
	$mapped['HTTP_MAIL'] = implode( ';', $attributes['urn:oid:0.9.2342.19200300.100.1.3'] );
	$mapped['HTTP_EMPLOYEENUMBER'] = implode( ';', $attributes['urn:oid:2.16.840.1.113730.3.1.3'] );
	$mapped['HTTP_ISMEMBEROF'] = implode( ';', $attributes['urn:oid:1.3.6.1.4.1.5923.1.5.1.1'] );
	$mapped['HTTP_TITLE'] = implode( ';', $attributes['urn:oid:2.5.4.12'] );
	$mapped['HTTP_O'] = implode( ';', $attributes['urn:oid:2.5.4.10'] );
	$mapped['HTTP_UID'] = implode( ';', $attributes['urn:oid:0.9.2342.19200300.100.1.1'] );
	$mapped['HTTP_EDUPERSONORCID'] = implode( ';', $attributes['urn:oid:1.3.6.1.4.1.5923.1.1.1.16'] );
	return $mapped;
}

/**
 * Set WP SAML Auth configuration options.
 *
 * @param mixed  $value       Configuration value.
 * @param string $option_name Configuration option name.
 */
function hcommons_wpsa_filter_option( $value, $option_name ) {
	$defaults = array(
		'connection_type' => 'simplesamlphp',
		'simplesamlphp_autoload' => '/srv/www/simplesamlphp/lib/_autoload.php',
		'auth_source' => 'default-sp',
		'auto_provision' => true,
		'permit_wp_login' => false,
		'get_user_by' => 'login',
		'user_login_attribute' => 'urn:oid:2.16.840.1.113730.3.1.3',
		'user_email_attribute' => 'urn:oid:0.9.2342.19200300.100.1.3',
		'display_name_attribute' => null,
		'first_name_attribute' => 'urn:oid:2.5.4.42',
		'last_name_attribute' => 'urn:oid:2.5.4.4',
		'default_role' => get_option( 'default_role' ),
	);
	$value = isset( $defaults[ $option_name ] ) ? $defaults[ $option_name ] : $value;
	return $value;
}
add_filter( 'wp_saml_auth_option', 'hcommons_wpsa_filter_option', 10, 2 );

/**
 * Automatically log out of SimpleSAML when logging out of WordPress.
 */
function hcommons_autologout() {
	if ( ! ( empty( $_GET['loggedout'] ) ) ) {
		// TODO correctly configure simplesaml logout instead of this hack
		// this isn't initialized until init 10...
		//$wsa = WP_SAML_Auth::get_instance()->get_provider();
		//$wsa->logout();
		setcookie( 'SimpleSAMLAuthToken', null, 1, '/', constant( 'COOKIEDOMAIN' ) );
		setcookie( 'SimpleSAML', null, 1, '/', constant( 'COOKIEDOMAIN' ) );
	}
}
// Before WP_SAML_Auth->action_init().
add_action( 'init', 'hcommons_autologout', 9 );

/**
 * Automatically log in to WordPress with an existing SimpleSAML session.
 */
function hcommons_autologin() {
	// Do nothing for WP_CLI.
	if ( defined( 'WP_CLI' ) && constant( 'WP_CLI' ) ) {
		return;
	}

	// Ignore existing sessions.
	if ( is_user_logged_in() ) {
		return;
	}

	// Don't interrupt login/logout in progress.
	// This condition is copied verbatim (and inverted) from WP_SAML_Auth->filter_authenticate().
	if ( ! ( empty( $_GET['loggedout'] ) || ( ! empty( $_GET['action'] ) && 'wp-saml-auth' === $_GET['action'] ) ) ) {
		return;
	}

	// Only proceed if there is a live SimpleSAML session.
	if ( empty( $_COOKIE['SimpleSAML'] ) ) {
		return;
	}

	// At this point, there is no WordPress session and we're not already authenticating, so try auto login.
	$result = WP_SAML_Auth::get_instance()->do_saml_authentication();

	// Success!
	if ( is_a( $result, 'WP_User' ) ) {
		return wp_set_current_user( $result->id );
	}

	// TODO: Proper error return.
	if ( is_wp_error( $result ) ) {
		error_log( $result->get_error_message() );
	}
}
// After WP_SAML_Auth->action_init().
add_action( 'init', 'hcommons_autologin', 11 );

/**
 * Stored mapped attributes in user meta on authentication.
 *
 * @param WP_User $user       The existing user object.
 * @param array   $attributes All attributes received from the SAML Response
 */
function hcommons_saml_attribute_map( $user, $attributes ) {
	$mapped = hcommons_map_saml_attributes( $attributes );
	update_user_meta( $user->ID, 'saml-attributes', $mapped );
}
//add_action( 'wp_saml_auth_existing_user_authenticated', 'hcommons_saml_attribute_map', 10, 2 );

/**
 * Preserve legacy references to $_SERVER shibboleth attributes by populating from SimpleSAML.
 */
function hcommons_set_saml_env() {
	$wsa = WP_SAML_Auth::get_instance()->get_provider();
	$attributes = $wsa->getAttributes();
	$mapped = hcommons_map_saml_attributes( $attributes );

	foreach ( $mapped as $k => $v ) {
		$_SERVER[ $k ] = $v;
	}

	if ( is_user_logged_in() ) {
		hcommons_maybe_set_user_role_for_site( wp_get_current_user() );
	}
}
// After hcommons_autologin().
add_action( 'init', 'hcommons_set_saml_env', 12 );
