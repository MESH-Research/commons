<?php

/**
 * BuddyPress Groups Forbidden Group Slugs
 * ---
 * Used for groups that have been redirected or slugs that we want to reserve.
 *
 * @package BuddyPress
 * @subpackage MLAForbiddenNames
 */

add_filter( 'groups_forbidden_names', 'mla_bp_groups_forbidden_names', 10, 1 );
 
function mla_bp_groups_forbidden_names( $forbidden_names ) {

	$mla_forbidden_group_slugs = array (
		'style',
	);
	
	return array_merge( $forbidden_names, $mla_forbidden_group_slugs );

}
