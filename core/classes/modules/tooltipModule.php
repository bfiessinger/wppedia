<?php

/**
 * WPPedia module Tooltips
 * 
 * @since 1.2.0
 */

namespace WPPedia\modules;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

class tooltipModule {

	private $excerpt_length = 40;

	public function __construct() {

		add_action( 'wp_ajax_nopriv_wppedia_generate_tooltip', [ $this, '__generate_tooltip' ] );
		add_action( 'wp_ajax_wppedia_generate_tooltip', [ $this, '__generate_tooltip' ] );

		// Add custom image size used in tooltips
		add_image_size( 'wppedia_tooltip_thumbnail', 320, 180, true );

	}

	/**
	 * Generate WPPedia Tooltip HTML
	 * 
	 * @since 1.0.0
	 */
	function __generate_tooltip() {

		// The Post ID delivered through the AJAX request
		$post_id = absint($_POST['post_id']);
		if (!$post_id || empty(get_post($post_id)))
			die;

		$this->tooltip_thumbnail($post_id);

		echo apply_filters( 'wppedia_tooltip_before_excerpt', '<div class="wppedia-tooltip-content">' );
		the_excerpt_wppedia( $post_id, $this->excerpt_length, true );
		echo apply_filters( 'wppedia_tooltip_after_excerpt', '</div>' );
		
		die;

	}

	/**
	 * Display the post thumbnail
	 * 
	 * @param WP_Post|int $post - Post ID or object
	 * 
	 * @return boolean - true if an image is available
	 * 
	 * @since 1.0.0
	 */
	private function tooltip_thumbnail( $post = null ) {

    $post = get_post( $post );
    if ( empty( $post ) )
      return;

		if ( ! has_post_thumbnail( $post ) )
			return;

		$thumbnail_id = get_post_thumbnail_id( $post );
		
		echo wp_get_attachment_image( $thumbnail_id, 'wppedia_tooltip_thumbnail', [ 'class' => 'wppedia-tooltip-image' ] );

		return true;

	}

}
