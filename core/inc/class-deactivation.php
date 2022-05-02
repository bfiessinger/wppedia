<?php

/**
 * Plugin Deactivation
 * 
 * @since 1.2.0
 */

namespace WPPedia;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

class deactivation {

	function __construct() {}

	public static function deactivate() {
		flush_rewrite_rules();
	}

}
