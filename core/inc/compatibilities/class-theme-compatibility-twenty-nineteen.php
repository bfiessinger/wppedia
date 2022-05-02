<?php
/**
 * Twenty Nineteen support.
 *
 * @since 1.2.0
 */

namespace WPPedia;

defined( 'ABSPATH' ) || exit;

/**
 * WPPedia_Twenty_Nineteen class.
 */
class WPPedia_Twenty_Nineteen {

	/**
	 * Theme init.
	 */
	public static function init() {

		// This theme doesn't have a traditional sidebar.
		remove_action( 'wppedia_sidebar', 'wppedia_sidebar', 10 );

	}

}

WPPedia_Twenty_Nineteen::init();
