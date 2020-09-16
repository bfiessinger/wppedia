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

		if ( ! get_option( 'wppedia_settings_general' ) ) {
			
			$opt_defaults = [
				// Assets
				'wppedia_layout_enqueue-base-style' => 'on',
				'wppedia_layout_enqueue-char-nav-style' => 'on',
				'wppedia_layout_enqueue-searchform-style' => 'on',
				// Crosslinking
				'wppedia_crosslinking_active' => 'on',
				'wppedia_crosslinking_post-types' => [ post_type::getInstance()->post_type ],
			];

			add_option( 'wppedia_settings_general', $opt_defaults );

		}
		
	}

}
