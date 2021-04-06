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

		delete_option('wppedia_flush_rewrite_rules_flag');
		flush_rewrite_rules();
		
	}

}
