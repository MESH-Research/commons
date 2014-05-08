<?php

/* 
 * Hide settings page (we don't want users changing their 
 * e-mail or password).
 */

function change_settings_subnav() {

	$args = array(
		'parent_slug' => 'settings',
		'screen_function' => 'bp_core_screen_notification_settings',
		'subnav_slug' => 'notifications'
	);

	bp_core_new_nav_default($args);

}

add_action('bp_setup_nav', 'change_settings_subnav', 5);


function remove_general_subnav() {
	global $bp;
	bp_core_remove_subnav_item($bp->settings->slug, 'general');
}

add_action( 'wp', 'remove_general_subnav', 2 );

?>
