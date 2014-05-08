<?php

/**
 * Modifications to the toolbar
 */

/**
 * Remove the main site from the My Sites dropdown, if the user doesn't have
 * sufficient creds
 *
 * It's a hack. Filter the output of get_blogs_of_user(). If the main site is
 * in the list, check the user's caps for that site. If there's nothing other
 * than 'blocked' and 'bbp_participant', it shouldn't show up.
 */
function mlac_remove_main_site_from_my_sites( $blogs ) {
	if ( 197 == get_current_user_id() ) {
		foreach( (array) $blogs as $bkey => $blog ) {
			if ( 1 == $blog->userblog_id ) {
				$caps = get_user_meta( get_current_user_id(), 'wp_capabilities', true );

				// Look for something other than 'subscriber' or 'bbp_participant'
				unset( $caps['subscriber'] );
				unset( $caps['bbp_participant'] );
				if ( empty( $caps ) ) {
					unset( $blogs[ $bkey ] );
				}
			}
		}
		$blogs = array_values( $blogs );
	}

	return $blogs;
}

/**
 * Glory glory, it's a super hack
 *
 * In order to make sure that the mlac_remove_main_site_from_my_sites() filter
 * does not run every time get_blogs_of_user() is run - we only want it to run
 * during admin bar propagation - we have to find a way to hook the filter just
 * before the admin bar is built, and to unhook immediately afterward. That's
 * why wp_admin_bar_class and admin_bar_init are used below.
 */
function mlac_hook_admin_bar_filters( $class ) {
	add_action( 'get_blogs_of_user', 'mlac_remove_main_site_from_my_sites' );
	return $class;
}
add_filter( 'wp_admin_bar_class', 'mlac_hook_admin_bar_filters' );

function mlac_unhook_admin_bar_filters() {
	remove_action( 'get_blogs_of_user', 'mlac_remove_main_site_from_my_sites' );
}
add_action( 'admin_bar_init', 'mlac_unhook_admin_bar_filters' );

