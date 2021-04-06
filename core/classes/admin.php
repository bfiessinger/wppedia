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

		// Add body class to WPPedia Admin Pages
		add_filter( 'admin_body_class', [ $this, 'wppedia_admin_body_class' ] );

  }

	/**
	 * Modify the posts state for the glossary Archive Page
	 * 
	 * @since 1.0.0
	 */
	function wppedia_archive_post_state( $post_states, $post ) {

		if( $post->ID == get_option('wppedia_frontpage', false) ) {
			$post_states[] = __( 'Glossary page', 'wppedia' );
		}
	
		return $post_states;

	}

	/**
	 * Get the current screen/page post type in admin
	 * 
	 * @since 1.0.0
	 */ 
	private function current_screen_post_type() {
		global $post, $typenow, $current_screen;
		if ($post && $post->post_type)
			return $post->post_type;
		elseif ($typenow)
			return $typenow;
		elseif ($current_screen && $current_screen->post_type)
			return $current_screen->post_type;
		elseif (isset($_REQUEST['post_type']))
			return sanitize_key($_REQUEST['post_type']);
		return null;
	}

	/**
	 * Add a body class to all WPPedia Admin pages
	 * 
	 * @since 1.0.0
	 */
	function wppedia_admin_body_class( $classes ) {
		$current_screen_pt = $this->current_screen_post_type();
		if (is_admin() && 'wppedia_term' == $current_screen_pt) {
			$classes .= ' wppedia-page';
		}
		return $classes;
	}

}
