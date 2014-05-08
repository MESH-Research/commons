<?php

/*
 * Remove misbehaving forums tab on profile pages.
 */

function remove_forums_nav() {
	bp_core_remove_nav_item('forums');
}
add_action( 'wp', 'remove_forums_nav', 3 );

/**
 * Removes Forums from Howdy dropdown
 */
function mlac_remove_forums_from_adminbar( $wp_admin_bar ) {
	$wp_admin_bar->remove_menu( 'my-account-forums' );
}
add_action( 'admin_bar_menu', 'mlac_remove_forums_from_adminbar', 9999 );

/* 
 * Removes forums from all sites except for MLA Commons. 
 * This is a temporary measure meant to solve the issue with 
 * forum post permissions described in this bug report: 
 * https://bbpress.trac.wordpress.org/ticket/2592#ticket
 * and discussed here in this forum post: 
 * http://commonsinabox.org/groups/help-support/forum/topic/scope-of-bbpress-plugin-and-associated-issues/
 * According to Boone, hopefully @r-a-y will add some functionality to CBOX
 * that allows for selective activation of plugins like bbPress. 
 * Until then, there's this hack. 
 * 
 */ 
function mla_remove_forums_from_child_sites($plugins) {
        if( is_multisite() AND ! is_main_site() ){ 
		unset($plugins['bbpress/bbpress.php']);
	}
	return $plugins;
}
add_filter('site_option_active_sitewide_plugins', 'mla_remove_forums_from_child_sites');
?>
