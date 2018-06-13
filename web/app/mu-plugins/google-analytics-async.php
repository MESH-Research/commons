<?php

/**
 * google-analytics-async tries to handle form submissions even when the form has nothing to do with that plugin's settings.
 * stop it, check if the form needs handling, call that ourselves if so. otherwise allow user to proceed without interruption
 */
function hcommons_prevent_gaa_submit_hijack() {
	global $google_analytics_async;

	remove_action( 'admin_init', array( $google_analytics_async, 'handle_page_requests' ) );

	if ( isset( $_SERVER['REQUEST_URI'] ) && false !== strpos( $_SERVER['REQUEST_URI'], 'page=google-analytics' ) ) {
		// code inside this block ripped straight from Google_Analytics_Async::handle_page_requests(),
		// since it tries to redirect to settings.php when we actually want options-general.php
		if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'submit_settings' ) ) {
			$google_analytics_async->save_options( array('track_settings' => $_POST) );
			wp_redirect( add_query_arg( array( 'page' => 'google-analytics', 'dmsg' => urlencode( __( 'Changes were saved!', $google_analytics_async->text_domain ) ) ), 'options-general.php' ) );
			exit;
		}
	}
}
//new version of GA+ gets a 404 saving setttings page. Removing this filter addresses that on mla commons.
//add_action( 'admin_init', 'hcommons_prevent_gaa_submit_hijack', 5 ); // before the original action has run, so we can cancel it
