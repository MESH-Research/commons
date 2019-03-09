<?php

/**
 * Disable large_network features (like removing pagination) on network users admin.
 */
function hcommons_wp_is_large_network( $is_large_network ) {
	if ( function_exists( 'get_current_screen' ) ) {
		$screen = get_current_screen();

		if ( is_a( $screen, 'WP_Screen' ) && 'users-network' === $screen->id ) {
			$is_large_network = false;
		}
	}

	return $is_large_network;
}
add_filter( 'wp_is_large_network', 'hcommons_wp_is_large_network' );
