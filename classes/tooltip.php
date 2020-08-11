<?php

/**
 * WP Wiki Template
 * 
 * @since 1.0.0
 */

namespace bf\wpPedia;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

class tooltip {

	private $excerpt_length = 40;

	public function __construct() {

		add_action( 'wp_ajax_nopriv_wppedia_generate_tooltip', [ $this, '__generate_tooltip' ] );
		add_action( 'wp_ajax_wppedia_generate_tooltip', [ $this, '__generate_tooltip' ] );

	}

	function __generate_tooltip() {

		// Setup Post Data
		$post_id = $_POST['post_id'];
		$post = get_post( $post_id );

		echo $this->the_excerpt( $post );
		
		die;

	}

	/**
	 * Get the excerpt with fallback generated from the content
	 * 
	 * @param WP_Post|int $post - Post ID or object
	 * 
	 * @since 1.0.0
	 */
	private function get_the_excerpt( $post = null ) {

		$str = '';

    $post = get_post( $post );
    if ( empty( $post ) ) {
      return;
    }

		if ( ! has_excerpt( $post ) ) {

			// Get the Post Content (formatted)
			setup_postdata( $post );
			$str = get_the_content( null, false, $post );
			wp_reset_postdata( $post );

			// Check if Text is not empty
			if ( '' != $str && $str ) {

				// Add some filters to the text
				$str = strip_shortcodes( $str );
				$str = str_replace(']]&gt;', ']]&gt;', $str);

				// Get a formatted string
				$str = force_balance_tags( html_entity_decode( wp_trim_words( htmlentities( $str ), $this->excerpt_length, null ) ) );

			}

		} else {

			// If an excerpt was specified just add some p tags
			$str = wpautop( $post->post_excerpt );

		}

		return apply_filters( 'wppedia_tooltip_excerpt', $str );

	}

}
