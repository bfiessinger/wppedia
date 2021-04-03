<?php

/**
 * Admin View
 * 
 * @since 1.0.0
 */

namespace bf\wpPedia;

use bf\wpPedia\helper;
use bf\wpPedia\options;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

class admin {

  protected function __clone() {}

  public function __construct() {

		// Add Text to the glossary archive page
		add_action( 'display_post_states', [ $this, 'wppedia_archive_post_state' ], 10, 2 );

  }

	/**
	 * Modify the posts state for the glossary Archive Page
	 * 
	 * @since 1.0.0
	 */
	function wppedia_archive_post_state( $post_states, $post ) {

		if( $post->ID == helper::getInstance()->get_option( options::$settings_general_page, 'wppedia_archive_page' ) ) {
			$post_states[] = __( 'Glossary page', 'wppedia' );
		}
	
		return $post_states;

	}

}
