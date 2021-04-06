<?php

/**
 * Plugin Deactivation
 * 
 * @since 1.0.0
 */

namespace bf\wpPedia;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

class deactivation {

	function __construct() {}

	public static function deactivate() {
		flush_rewrite_rules();
	}

}
