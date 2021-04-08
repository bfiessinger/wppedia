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

		$default_options = [
			// Pages
			'wppedia_frontpage' => false,
			// Crosslinking
			'wppedia_feature_crosslinks' => true,
			'wppedia_crosslinks_prefer_single_words' => false,
			'wppedia_crosslinks_posttypes' => [
				\wppedia_get_post_type()
			],
			// Permalinks
			'wppedia_permalink_base_setting' => 'glossary',
			'wppedia_permalink_use_initial_character' => true,
			// Layout
			'wppedia_singular_use_templates' => true,
			'wppedia_archive_use_templates' => true,
			'wppedia_archive_show_navigation' => true,
			'wppedia_singular_show_navigation' => false,
			'wppedia_archive_show_searchbar' => true,
			'wppedia_singular_show_searchbar' => false,
			// Query
			'wppedia_posts_per_page' => 25
		];

		foreach ($default_options as $option_key => $option_value) {
			if (!get_option($option_key)) {
				add_option( $option_key, $option_value, '', false );
			}
		}		
	}

}
