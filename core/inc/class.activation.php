<?php

/**
 * Plugin Activation
 * 
 * @since 1.0.0
 */

namespace bf\wpPedia;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

class activation {

	function __construct() {}

	public static function activate() {
		
    if ( ! get_option( 'wppedia_flush_rewrite_rules_flag' ) ) {
			add_option( 'wppedia_flush_rewrite_rules_flag', true );
		}
		
	}

}