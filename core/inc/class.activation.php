<?php

/**
 * Plugin Activation
 * 
 * @since 1.2.0
 */

namespace WPPedia;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

class activation {

	function __construct() {}

	public static function activate() {
		add_option('wppedia_installed_version', wppedia_get_version(), '', false);
		add_option('wppedia_activated_at', time(), '', false);

    if ( ! get_option( 'wppedia_flush_rewrite_rules_flag' ) ) {
			add_option('wppedia_flush_rewrite_rules_flag', true);
		}
	}

}
