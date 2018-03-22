<?php

/**
 * Set WP SAML Auth configuration options
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
