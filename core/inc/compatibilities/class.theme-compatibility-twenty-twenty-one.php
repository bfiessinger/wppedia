<?php
/**
 * Twenty Twenty One support.
 *
 * @since 1.2.0
 */

namespace WPPedia;

defined( 'ABSPATH' ) || exit;

/**
 * WPPedia_Twenty_Twenty_One class.
 */
class WPPedia_Twenty_Twenty_One {

	/**
	 * Theme init.
	 */
	public static function init() {

		// This theme doesn't have a traditional sidebar.
		remove_action( 'wppedia_sidebar', 'wppedia_sidebar', 10 );

		// Twenty Twenty-One wraps the content before WPPedia does it.
		remove_action( 'wppedia_before_main_content', 'wppedia_wrapper_start', 10 );
		add_action( 'wppedia_before_main_content', [__CLASS__, 'wppedia_wrapper_start'], 10 );
		remove_action( 'wppedia_after_main_content', 'wppedia_wrapper_end', 20 );
		add_action( 'wppedia_after_main_content', [__CLASS__, 'wppedia_wrapper_end'], 20 );

		add_action('wp_enqueue_scripts', [ __CLASS__, 'unset_wppedia_content_width' ], 202 );

	}

	public static function wppedia_wrapper_start() {
		if (is_archive()) {
			echo '<div class="entry-content">';
		}
	}

	public static function wppedia_wrapper_end() {
		if (is_archive()) {
			echo '</div>';
		}
	}

	/**
	 * Unset the theme's content width.
	 * Twenty Twenty-One uses a custom structure to handle the content width.
	 *
	 * @since 1.3.0
	 */
	public static function unset_wppedia_content_width() {
		wppedia_remove_inline_style('wppedia-content-width');
	}

}

WPPedia_Twenty_Twenty_One::init();
