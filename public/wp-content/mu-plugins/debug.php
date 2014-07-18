<?php 
//debugging
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
?>
