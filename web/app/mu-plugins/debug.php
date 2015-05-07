<?php

// Function for debugging. Not necessary on production.
if (!function_exists('_log')) {
	/**
	 * Just writes objects, arrays, and other useful things
	 * to the debug.log.
	 */
	function _log( $message, $var = false ) {
		if ( WP_DEBUG === true ) {
			if ( ! $var ) { // no second param given. Assuming simple message. 
				if (is_array($message) || is_object($message)) {
					error_log(print_r($message, true));
				} else {
					error_log($message);
				}
			} else { // this is probably a "message" + var situation. 
				if (is_array($message) || is_object($message)) {
					error_log(print_r($message, true));
					if ( is_array( $var ) || is_object( $var ) ) { 
						error_log( print_r( $var, true ) );
					} else { 
						error_log( $var ); 
					} 	
				} else {
					error_log($message);
					if ( is_array( $var ) || is_object( $var ) ) { 
						error_log( print_r( $var, true ) );
					} else { 
						error_log( $var ); 
					} 	
				}
			} 
		}
	}
}

function _vlog( $var ) { 
	error_log( print_r( debug_backtrace(), true ) ); 
} 

/* Don't write PHP Strict Standards error messages to debug.log.
 * Because apparently everything everywhere is a violation of a PHP
 * Strict Standard.
 *
 * When not debugging, skip E_NOTICE and lower.
 */
if (WP_DEBUG === true) {
	error_reporting(E_ALL & ~E_STRICT);
} else {
	error_reporting(E_USER_ERROR);
}

?>
