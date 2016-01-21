<?php
/**
 * Plugin Name: MLA Legacy Logger
 * Plugin URI:  https://github.com/mlaa/commons
 * Description: Soon-to-be-deprecated logger.
 * Version:     1.0.0
 * License:     CC BY-NC 4.0
 */

// Provide /dev/null legacy log function to prevent errors.
namespace {
	if ( ! function_exists( '_log' ) ) {
		function _log() {}
	}
}
