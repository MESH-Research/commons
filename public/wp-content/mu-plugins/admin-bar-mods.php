<?php

/**
 * Remove the main site from the My Sites dropdown.
 */
function mla_remove_main_site_from_my_sites( $blogs ) {
	foreach( (array) $blogs as $bkey => $blog ) {
		if ( 1 == $blog->userblog_id ) {
			unset( $blogs[ $bkey ] );
		}
	}
	return array_values( $blogs );
}

add_action( 'get_blogs_of_user', 'mla_remove_main_site_from_my_sites' );
