<?php

// Function for debugging. Not necessary on production.
if (!function_exists('_log')) {
	/**
	 * Just writes objects, arrays, and other useful things
	 * to the debug.log.
	 */
	function _log($message) {
		if (WP_DEBUG === true) {
			if (is_array($message) || is_object($message)) {
				error_log(print_r($message, true));
			} else {
				error_log($message);
			}
		}
	}
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
